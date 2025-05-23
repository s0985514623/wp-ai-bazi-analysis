<?php
/**
 * Bootstrap
 */

declare (strict_types = 1);

namespace R2\WpBaziPlugin;

use R2\WpBaziPlugin\Utils\Base;
use R2WpBaziPlugin\vendor\Kucrut\Vite;

/**
 * Class Bootstrap
 */
final class Bootstrap {
	use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Admin\CPT::instance();
		Admin\ApiSettings::instance();
		// FrontEnd\Entry::instance();
		FrontEnd\Shortcodes::instance();
		API\AiChat::instance();
		// error log
		// error_log('Bootstrap constructor');

		\add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_script' ], 99 );
		\add_action( 'wp_enqueue_scripts', [ $this, 'frontend_enqueue_script' ], 99 );

		// 注冊圖像生成相關的鉤子
		$this->register_avatar_hooks();
	}

	/**
	 * Admin Enqueue script
	 * You can load the script on demand
	 *
	 * @param string $hook current page hook
	 *
	 * @return void
	 */
	public function admin_enqueue_script( $hook ): void {
		$this->enqueue_script();
	}


	/**
	 * Front-end Enqueue script
	 * You can load the script on demand
	 *
	 * @return void
	 */
	public function frontend_enqueue_script(): void {
		$this->enqueue_script();
	}

	/**
	 * Enqueue script
	 * You can load the script on demand
	 *
	 * @return void
	 */
	public function enqueue_script(): void {

		Vite\enqueue_asset(
			Plugin::$dir . '/js/dist',
			'js/src/main.tsx',
			[
				'handle'    => Plugin::$kebab,
				'in-footer' => true,
			]
		);

		$post_id   = \get_the_ID();
		$permalink = \get_permalink( $post_id );

		\wp_localize_script(
			Plugin::$kebab,
			Plugin::$snake . '_data',
			[
				'env' => [
					'siteUrl'       => \untrailingslashit( \site_url() ),
					'ajaxUrl'       => \untrailingslashit( \admin_url( 'admin-ajax.php' ) ),
					'userId'        => \wp_get_current_user()->data->ID ?? null,
					'postId'        => $post_id,
					'permalink'     => \untrailingslashit( $permalink ),
					'APP_NAME'      => Plugin::$app_name,
					'KEBAB'         => Plugin::$kebab,
					'SNAKE'         => Plugin::$snake,
					'BASE_URL'      => Base::BASE_URL,
					'APP1_SELECTOR' => Base::APP1_SELECTOR,
					'APP2_SELECTOR' => Base::APP2_SELECTOR,
					'API_TIMEOUT'   => Base::API_TIMEOUT,
					'nonce'         => \wp_create_nonce( Plugin::$kebab ),
				],
			]
		);

		\wp_localize_script(
			Plugin::$kebab,
			'wpApiSettings',
			[
				'root'  => \untrailingslashit( \esc_url_raw( rest_url() ) ),
				'nonce' => \wp_create_nonce( 'wp_rest' ),
			]
		);
	}

	/**
	 * 注冊頭像生成相關的鉤子
	 */
	private function register_avatar_hooks(): void {
		// 確保WP-Cron系統知道這個鉤子
		\add_filter(
			'cron_schedules',
			function ( $schedules ) {
				return $schedules;
			}
			);

		// 確保處理過期的頭像請求數據
		\add_action(
			'wp_scheduled_delete',
			function () {
				global $wpdb;

				// 清除超過24小時的頭像請求數據
				$expired_time = time() - ( 24 * HOUR_IN_SECONDS );

				// 獲取所有過期的頭像請求選項
				$avatar_options = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} 
					WHERE option_name LIKE %s 
					AND option_value LIKE %s",
					'%wp_bazi_avatar_request_%',
					'%"created_at":' . $expired_time . '%'
				)
				);

				// 刪除過期的選項
				if (!empty($avatar_options)) {
					foreach ($avatar_options as $option) {
						\delete_option($option->option_name);

						// 同時刪除相關的結果選項
						$request_id = str_replace('wp_bazi_avatar_request_', '', $option->option_name);
						\delete_option('wp_bazi_avatar_result_' . $request_id);
					}
				}
			}
			);
	}
}
