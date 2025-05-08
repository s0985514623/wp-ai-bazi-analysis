<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Images;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseHasMetaInformationContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\HasMetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{created: int, data: array<int, array{url?: string, b64_json?: string}>}>
 */
final class EditResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{created: int, data: array<int, array{url?: string, b64_json?: string}>}>
     */
    use ArrayAccessible;

    use Fakeable;
    use HasMetaInformation;

    /**
     * @param  array<int, EditResponseData>  $data
     */
    private function __construct(
        public readonly int $created,
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{created: int, data: array<int, array{url?: string, b64_json?: string}>}  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $results = array_map(fn (array $result): EditResponseData => EditResponseData::from(
            $result
        ), $attributes['data']);

        return new self(
            $attributes['created'],
            $results,
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'created' => $this->created,
            'data' => array_map(
                static fn (EditResponseData $result): array => $result->toArray(),
                $this->data,
            ),
        ];
    }
}
