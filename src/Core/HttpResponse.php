<?php

declare(strict_types=1);

namespace App\Core;

use Laminas\Diactoros\Response as LaminasResponse;

class HttpResponse extends LaminasResponse
{
    public const HEADER_ACCEPT = 'Accept';
    public const HEADER_CONTENT_TYPE = 'Content-Type';
    public const CONTENT_TYPE_HTML = 'text/html';
    public const CONTENT_TYPE_JSON = 'application/json';

    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;            // RFC2518
    public const HTTP_EARLY_HINTS = 103;           // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;          // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    public const HTTP_IM_USED = 226;               // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_NOT_FOUND_MESSAGE = 'Resource not found';
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
    public const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const HTTP_LOCKED = 423;                                                      // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    private int $statusCode;

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = ''): static
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Send headers
     *
     * @return HttpResponse
     */
    private function sendHeaders(): static
    {
        // Headers have already been sent
        if (headers_sent()) {
            return $this;
        }

        // Headers
        $headers = $this->getHeaders();

//        echo '<pre>';
//        var_dump($this->getStatusCode());
//        exit;

        $statusCode = $this->getStatusCode();

        // Set cookies (if any)

        foreach ($headers as $name => $value) {
            $replace = 0 === strcasecmp($name, self::HEADER_CONTENT_TYPE);
            header($name . ': ' . $value[0], $replace, $statusCode);
        }

        // Status
        $version = $this->getProtocolVersion();
        $statusText = $this->getReasonPhrase();
        header(sprintf('HTTP/%s %s %s', $version, $statusCode, $statusText), true, $statusCode);
        return $this;
    }

    /**
     * Write the response body
     *
     * @param string $value
     * @return $this
     */
    public function writeBody(string $value): static
    {
        $this->getBody()->write($value);
        return $this;
    }

    /**
     * Send the response body
     *
     * @return $this
     */
    private function sendContent(): static
    {
        echo $this->getBody();
        return $this;
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
