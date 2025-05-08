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
	 * 創建圖像生成請求
	 *
	 * @param string $prompt 提示文本
	 * @param array  $options 可選參數
	 * @return array 處理後的響應
	 */
	public function createImage( string $prompt, array $options = [] ) {
		try {
			// 合併默認選項和傳入的選項
			$params = array_merge(
				[
					'model'  => 'gpt-image-1',
					'prompt' => $prompt,
					'n'      => 1,
					'size'   => '1024x1024',
				// 'quality' => 'standard',
				// 'style'   => 'vivid',
				],
				$options
				);

			// 發送請求
			$response = $this->client->images()->create($params);
			// log response
			// error_log(print_r($response, true));
			// 返回響應內容
			if (isset($response->data[0]->url) && !empty($response->data[0]->url)) {
				return [
					'success'   => true,
					'image_url' => $response->data[0]->url,
					'data'      => $response->toArray(),
				];
			} elseif (isset($response->data[0]->b64_json)) {
				// 處理base64圖像數據
				$b64_data  = $response->data[0]->b64_json;
				$image_url = 'data:image/png;base64,' . $b64_data;
				return [
					'success'   => true,
					'image_url' => $image_url,
					'data'      => $response->toArray(),
				];
			} else {
				return [
					'success' => false,
					'error'   => '圖像生成結果格式不正確',
					'code'    => 500,
				];
			}
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
	 * 根據八字分析結果生成Q版人像
	 *
	 * @param array $baziData 八字分析結果
	 * @param array $userData 用戶基本信息
	 * @return array 處理後的響應（包含圖像URL）
	 */
	public function generateQAvatar( array $baziData, array $userData ) {
		$gender = $userData['gender'] === 'male' ? '男' : '女';
		$age    = date('Y') - intval($userData['year']);

		// 從分析結果提取特徵
		$personalityAnalysis = isset($baziData['personalityAnalysis']) ? $baziData['personalityAnalysis'] : '';
		$clothingSuggestions = isset($baziData['clothingSuggestions']) ? $baziData['clothingSuggestions'] : '';
		$dayMaster           = isset($baziData['dayMaster']) ? $baziData['dayMaster'] : '';
		$missingElements     = isset($baziData['missingElements']) ? $baziData['missingElements'] : '';
		$weakElements        = isset($baziData['weakElements']) ? $baziData['weakElements'] : '';
		$elementSuggestions  = isset($baziData['elementSuggestions']) ? $baziData['elementSuggestions'] : '';
		// 獲取五行特徵
		$elements = [
			'metal' => isset($baziData['fiveElements']['metal']) ? (int) $baziData['fiveElements']['metal'] : 0,
			'wood'  => isset($baziData['fiveElements']['wood']) ? (int) $baziData['fiveElements']['wood'] : 0,
			'water' => isset($baziData['fiveElements']['water']) ? (int) $baziData['fiveElements']['water'] : 0,
			'fire'  => isset($baziData['fiveElements']['fire']) ? (int) $baziData['fiveElements']['fire'] : 0,
			'earth' => isset($baziData['fiveElements']['earth']) ? (int) $baziData['fiveElements']['earth'] : 0,
		];

		// 確定主要五行元素（最多的）
		$dominant_element = array_search(max($elements), $elements);

		// 生成圖像提示詞
		$prompt  = "請生成一張純視覺圖像（不包含任何文字或標記），人物特徵為\n\n";
		$prompt .= "- {$gender}性，年齡約{$age}歲，Q版卡通風格，大頭小身體\n";
		$prompt .= "- 命格屬性：{$dayMaster}命\n";

		// 使用性格特徵指導人物設計
		$prompt .= "- 性格特徵：{$personalityAnalysis}\n";

		// 添加五行補充建議
		$prompt .= "- 服飾配飾補充建議內容：{$clothingSuggestions}\n";
		$prompt .= "- 請根據性格特徵以及服飾配飾補充建議內容，創建視覺表現\n";

		// 明確的風格和禁止指示
		$prompt .= "\n⚠️禁止內容（務必嚴格遵守）：\n";
		$prompt .= "1. ❌圖像中嚴禁出現任何形式的文字、數字、符號、標籤或裝飾字元\n";
		$prompt .= "2. ❌背景與人物周圍不得包含任何抽象形狀或圖案來代表五行\n";
		$prompt .= "3. ❌圖像四周絕對禁止添加邊框、裝飾性框線、圓圈、線條、角落圖案等標記元素\n";
		$prompt .= "4. ❌不要出現任何可辨識為文字的圖形圖案\n";
		$prompt .= "\n 背景應為單一純色或簡單漸層，不使用任何圖騰、紋理或抽象圖形\n";
		$prompt .= "- 構圖為正方形（1:1），人物居中，背景乾淨不干擾主體\n";
		$prompt .= "- 此圖像為純視覺素材，僅使用人物、服裝與色彩暗示五行特性，無任何標示\n";
		// log prompt
		// error_log(print_r($prompt, true));
		// 發送圖像生成請求
		return $this->createImage($prompt);
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

		// 先標記圖像生成狀態為"生成中"
		$result['qavatarStatus'] = 'generating';
		$result['qavatarUrl']    = '';

		// 啟動非阻塞的圖像生成過程
		$request_id                 = $this->triggerAvatarGeneration($bazi_data, $data);
		$result['qavatarRequestId'] = $request_id;

		return [
			'success' => true,
			'data'    => $result,
		];
	}

	/**
	 * 觸發非阻塞的圖像生成過程
	 * 使用WordPress的wp_schedule_single_event來非阻塞處理
	 *
	 * @param array $baziData 八字分析結果
	 * @param array $userData 用戶基本信息
	 */
	private function triggerAvatarGeneration( array $baziData, array $userData ) {
		// 生成唯一請求ID
		$request_id = md5(json_encode($userData) . time());

		// 將分析結果和請求ID存儲到臨時選項中
		update_option(
			'wp_bazi_avatar_request_' . $request_id,
			[
				'bazi_data'  => $baziData,
				'user_data'  => $userData,
				'status'     => 'pending',
				'created_at' => time(),
			]
			);

		// 使用WordPress計劃任務API立即處理（非阻塞）
		wp_schedule_single_event(time(), 'wp_bazi_generate_avatar', [ $request_id ]);

		// 確保計劃任務處理器已注冊
		if (! has_action('wp_bazi_generate_avatar')) {
			add_action('wp_bazi_generate_avatar', [ $this, 'processAvatarGeneration' ], 10, 1);
		}

		// 返回請求ID供前端使用
		return $request_id;
	}

	/**
	 * 處理圖像生成的回調函數
	 *
	 * @param string $request_id 請求ID
	 */
	public function processAvatarGeneration( $request_id ) {
		// 獲取存儲的分析結果和用戶數據
		$request_data = get_option('wp_bazi_avatar_request_' . $request_id);

		if (! $request_data || ! isset($request_data['bazi_data']) || ! isset($request_data['user_data'])) {
			return;
		}

		// 更新狀態為處理中
		$request_data['status'] = 'processing';
		update_option('wp_bazi_avatar_request_' . $request_id, $request_data);

		// 生成Q版頭像
		$image_response = $this->generateQAvatar($request_data['bazi_data'], $request_data['user_data']);
		// log response
		// error_log(print_r($image_response, true));

		// 處理結果
		if ($image_response['success']) {
			// 獲取圖片URL
			$image_url = $image_response['image_url'];

			// 創建自定義事件結果
			$result = [
				'request_id' => $request_id,
				'image_url'  => $image_url,
			];

			// 更新請求狀態
			$request_data['status']       = 'completed';
			$request_data['image_url']    = $image_url;
			$request_data['completed_at'] = time();
			update_option('wp_bazi_avatar_request_' . $request_id, $request_data);

			// 在WordPress數據庫中存儲結果，前端可以通過AJAX輪詢獲取
			update_option('wp_bazi_avatar_result_' . $request_id, $result);
		} else {
			// 處理失敗
			$request_data['status'] = 'failed';
			$request_data['error']  = $image_response['error'];
			update_option('wp_bazi_avatar_request_' . $request_id, $request_data);
		}
	}
}
