<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Assistants;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{file_ids: array<int,string>}>
 */
final class AssistantResponseToolResourceCodeInterpreter implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{file_ids: array<int,string>}>
     */
    use ArrayAccessible;

    use Fakeable;

    /**
     * @param  array<int, string>  $fileIds
     */
    private function __construct(
        public array $fileIds,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{file_ids: array<int,string>}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['file_ids'],
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'file_ids' => $this->fileIds,
        ];
    }
}
