<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\EditResponse;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Images\VariationResponse;

interface ImagesContract
{
    /**
     * Creates an image given a prompt.
     *
     * @see https://platform.openai.com/docs/api-reference/images/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters): CreateResponse;

    /**
     * Creates an edited or extended image given an original image and a prompt.
     *
     * @see https://platform.openai.com/docs/api-reference/images/create-edit
     *
     * @param  array<string, mixed>  $parameters
     */
    public function edit(array $parameters): EditResponse;

    /**
     * Creates a variation of a given image.
     *
     * @see https://platform.openai.com/docs/api-reference/images/create-variation
     *
     * @param  array<string, mixed>  $parameters
     */
    public function variation(array $parameters): VariationResponse;
}
