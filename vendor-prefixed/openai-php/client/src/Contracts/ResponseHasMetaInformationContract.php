<?php

declare(strict_types=1);

namespace R2WpBaziPlugin\vendor\OpenAI\Contracts;

use R2WpBaziPlugin\vendor\OpenAI\Responses\Meta\MetaInformation;

interface ResponseHasMetaInformationContract {

	public function meta(): MetaInformation;
}
