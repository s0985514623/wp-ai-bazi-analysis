<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Fixtures\Models;

final class ListResponseFixture {

	public const ATTRIBUTES = [
		'object' => 'list',
		'data'   => [
			RetrieveResponseFixture::ATTRIBUTES,
		],
	];
}
