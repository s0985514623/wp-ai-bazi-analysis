<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\FineTunes;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\FakeableForStreamedResponse;

/**
 * @implements ResponseContract<array{object: string, created_at: int, level: string, message: string}>
 */
final class RetrieveStreamedResponseEvent implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{object: string, created_at: int, level: string, message: string}>
     */
    use ArrayAccessible;

    use FakeableForStreamedResponse;

    private function __construct(
        public readonly string $object,
        public readonly int $createdAt,
        public readonly string $level,
        public readonly string $message,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{object: string, created_at: int, level: string, message: string}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['object'],
            $attributes['created_at'],
            $attributes['level'],
            $attributes['message'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'level' => $this->level,
            'message' => $this->message,
        ];
    }
}
