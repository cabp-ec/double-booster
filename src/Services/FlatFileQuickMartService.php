<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Transactions\QuickMartTransaction;

class FlatFileQuickMartService extends BaseService
{
    const DETAIL_INDEXES = ['quantity', 'regularPrice', 'memberPrice', 'taxStatus'];
    const TAX_STATUS_EXEMPT = 'tax-exempt';
    const TAX_STATUS_TAXABLE = 'taxable';
    const TAX_STATUSSES = [self::TAX_STATUS_EXEMPT, self::TAX_STATUS_TAXABLE];

    private QuickMartTransaction $currentTransaction;

    public function __construct(
        private FileStorageService $fileStorageService,
        private FlatFileCrmService $fileCrmService,
        protected array            $resourcePath,
        protected array            $config
    )
    {
        parent::__construct($config);
    }

    /**
     * Milk: 5, #.75, $3.50, Tax-Exempt
     * @param string $line
     * @return array
     */
    private function transformInventoryLine(string $line): array
    {
        $productDetail = explode($this->config['productDetailDelimiter'], $line);
        $productName = trim($productDetail[0]);
        $detailRaw = explode($this->config['detailItemDelimiter'], trim($productDetail[1]));
        $detail = [];

        foreach ($detailRaw as $index => $detailItem) {
            $detail[self::DETAIL_INDEXES[$index]] = strtolower(trim($detailItem));
        }

        return array_merge_recursive(['name' => $productName], $detail);
    }

    public function isCustomer(string $email): bool
    {
        // TODO: implement
        return false;
    }

    /**
     * Get the whole inventory
     *
     * @return array
     */
    public function getInventory(): array
    {
        $path = $this->resourcePath['resources']['input'] . DIRECTORY_SEPARATOR;
        $path .= $this->config['files']['inventory'];
        $lines = $this->fileStorageService->getLines($path);
        $output = [];

        foreach ($lines as $index => $line) {
            $id = $index + 1;

            $output[$id] = array_merge_recursive(
                ['id' => $id],
                $this->transformInventoryLine($line)
            );
        }

        return $output;
    }

    /**
     * Add products to the cart
     *
     * @param array $products
     * @return bool
     */
    public function addProductToCart(array $products): bool
    {
        $cart = $this->session->cart;

        foreach ($products as $product) {
            $id = (int)array_keys($product)[0];
            $qty = (int)$product[$id];
            $totalQty = ($cart[$id] ?? 0) + $qty;
            $cart[$id] = $totalQty;
        }

        $this->session->cart = $cart;
        // TODO: update inventory

        return true;
    }

    /**
     * Remove products from the cart
     *
     * @param array $products
     * @return bool
     */
    public function removeProductFromCart(array $products): bool
    {
        $cart = $this->session->cart;

        foreach ($products as $product) {
            $id = (int)array_keys($product)[0];
            $qty = (int)$product[$id];
            $totalQty = ($cart[$id] ?? 0) - $qty;
            $cart[$id] = $totalQty;
        }

        $this->session->cart = $cart;
        // TODO: update inventory

        return true;
    }

    // TODO: move this method to the QuickMartTransaction class
    public function transaction(
        float   $paymentValue = 0.0,
        string  $paymentType = QuickMartTransaction::PAYMENT_TYPE_CASH,
        ?string $customerEmail = null,
        bool    $userFriendlyData = true,
    ): array
    {
        $inventory = $this->getInventory();
        $isMember = $this->fileCrmService->isMemberCustomer($customerEmail ?? '');
        $taxableProducts = [];
        $taxValue = 6.5; // TODO: get this value from settings & locale

        // Output data
        $txnNumber = '----';
        $totalItems = 0;
        $txnSubTotal = 0; // i.e. before taxes
        $txnDate = time();
        $txnDateLabel = date('F j, Y', $txnDate);
        $txnDateFile = date('mdY', $txnDate);
        $txnDetailLines = [];
        $savings = 0;

        foreach ($this->session->cart as $id => $qty) {
            $productDetail = $inventory[$id]['detail'];
            $newAvail = $productDetail['quantity'] - $qty; // TODO: update inventory line entry
            $memberPrice = floatval(str_replace('$', '', $productDetail['memberPrice']));
            $regularPrice = floatval(str_replace('$', '', $productDetail['regularPrice']));
            $price = $isMember ? $memberPrice : $regularPrice;
            $savings += ($price - $memberPrice);
            $lineTotal = $price * $qty;
            $txnSubTotal += $lineTotal;
            $totalItems += $qty;

            if (strtolower($productDetail['taxStatus']) === FlatFileQuickMartService::TAX_STATUS_TAXABLE) {
                $taxableProducts[] = $lineTotal;
            }

            $txnDetailLines[] = [
                'productId' => $id,
                'productName' => $inventory[$id]['name'],
                'qty' => $qty,
                'singlePrice' => $price,
                'total' => $lineTotal,
            ];
        }

        $totalTaxable = array_sum($taxableProducts);
        $taxDeductible = ($totalTaxable * $taxValue) / 100;
        $txnTotal = $txnSubTotal + $taxDeductible;
        $change = max($paymentValue - $txnTotal, 0);

        return [
            'date' => $userFriendlyData ? $txnDateLabel : $txnDate,
            'dateFile' => $txnDateFile,
            'dateStamp' => $txnDate,
            'totalItems' => $totalItems,
            'paymentType' => $paymentType,
            'paymentValue' => ($userFriendlyData ? '$' : '') . $paymentValue,
            'txnNumber' => $txnNumber,
            'txnSubTotal' => ($userFriendlyData ? '$' : '') . $txnSubTotal,
            'taxDeductible' => ($userFriendlyData ? '$' : '') . $taxDeductible,
            'txnTotal' => ($userFriendlyData ? '$' : '') . $txnTotal,
            'change' => ($userFriendlyData ? '$' : '') . $change,
            'savings' => $savings,
            'detail' => $txnDetailLines,
        ];
    }

