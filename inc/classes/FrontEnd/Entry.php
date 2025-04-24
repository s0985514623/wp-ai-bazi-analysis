<?php
/**
 * Front-end Entry
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\FrontEnd;

use R2\WpBaziPlugin\Utils\Base;
/**
 * Class Entry
 */
final class Entry {
	use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

	/**
	 * Constructor
	 */
	public function __construct() {
		\add_action( 'wp_footer', [ $this, 'render_app' ] );
	}

	/**
	 * Render application's markup
	 */
	public function render_app(): void {
		// phpcs:ignore
		echo '<div id="my_app"></div>';
	}
}
