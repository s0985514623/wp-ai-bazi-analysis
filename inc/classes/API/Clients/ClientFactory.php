<?php
/**
 * API 客戶端工廠
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API\Clients;

use R2\WpBaziPlugin\Admin\ApiSettings;
use R2\WpBaziPlugin\API\Services\ImageGenerationService;

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

	/**
	 * 獲取圖像生成服務實例
	 *
	 * @return ImageGenerationService|null 圖像生成服務實例或null（如果服務未啟用）
	 */
	public static function getImageService() {
		$api_settings = ApiSettings::get_api_settings();

		// 檢查是否啟用圖像生成功能
		if ($api_settings['enable_qavatar'] !== 'yes') {
			return null;
		}

		// 返回圖像生成服務實例
		return new ImageGenerationService($api_settings);
	}
}