    private function loadUpTransaction(string $filePath): array
    {
        $star = '*';
        $starFound = 0;
        $headerLines = 2;
        $lines = $this->fileStorageService->getLines($filePath, true);
        $header = [];
        $detail = [];
        $footer = [];
        $savings = [];
        $count = 0;
        $keys = ['header'];

        echo '<pre>';

        foreach ($lines as $index => $line) {
            $line = trim($line);
//            echo $line . '<br>';

            if ($index <= $headerLines) {
                $header[] = $line;
                continue;
            }

            if (str_starts_with($line, $star) && str_ends_with($line, $star)) {
                $starFound++;
                continue;
            }

            if ($index > $headerLines && $starFound === 0) {
                $detail[] = $line;
                continue;
            }

            if ($starFound === 1) {
                $footer[] = $line;
                continue;
            }

            if ($starFound === 2) {
                $savings[] = $line;
            }
        }

        $transaction = [
            'header' => $header,
            'detail' => $detail,
            'footer' => $footer,
            'savings' => $savings,
        ];

        echo '<pre>';
        var_dump($detail);
        exit;

        // TODO: implement
        return [];
    }

    public function cancelTransaction(string $filePath): bool
    {
        $transaction = $this->loadUpTransaction($filePath);
        // TODO: implement
        return false;
    }

    public function cancelTransactions(array $values): array
    {
        $txnOutputPath = $this->resourcePath['resources']['output'] . DIRECTORY_SEPARATOR;
        $txnOutputPath .= 'transactions' . DIRECTORY_SEPARATOR . 'success' . DIRECTORY_SEPARATOR;
        $output = [];

        foreach ($values as $txnNumber) {
            $fileNamePattern = "transaction_$txnNumber" . '_*.txt';
            $filePath = $this->fileStorageService->filePathLookUp($txnOutputPath, $fileNamePattern);

            if ($filePath) {
                $output[$txnNumber] = $this->cancelTransaction($filePath);
            }
        }

        return $output;
    }

    /**
     * Calculate a transaction and save it as a .txt file
     *
     * @param string $customerEmail
     * @param float $paymentValue
     * @param string $paymentType
     * @return array
     */
    public function saveTransactionFile(string $customerEmail, float $paymentValue, string $paymentType): array
    {
        $txnOutputPath = $this->resourcePath['resources']['output'] . DIRECTORY_SEPARATOR;
        $txnOutputPath .= 'transactions' . DIRECTORY_SEPARATOR . 'success' . DIRECTORY_SEPARATOR;
        $txnNumber = $this->fileStorageService->countFilesInDir($txnOutputPath) + 1;
        $txnMaxExtraDigits = $this->config['maxInvoiceDigits'] - strlen(strval($txnNumber));
        $txnExtraDigits = str_repeat('0', $txnMaxExtraDigits);
        $transaction = $this->transaction($paymentValue, $paymentType, $customerEmail);
        $transaction['txnNumber'] = $txnExtraDigits . $txnNumber;
        $txnOutputPath .= sprintf($this->config['files']['transaction'], $transaction['txnNumber'], $transaction['dateFile']);

        // File contents
        $contents = $transaction['date'] . "\n";
        $contents .= 'TRANSACTION: ' . $transaction['txnNumber'] . "\n";
        $contents .= implode("\t", ['ITEM', 'QUANTITY', 'UNIT PRICE', 'TOTAL']) . "\n";

        foreach ($transaction['detail'] as $txnDetailLine) {
            $lineData = [
                $txnDetailLine['productName'],
                $txnDetailLine['qty'],
                $txnDetailLine['singlePrice'],
                $txnDetailLine['total'],
            ];

            $contents .= implode("\t", $lineData) . "\n";
        }

        $contents .= str_repeat('*', 20) . "\n";
        $contents .= 'TOTAL NUMBER OF ITEMS SOLD: ' . $transaction['totalItems'] . "\n";
        $contents .= 'SUB-TOTAL: $' . $transaction['txnSubTotal'] . "\n";
        $contents .= 'TAX (6.5%): $' . $transaction['taxDeductible'] . "\n";
        $contents .= 'TOTAL: $' . $transaction['txnTotal'] . "\n";
        $contents .= $transaction['paymentType'] . ': ' . $transaction['paymentValue'] . "\n";
        $contents .= 'CHANGE: $' . $transaction['change'] . "\n";
        $contents .= str_repeat('*', 20) . "\n";
        $contents .= 'YOU SAVED: $' . $transaction['savings'] . "\n";
        $this->fileStorageService->writeFile($txnOutputPath, $contents);

        return $transaction;
    }
}
