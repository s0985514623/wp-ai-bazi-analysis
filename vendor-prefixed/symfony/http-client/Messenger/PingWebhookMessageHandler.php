<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace R2WpBaziPlugin\vendor\Symfony\Component\HttpClient\Messenger;

use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class PingWebhookMessageHandler
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function __invoke(PingWebhookMessage $message): ResponseInterface
    {
        $response = $this->httpClient->request($message->method, $message->url, $message->options);
        $response->getHeaders($message->throw);

        return $response;
    }
}
