<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Runs\Steps;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{message_id: string}>
 */
final class ThreadRunStepResponseMessageCreation implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{message_id: string}>
     */
    use ArrayAccessible;

    use Fakeable;

    private function __construct(
        public string $messageId,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{message_id: string}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['message_id'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
        ];
    }
}
