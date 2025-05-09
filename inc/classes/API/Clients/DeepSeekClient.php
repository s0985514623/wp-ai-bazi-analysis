<?php
/**
 * DeepSeek 客戶端
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API\Clients;

/**
 * DeepSeek 客戶端類
 * 處理 DeepSeek API 請求
 */
class DeepSeekClient extends BaseClient {

	/**
	 * 獲取實例（單例模式）
	 *
	 * @return DeepSeekClient
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

			// 準備API請求頭
			$headers = [
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->api_settings['api_key'],
			];

			// 發送請求到API
			$response = \wp_remote_post(
				$this->api_settings['api_url'],
				[
					'headers' => $headers,
					'body'    => json_encode($params),
					'timeout' => 60,
				]
			);
			// log response
			// error_log(print_r($response, true));
			if (is_wp_error($response)) {
				return [
					'success' => false,
					'error'   => $response->get_error_message(),
					'code'    => 500,
				];
			}

			$body   = \wp_remote_retrieve_body($response);
			$result = json_decode($body, true);

			if (!isset($result['choices'][0]['message']['content'])) {
				return [
					'success' => false,
					'error'   => '無效的API響應格式',
					'code'    => 500,
				];
			}

			return [
				'success' => true,
				'content' => $result['choices'][0]['message']['content'],
				'data'    => $result,
			];
		} catch (\Exception $e) {
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
		if (!$response['success']) {
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
		$result['apiProvider'] = 'deepseek';
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
