<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Resources;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\Resources\ModerationsContract;
use R2WpBaziPlugin\vendor\OpenAI\Responses\Moderations\CreateResponse;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Payload;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Response;

final class Moderations implements ModerationsContract
{
    use Concerns\Transportable;

    /**
     * Classifies if text violates OpenAI's Content Policy.
     *
     * @see https://platform.openai.com/docs/api-reference/moderations/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters): CreateResponse
    {
        $payload = Payload::create('moderations', $parameters);

        /** @var Response<array{id: string, model: string, results: array<int, array{categories: array<string, bool>, category_scores: array<string, float>, flagged: bool}>}> $response */
        $response = $this->transporter->requestObject($payload);

        return CreateResponse::from($response->data(), $response->meta());
    }
}
