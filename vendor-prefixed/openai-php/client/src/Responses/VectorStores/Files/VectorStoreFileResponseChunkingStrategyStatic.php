<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\VectorStores\Files;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{type: 'static', static: array{max_chunk_size_tokens: int, chunk_overlap_tokens: int}}>
 */
final class VectorStoreFileResponseChunkingStrategyStatic implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{type: 'static', static: array{max_chunk_size_tokens: int, chunk_overlap_tokens: int}}>
     */
    use ArrayAccessible;

    use Fakeable;

    /**
     * @param  'static'  $type
     */
    private function __construct(
        public readonly string $type,
        public readonly int $maxChunkSizeTokens,
        public readonly int $chunkOverlapTokens,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{type: 'static', static: array{max_chunk_size_tokens: int, chunk_overlap_tokens: int}}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['type'],
            $attributes['static']['max_chunk_size_tokens'],
            $attributes['static']['chunk_overlap_tokens'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'static' => [
                'max_chunk_size_tokens' => $this->maxChunkSizeTokens,
                'chunk_overlap_tokens' => $this->chunkOverlapTokens,
            ],
        ];
    }
}
