<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns;

use BadMethodCallException;

/**
 * @template TArray of array
 *
 * @mixin Response<TArray>
 */
trait ArrayAccessible
{
    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset]; // @phpstan-ignore-line
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new BadMethodCallException('Cannot set response attributes.');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $offset): never
    {
        throw new BadMethodCallException('Cannot unset response attributes.');
    }
}
