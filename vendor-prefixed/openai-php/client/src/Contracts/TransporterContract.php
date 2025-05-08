<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Contracts;

use R2WpBaziPlugin\vendor\OpenAI\Exceptions\ErrorException;
use R2WpBaziPlugin\vendor\OpenAI\Exceptions\TransporterException;
use R2WpBaziPlugin\vendor\OpenAI\Exceptions\UnserializableResponse;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Payload;
use R2WpBaziPlugin\vendor\OpenAI\ValueObjects\Transporter\Response;
use R2WpBaziPlugin\vendor\Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface TransporterContract {

	/**
	 * Sends a request to a server.
	 *
	 * @return Response<array<array-key, mixed>|string>
	 *
	 * @throws ErrorException|UnserializableResponse|TransporterException
	 */
	public function requestObject( Payload $payload ): Response;

	/**
	 * Sends a content request to a server.
	 *
	 * @throws ErrorException|TransporterException
	 */
	public function requestContent( Payload $payload ): string;

	/**
	 * Sends a stream request to a server.
	 * *
	 *
	 * @throws ErrorException
	 */
	public function requestStream( Payload $payload ): ResponseInterface;
}
