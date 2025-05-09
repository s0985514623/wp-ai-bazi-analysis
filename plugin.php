<?php
/**
 * Plugin Name:       WP AI 八字命格分析 (DEV)
 * Plugin URI:        https://github.com/s0985514623/wp-ai-bazi-analysis
 * Description:       串接AI進行八字命格分析外掛
 * Version:           1.0.8
 * Requires at least: 5.7
 * Requires PHP:      8.0
 * Author:            Ren
 * Author URI:        https://github.com/s0985514623
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp_ai_bazi_analysis
 * Domain Path:       /languages
 * Tags: vite, react, tailwind, typescript, react-query, scss, WordPress, WordPress plugin
 */

declare ( strict_types=1 );

namespace R2\WpBaziPlugin;

if ( ! \class_exists( 'R2\WpBaziPlugin\Plugin' ) ) {
	require_once __DIR__ . '/vendor-prefixed/autoload.php';
	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class Plugin
	 */
	final class Plugin {
		use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\PluginTrait;
		use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

		/**
		 * Constructor
		 */
		public function __construct() {

			// $this->required_plugins = array(
			// array(
			// 'name'     => 'WooCommerce',
			// 'slug'     => 'woocommerce',
			// 'required' => true,
			// 'version'  => '7.6.0',
			// ),
			// array(
			// 'name'     => 'WP Toolkit',
			// 'slug'     => 'wp-toolkit',
			// 'source'   => 'Author URL/wp-toolkit/releases/latest/download/wp-toolkit.zip',
			// 'required' => true,
			// ),
			// );

			$this->init(
				[
					'app_name'    => 'WP AI Bazi',
					'github_repo' => 'https://github.com/s0985514623/wp-ai-bazi-analysis',
					'callback'    => [ Bootstrap::class, 'instance' ],
				]
			);
		}
	}
}

Plugin::instance();
