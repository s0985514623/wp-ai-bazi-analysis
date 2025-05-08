<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Resources\Concerns;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseContract;
use R2WpBaziPlugin\vendor\OpenAI\Contracts\ResponseStreamContract;
use R2WpBaziPlugin\vendor\OpenAI\Testing\ClientFake;
use R2WpBaziPlugin\vendor\OpenAI\Testing\Requests\TestRequest;

trait Testable
{
    public function __construct(protected ClientFake $fake) {}

    abstract protected function resource(): string;

    /**
     * @param  array<string, mixed>  $args
     */
    protected function record(string $method, array $args = []): ResponseContract|ResponseStreamContract|string
    {
        return $this->fake->record(new TestRequest($this->resource(), $method, $args));
    }

    public function assertSent(callable|int|null $callback = null): void
    {
        $this->fake->assertSent($this->resource(), $callback);
    }

    public function assertNotSent(callable|int|null $callback = null): void
    {
        $this->fake->assertNotSent($this->resource(), $callback);
    }
}
