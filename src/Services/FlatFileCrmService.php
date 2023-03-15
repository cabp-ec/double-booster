<?php

declare(strict_types=1);

namespace App\Services;

class FlatFileCrmService extends BaseService
{
    const DETAIL_INDEXES = ['quantity', 'regularPrice', 'memberPrice', 'taxStatus'];
    const TAX_STATUS_EXEMPT = 'tax-exempt';
    const TAX_STATUS_TAXABLE = 'taxable';
    const TAX_STATUSSES = [self::TAX_STATUS_EXEMPT, self::TAX_STATUS_TAXABLE];

    public function __construct(
        private FileStorageService $fileStorageService,
        protected array            $resourcePath,
        protected array            $config
    )
    {
        parent::__construct($config);
    }

    /**
     * Transform a single customer data-line into a workable format
     *
     * @param string $line
     * @return array
     */
    private function transformCustomerEntryLine(string $line): array
    {
        $productDetail = explode($this->config['customerDetailDelimiter'], $line);
        $email = trim($productDetail[0]);
        $rewards = boolval(trim($productDetail[1] ?? 0));

        return [
            'email' => $email,
            'rewards' => $rewards,
        ];
    }

    /**
     * Get all customers
     *
     * @param array $filters
     * @return array
     */
    public function getCustomers(array $filters = []): array
    {
        // TODO: DRY
        $path = $this->resourcePath['resources']['input'] . DIRECTORY_SEPARATOR . $this->config['files']['customers'];
        $lines = $this->fileStorageService->getLines($path);
        $output = [];
        $find = $filters['find'] ?? false;

        foreach ($lines as $index => $line) {
            $id = $index + 1;
            $dataLine = $this->transformCustomerEntryLine($line);
            $output[$id] = array_merge_recursive(['id' => $id], $dataLine);

            if ($find && $dataLine['email'] === $find) {
                break;
            }
        }

        return $output;
    }

    /**
     * Fins a customer by e-mail
     *
     * @param string $email
     * @return array
     */
    public function findCustomer(string $email): array
    {
        $customers = $this->getCustomers(['find' => $email]);
        $keys = array_keys($customers);
        $key = $keys[count($keys) - 1];

        return $customers[$key];
    }

    /**
     * Check if the given customer is a member (i.e. having rewards)
     * Having rewards means: yes|no
     * Having reward points means: numeric, countable
     *
     * @param string $email
     * @return bool
     */
    public function isMemberCustomer(string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        $customer = $this->findCustomer($email);
        return $customer['rewards'] ?? false;
    }
}
