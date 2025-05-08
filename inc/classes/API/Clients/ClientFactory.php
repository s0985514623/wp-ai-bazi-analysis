<?php
/**
 * API 客戶端工廠
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API\Clients;

use R2\WpBaziPlugin\Admin\ApiSettings;

/**
 * ClientFactory 類
 * 用於創建API客戶端實例
 */
class ClientFactory {
	/**
	 * 創建API客戶端實例
	 *
	 * @return BaseClient 客戶端實例
	 */
	public static function createClient(): BaseClient {
		$api_settings = ApiSettings::get_api_settings();
		$api_provider = $api_settings['api_provider'] ?? 'deepseek';

		switch ($api_provider) {
			case 'openai':
				return OpenAIClient::getInstance();
			case 'deepseek':
			default:
				return DeepSeekClient::getInstance();
		}
	}
}
