<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Resources\Concerns;

use R2WpBaziPlugin\vendor\OpenAI\Contracts\TransporterContract;

trait Transportable
{
    public function __construct(private readonly TransporterContract $transporter)
    {
        // ..
    }
}
