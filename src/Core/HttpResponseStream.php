<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Http\Message\StreamInterface;
use Throwable;

use function fopen;
use function fwrite;
use function get_resource_type;
use function in_array;
use function is_resource;
use function sprintf;
use function stream_get_contents;

use const PHP_VERSION_ID;
use const SEEK_SET;

class HttpResponseStream implements StreamInterface
{
    /**
     * A list of allowed stream resource types that are allowed to instantiate a Stream
     * TODO: include GD to handle image streams properly
     */
    private const ALLOWED_STREAM_RESOURCE_TYPES = ['stream'];

    /** @var resource|null */
    protected $resource;
    protected string $stream;

    /**
     * The HTTP response stream class
     *
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->setStream($body);
    }

    /**
     * Set a stream for the output
     *
     * @param string $body
     * @return void
     */
    private function setStream(string $body): void
    {
        try {
            $resource = fopen('php://memory', 'r+');
            fwrite($resource, $body);
            rewind($resource);
        } catch (Throwable $error) {
            throw new \App\Core\RuntimeException(
                sprintf('Invalid stream reference provided: %s', $error->getMessage()),
                0,
                $error
            );
        }

        if (!$this->isValidStreamResourceType($resource)) {
            throw new \App\Core\InvalidArgumentException(
                'Invalid stream provided; must be a string stream identifier or stream resource'
            );
        }

        $this->stream = $body;
        $this->resource = $resource;
    }

    /**
     * Determine if a resource is one of the resource types allowed to instantiate a Stream
     *
     * @param mixed $resource Stream resource.
     * @return bool
     */
    private function isValidStreamResourceType(mixed $resource): bool
    {
        if (is_resource($resource)) {
            return in_array(get_resource_type($resource), self::ALLOWED_STREAM_RESOURCE_TYPES, true);
        }

        // TODO: implement checks for GD and other stuff here
        return false;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    public function detach()
    {
        // TODO: Implement detach() method.
    }

    public function getSize()
    {
        // TODO: Implement getSize() method.
    }

    public function tell()
    {
        // TODO: Implement tell() method.
    }

    public function eof()
    {
        // TODO: Implement eof() method.
    }

    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        // TODO: Implement seek() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    public function write($string)
    {
        // TODO: Implement write() method.
    }

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function read($length)
    {
        // TODO: Implement read() method.
    }

    public function getContents(): string
    {
        return stream_get_contents($this->resource);
    }

    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }
}
