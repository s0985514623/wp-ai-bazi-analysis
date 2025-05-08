<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{name: string}>
 */
final class ThreadRunResponseToolChoiceFunction implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{name: string}>
     */
    use ArrayAccessible;

    use Fakeable;

    private function __construct(
        public string $name,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{name: string}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['name'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
