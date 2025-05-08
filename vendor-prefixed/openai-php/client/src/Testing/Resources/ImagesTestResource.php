<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ImagesContract;
use R2WpBaziPlugin\vendor\OpenAI\Resources\Images;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\EditResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\VariationResponse;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns\Testable;

final class ImagesTestResource implements ImagesContract
{
    use Testable;

    protected function resource(): string
    {
        return Images::class;
    }

    public function create(array $parameters): CreateResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function edit(array $parameters): EditResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }

    public function variation(array $parameters): VariationResponse
    {
        return $this->record(__FUNCTION__, func_get_args());
    }
}
