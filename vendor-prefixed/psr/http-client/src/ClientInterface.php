<?php

namespace R2WpBaziPlugin\vendor\Psr\Http\Client;

use R2WpBaziPlugin\vendor\Psr\Http\Message\RequestInterface;
use R2WpBaziPlugin\vendor\Psr\Http\Message\ResponseInterface;

interface ClientInterface {

	/**
	 * Sends a PSR-7 request and returns a PSR-7 response.
	 *
	 * @param RequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \R2WpBaziPlugin\vendor\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
	 */
	public function sendRequest( RequestInterface $request ): ResponseInterface;
}
