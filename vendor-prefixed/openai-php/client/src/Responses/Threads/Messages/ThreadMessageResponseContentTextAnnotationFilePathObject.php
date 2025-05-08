<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Responses\Threads\Messages;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Concerns\ArrayAccessible;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}>
 */
final class ThreadMessageResponseContentTextAnnotationFilePathObject implements ResponseContract
{
    /**
     * @use ArrayAccessible<array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}>
     */
    use ArrayAccessible;

    use Fakeable;

    /**
     * @param  'file_path'  $type
     */
    private function __construct(
        public string $type,
        public string $text,
        public int $startIndex,
        public int $endIndex,
        public ThreadMessageResponseContentTextAnnotationFilePath $filePath,
    ) {}

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{type: 'file_path', text: string, file_path: array{file_id: string}, start_index: int, end_index: int}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['type'],
            $attributes['text'],
            $attributes['start_index'],
            $attributes['end_index'],
            ThreadMessageResponseContentTextAnnotationFilePath::from($attributes['file_path']),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
            'start_index' => $this->startIndex,
            'end_index' => $this->endIndex,
            'file_path' => $this->filePath->toArray(),
        ];
    }
}
