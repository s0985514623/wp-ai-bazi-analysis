<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\AudioContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Audio;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Audio\SpeechStreamResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Audio\TranscriptionResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Audio\TranslationResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class AudioTestResource implements AudioContract
{
    use Testable;

    protected function resource(): string
    {
        return Audio::class;
    }

    public function speech(array $parameters): string
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function speechStreamed(array $parameters): SpeechStreamResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function transcribe(array $parameters): TranscriptionResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function translate(array $parameters): TranslationResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
