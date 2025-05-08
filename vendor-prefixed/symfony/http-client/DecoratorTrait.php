<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace R2WpBaziPlugin\vendor\Symfony\Component\HttpClient;

use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use R2WpBaziPlugin\vendor\Symfony\Contracts\Service\ResetInterface;

/**
 * Eases with writing decorators.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait DecoratorTrait
{
    private HttpClientInterface $client;

    public function __construct(?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);

        return $clone;
    }

    /**
     * @return void
     */
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
