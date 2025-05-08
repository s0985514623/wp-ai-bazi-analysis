<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseHasMetaInformationContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Assistants\AssistantResponseToolResources;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\HasMetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id: string, object: string, created_at: int, tool_resources: ?array{code_interpreter?: array{file_ids: array<int,string>}, file_search?: array{vector_store_ids: array<int,string>}}, metadata: array<string, string>}>
 */
final class ThreadResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{id: string, object: string, created_at: int, tool_resources: ?array{code_interpreter?: array{file_ids: array<int,string>}, file_search?: array{vector_store_ids: array<int,string>}}, metadata: array<string, string>}>
     */
    use ArrayAccessible;

    use Fakeable;
    use HasMetaInformation;

    /**
     * @param  array<string, string>  $metadata
     */
    private function __construct(
        public string $id,
        public string $object,
        public int $createdAt,
        public ?AssistantResponseToolResources $toolResources,
        public array $metadata,
        private readonly MetaInformation $meta,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id: string, object: string, created_at: int, tool_resources: ?array{code_interpreter?: array{file_ids: array<int,string>}, file_search?: array{vector_store_ids: array<int,string>}}, metadata: array<string, string>}  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created_at'],
            isset($attributes['tool_resources']) ? AssistantResponseToolResources::from($attributes['tool_resources']) : null,
            $attributes['metadata'],
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'tool_resources' => $this->toolResources?->toArray(),
            'metadata' => $this->metadata,
        ];
    }
}
