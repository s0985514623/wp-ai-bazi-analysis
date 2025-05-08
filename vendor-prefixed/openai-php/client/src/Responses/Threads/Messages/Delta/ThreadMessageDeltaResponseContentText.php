<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages\Delta;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages\ThreadMessageResponseContentTextAnnotationFileCitationObject;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages\ThreadMessageResponseContentTextAnnotationFilePathObject;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{value: ?string, annotations: array<int, array{type: 'file_citation', text: string, file_citation: array{file_id: string, quote?: string}, start_index: int, end_index: int}|array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}>}>
 */
final class ThreadMessageDeltaResponseContentText implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{value: ?string, annotations: array<int, array{type: 'file_citation', text: string, file_citation: array{file_id: string, quote?: string}, start_index: int, end_index: int}|array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}>}>
     */
    use ArrayAccessible;

    use Fakeable;

    /**
     * @param  \R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages\ThreadMessageResponseContentTextAnnotationFilePathObject[]|\R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages\ThreadMessageResponseContentTextAnnotationFileCitationObject[]  $annotations
     */
    private function __construct(
        public ?string $value,
        public array $annotations,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{value?: string, annotations?: array<int, array{type: 'file_citation', text: string, file_citation: array{file_id: string, quote?: string}, start_index: int, end_index: int}|array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}>}  $attributes
     */
    public static function from(array $attributes): self
    {
        $annotations = array_map(
            fn (array $annotation): ThreadMessageResponseContentTextAnnotationFileCitationObject|ThreadMessageResponseContentTextAnnotationFilePathObject => match ($annotation['type']) {
                'file_citation' => ThreadMessageResponseContentTextAnnotationFileCitationObject::from($annotation),
                'file_path' => ThreadMessageResponseContentTextAnnotationFilePathObject::from($annotation),
            },
            $attributes['annotations'] ?? [],
        );

        return new self(
            $attributes['value'] ?? null,
            $annotations,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'annotations' => array_map(
                fn (ThreadMessageResponseContentTextAnnotationFilePathObject|ThreadMessageResponseContentTextAnnotationFileCitationObject $annotation): array => $annotation->toArray(),
                $this->annotations,
            ),
        ];
    }
}
