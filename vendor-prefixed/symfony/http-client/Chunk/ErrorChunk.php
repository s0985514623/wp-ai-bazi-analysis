<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Chunk;

use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Exception\TimeoutException;
use R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Exception\TransportException;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ChunkInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class ErrorChunk implements ChunkInterface
{
    private bool $didThrow = false;
    private int $offset;
    private string $errorMessage;
    private ?\Throwable $error = null;

    public function __construct(int $offset, \Throwable|string $error)
    {
        $this->offset = $offset;

        if (\is_string($error)) {
            $this->errorMessage = $error;
        } else {
            $this->error = $error;
            $this->errorMessage = $error->getMessage();
        }
    }

    public function isTimeout(): bool
    {
        $this->didThrow = true;

        if (null !== $this->error) {
            throw new TransportException($this->errorMessage, 0, $this->error);
        }

        return true;
    }

    public function isFirst(): bool
    {
        $this->didThrow = true;
        throw null !== $this->error ? new TransportException($this->errorMessage, 0, $this->error) : new TimeoutException($this->errorMessage);
    }

    public function isLast(): bool
    {
        $this->didThrow = true;
        throw null !== $this->error ? new TransportException($this->errorMessage, 0, $this->error) : new TimeoutException($this->errorMessage);
    }

    public function getInformationalStatus(): ?array
    {
        $this->didThrow = true;
        throw null !== $this->error ? new TransportException($this->errorMessage, 0, $this->error) : new TimeoutException($this->errorMessage);
    }

    public function getContent(): string
    {
        $this->didThrow = true;
        throw null !== $this->error ? new TransportException($this->errorMessage, 0, $this->error) : new TimeoutException($this->errorMessage);
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getError(): ?string
    {
        return $this->errorMessage;
    }

    public function didThrow(?bool $didThrow = null): bool
    {
        if (null !== $didThrow && $this->didThrow !== $didThrow) {
            return !$this->didThrow = $didThrow;
        }

        return $this->didThrow;
    }

    public function __sleep(): array
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        if (!$this->didThrow) {
            $this->didThrow = true;
            throw null !== $this->error ? new TransportException($this->errorMessage, 0, $this->error) : new TimeoutException($this->errorMessage);
        }
    }
}
