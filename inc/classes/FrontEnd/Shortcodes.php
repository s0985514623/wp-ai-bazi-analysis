<?php
/**
 * Shortcodes
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\FrontEnd;

/**
 * Class Shortcodes
 */
final class Shortcodes {
	use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

	/**
	 * Constructor
	 */
	public function __construct() {
		\add_shortcode('bazi-page', [ $this, 'render_bazi_page' ]);
	}

	/**
	 * Render bazi page shortcode
	 *
	 * @param array  $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string Rendered shortcode.
	 */
	public function render_bazi_page( $atts = [], $content = null ): string {
		// 合并属性
		$atts = \shortcode_atts(
			[
				'class' => 'bazi-container',
			],
			$atts,
			'bazi-page'
		);

		// 创建一个空的div用于React渲染
		$output = '<div id="bazi-page" class="' . \esc_attr($atts['class']) . '"></div>';

		return $output;
	}
}
