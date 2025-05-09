<?php
/**
 * R2WpBaziPlugin_vendor_OpenAI 客戶端
 */

declare (strict_types = 1);

namespace R2\WpBaziPlugin\API\Clients;

use R2WpBaziPlugin_vendor_OpenAI as OpenAI;

/**
 * OpenAI 客戶端類
 * 使用 openai-php/client 庫處理 OpenAI API 請求
 */
class OpenAIClient extends BaseClient {


	/**
	 * OpenAI 客戶端實例
	 *
	 * @var \R2WpBaziPlugin\vendor\OpenAI\Client
	 */
	private $client;

	/**
	 * 構造函數
	 */
	public function __construct() {
		parent::__construct();

		// 創建 OpenAI 客戶端實例
		$this->client = OpenAI::client($this->api_settings['api_key']);
	}

	/**
	 * 獲取實例（單例模式）
	 *
	 * @return OpenAIClient
	 */
	public static function getInstance() {
		static $instance = null;
		if ($instance === null) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * 創建聊天完成請求
	 *
	 * @param string $prompt 提示文本
	 * @param array  $options 可選參數
	 * @return array 處理後的響應
	 */
	public function createChatCompletion( string $prompt, array $options = [] ) {
		try {
			// 合併默認選項和傳入的選項
			$params = array_merge(
				[
					'model'       => $this->api_settings['api_model'],
					'messages'    => [
						[
							'role'    => 'user',
							'content' => $prompt,
						],
					],
					'temperature' => $this->api_settings['temperature'],
					'max_tokens'  => $this->api_settings['max_tokens'],
				],
				$options
				);

			// 發送請求
			$response = $this->client->chat()->create($params);
			// log response
			// error_log(print_r($response, true));
			// 返回響應內容
			return [
				'success' => true,
				'content' => $response->choices[0]->message->content,
				'data'    => $response->toArray(),
			];
		} catch (\Exception $e) {
			// 異常處理
			return [
				'success' => false,
				'error'   => $e->getMessage(),
				'code'    => $e->getCode(),
			];
		}
	}


	/**
	 * 處理八字分析請求
	 *
	 * @param array $data 用戶提交的數據
	 * @return array 處理後的響應
	 */
	public function analyzeBazi( array $data ) {
		// 獲取表單數據
		$gender        = $data['gender'] === 'male' ? '男' : '女';
		$year          = intval($data['year']);
		$month         = intval($data['month']);
		$day           = intval($data['day']);
		$hour          = $data['hour'];
		$city          = $data['city'];
		$country       = $data['country'];
		$analysis_type = $data['analysis_type'];

		// 獲取中文時辰
		$chinese_hour = $this->getChineseHour($hour);

		// 構建提示文本
		$prompt = $this->buildBaziPrompt(
			$gender,
			$year,
			$month,
			$day,
			$hour,
			$chinese_hour,
			$city,
			$country,
			$analysis_type
		);

		// 發送API請求
		$response = $this->createChatCompletion($prompt);

		// 處理響應
		if (! $response['success']) {
			return [
				'success' => false,
				'error'   => $response['error'],
				'code'    => $response['code'] ?? 500,
			];
		}

		// 解析JSON響應
		$content   = $response['content'];
		$bazi_data = $this->parseJsonResponse($content);

		if ($bazi_data === null) {
			return [
				'success' => false,
				'error'   => '無法解析分析結果',
				'code'    => 422,
			];
		}

		// 合併原始數據和分析結果
		$result                = array_merge($data, $bazi_data);
		$result['apiProvider'] = 'openai';
		$result['apiModel']    = $this->api_settings['api_model'];

		// 獲取圖像服務，用於判斷是否啟用Q版人像功能
		$imageService = \R2\WpBaziPlugin\API\Clients\ClientFactory::getImageService();

		// 檢查是否啟用Q版人像功能
		if ($imageService !== null) {
			// 已啟用Q版人像功能
			// 先標記圖像生成狀態為"生成中"
			$result['qavatarStatus'] = 'generating';
			$result['qavatarUrl']    = '';

			// 啟動非阻塞的圖像生成過程
			$request_id                 = $this->triggerAvatarGeneration($bazi_data, $data);
			$result['qavatarRequestId'] = $request_id;
		} else {
			// 未啟用Q版人像功能
			$result['qavatarStatus']    = 'disabled';
			$result['qavatarUrl']       = '';
			$result['qavatarRequestId'] = '';
		}

		return [
			'success' => true,
			'data'    => $result,
		];
	}
}
