<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{prompt_tokens: int, completion_tokens: int, total_tokens: int}>
 */
final class ThreadRunStepResponseUsage implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{prompt_tokens: int, completion_tokens: int, total_tokens: int}>
     */
    use ArrayAccessible;

    use Fakeable;

    private function __construct(
        public int $completionTokens,
        public int $promptTokens,
        public int $totalTokens,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{prompt_tokens: int, completion_tokens: int, total_tokens: int}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['completion_tokens'],
            $attributes['prompt_tokens'],
            $attributes['total_tokens'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->totalTokens,
        ];
    }
}
