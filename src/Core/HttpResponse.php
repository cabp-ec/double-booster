<?php

declare(strict_types=1);

namespace App\Core;

use Exception;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class HttpResponse implements ResponseInterface
{
    private const KEY_CONTENT_TYPE = 'Content-Type';
    private const CONTENT_TYPE_HTML = 'text/html';
    private const CONTENT_TYPE_JSON = 'application/json';

    public const MIN_STATUS_CODE_VALUE = 100;
    public const MAX_STATUS_CODE_VALUE = 599;

    private StreamInterface $stream;
    private string $protocol = '1.1';
    private string $reasonPhrase;
    private int $statusCode;

    /**
     * All registered headers, as key => array of values
     *
     * @var array
     * @psalm-var array<non-empty-string, list<string>>
     */
    protected array $headers = [];

    /**
     * Map of normalized header name to original name used to register header
     *
     * @var array
     * @psalm-var array<non-empty-string, non-empty-string>
     */
    protected array $headerNames = [];

    /**
     * Map of standard HTTP status code/reason phrases
     *
     * @psalm-var array<positive-int, non-empty-string>
     */
    private array $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Content',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended (OBSOLETED)',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;           // RFC2518
    public const HTTP_EARLY_HINTS = 103;          // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;         // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;     // RFC5842
    public const HTTP_IM_USED = 226;              // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308; // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                         // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                   // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                  // RFC4918
    public const HTTP_LOCKED = 423;                                // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                     // RFC4918
    public const HTTP_TOO_EARLY = 425;                             // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                      // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                 // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                     // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;       // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;  // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                  // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                         // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                          // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;       // RFC6585

    /**
     * The HTTP Response Object
     * TODO: keep implementing PSR-compliant stubs
     *
     * @param string|StreamInterface $body
     * @param int $status
     * @param array $headers
     * @throws Exception
     */
    public function __construct(
        string|StreamInterface $body = 'php://memory',
        int                    $status = 200,
        array                  $headers = []
    )
    {
        $this->setStatusCode($status);
        $this->stream = $this->getStream($body);
        $this->setHeaders($headers);
    }

    /**
     * Set a valid status code
     * TODO: update signature to allow for custom phrase
     *
     * @param int $code
     */
    private function setStatusCode(int $code): void
    {
        $reasonPhrase = null;

        if ($code < static::MIN_STATUS_CODE_VALUE || $code > static::MAX_STATUS_CODE_VALUE) {
            throw new InvalidArgumentException(sprintf(
                'Invalid status code "%s"; must be an integer between %d and %d, inclusive',
                $code,
                static::MIN_STATUS_CODE_VALUE,
                static::MAX_STATUS_CODE_VALUE
            ));
        }

        if (isset($this->phrases[$code])) {
            $reasonPhrase = null ?? $this->phrases[$code];
        }

        $this->reasonPhrase = $reasonPhrase;
        $this->statusCode = $code;
    }

    /**
     * Get a stream object out f the given body
     *
     * @param StreamInterface|string $stream
     * @return StreamInterface
     * @throws Exception
     */
    private function getStream(StreamInterface|string $stream): StreamInterface
    {
        if ($stream instanceof StreamInterface) {
            return $stream;
        }

        if (!is_string($stream) && !is_resource($stream)) {
            throw new InvalidArgumentException(
                'Stream must be a string stream resource identifier, '
                . 'an actual stream resource, '
                . 'or a Psr\Http\Message\StreamInterface implementation'
            );
        }

        return new HttpResponseStream($stream);
    }

    /**
     * Filter a set of headers to ensure they are in the correct internal format
     * Used by message constructors to allow setting all initial headers at once
     *
     * @param array $values Headers to filter.
     */
    private function setHeaders(array $values): void
    {
        // TODO: perform proper checks here
        $this->headers = $values;
    }

    /**
     * Validate the given HTTP protocol version
     *
     * @param string $value
     * @return void
     */
    private function validateProtocolVersion(string $value): void
    {
        // TODO: implement
    }

    /**
     * Send response headers
     *
     * @return void
     */
    private function sendHeaders(): void
    {
        // Headers have already been sent
        if (headers_sent()) {
            return;
        }

        // TODO: add or transform response headers here (e.g. cookies, cors, encoding)
        $headers = $this->getHeaders();
        $statusCode = $this->getStatusCode();

        foreach ($headers as $name => $value) {
            $replace = 0 === strcasecmp($name, self::KEY_CONTENT_TYPE);
            header($name . ': ' . $value, $replace, $statusCode);
        }

        // Status
        $version = $this->getProtocolVersion();
        $statusText = $this->getReasonPhrase();
        header(sprintf('HTTP/%s %s %s', $version, $statusCode, $statusText), true, $statusCode);
    }

    /**
     * In case you want to do additional stuff over your content,
     * use this method, otherwise it's not necessary...
     *
     * @return void
     */
    private function sendContent(): void
    {
        echo $this->getBody();
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version): MessageInterface
    {
        $this->validateProtocolVersion($version);
        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        // TODO: Implement hasHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        // TODO: Implement getHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        // TODO: Implement withHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        // TODO: Implement withAddedHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        // TODO: Implement withoutHeader() method.
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        // TODO: Implement withBody() method.
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * Send the response
     *
     * @return $this
     */
    public function send(): static
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this;
    }
}
