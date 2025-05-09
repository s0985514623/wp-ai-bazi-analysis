<?php
/**
 * 圖像生成服務
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API\Services;

use R2WpBaziPlugin_vendor_OpenAI as OpenAI;

/**
 * 圖像生成服務類
 * 處理不同模型的圖像生成請求
 */
class ImageGenerationService {
	/**
	 * API設置
	 *
	 * @var array
	 */
	private $api_settings;

	/**
	 * 構造函數
	 *
	 * @param array $api_settings API設置
	 */
	public function __construct( array $api_settings ) {
		$this->api_settings = $api_settings;
	}

	/**
	 * 創建圖像生成請求
	 *
	 * @param string $prompt 提示文本
	 * @param array  $options 可選參數
	 * @return array 處理後的響應
	 */
	public function createImage( string $prompt, array $options = [] ) {
		// 使用特定的圖像API密鑰，如果未設置則使用主API密鑰
		$api_key = !empty($this->api_settings['qavatar_api_key']) ?
		$this->api_settings['qavatar_api_key'] :
		$this->api_settings['api_key'];

		// 創建臨時OpenAI客戶端進行圖像生成
		$client = OpenAI::client($api_key);

		try {
			// 獲取圖像生成模型
			$model = !empty($options['model']) ? $options['model'] :
			( !empty($this->api_settings['qavatar_model']) ?
			$this->api_settings['qavatar_model'] : 'dall-e-3' );

			// 合併默認選項和傳入的選項
			$params = array_merge(
				[
					'model'  => $model,
					'prompt' => $prompt,
					'n'      => 1,
					'size'   => '1024x1024',
				],
				$options
			);

			// 添加DALL-E模型特有的參數
			if ($model === 'dall-e-3' && !isset($params['quality'])) {
				$params['quality'] = 'hd';
				$params['style']   = 'vivid';
			}

			// 發送請求
			$response = $client->images()->create($params);

			// 處理響應
			if (isset($response->data[0]->url) && !empty($response->data[0]->url)) {
				// 將圖片保存到WordPress媒體庫
				$media_id = $this->save_image_to_media_library($response->data[0]->url, 'qavatar');

				if ($media_id) {
					// 成功保存到媒體庫
					$media_url = wp_get_attachment_url($media_id);
					return [
						'success'      => true,
						'image_url'    => $media_url,
						'media_id'     => $media_id,
						'original_url' => $response->data[0]->url,
						'data'         => $response->toArray(),
					];
				} else {
					// 保存失敗，返回原始URL
					return [
						'success'     => true,
						'image_url'   => $response->data[0]->url,
						'data'        => $response->toArray(),
						'media_error' => '無法將圖片保存到媒體庫',
					];
				}
			} elseif (isset($response->data[0]->b64_json)) {
				// 處理base64圖像數據
				$b64_data = $response->data[0]->b64_json;

				// 將base64圖像保存到媒體庫
				$media_id = $this->save_base64_to_media_library($b64_data, 'qavatar-b64');

				if ($media_id) {
					// 成功保存到媒體庫
					$media_url = wp_get_attachment_url($media_id);
					return [
						'success'   => true,
						'image_url' => $media_url,
						'media_id'  => $media_id,
						'data'      => $response->toArray(),
					];
				} else {
					// 保存失敗，返回原始base64
					$image_url = 'data:image/png;base64,' . $b64_data;
					return [
						'success'     => true,
						'image_url'   => $image_url,
						'data'        => $response->toArray(),
						'media_error' => '無法將base64圖片保存到媒體庫',
					];
				}
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
	 * 從URL下載並保存圖片到WordPress媒體庫
	 *
	 * @param string $image_url 圖片URL
	 * @param string $title 圖片標題前綴
	 * @return int|false 成功返回媒體ID，失敗返回false
	 */
	private function save_image_to_media_library( $image_url, $title = 'qavatar' ) {
		// 確保WordPress文件系統可用
		if (!function_exists('wp_upload_bits') || !function_exists('wp_handle_sideload')) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		if (!function_exists('wp_insert_attachment')) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		try {
			// 下載圖片
			$response = wp_remote_get($image_url);

			if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
				return false;
			}

			$image_data = wp_remote_retrieve_body($response);

			// 創建臨時文件
			$filename = $title . '-' . time() . '.png';
			$upload   = wp_upload_bits($filename, null, $image_data);

			if ($upload['error']) {
				return false;
			}

			// 準備文件信息
			$file_path = $upload['file'];
			$file_type = wp_check_filetype($filename, null);

			$attachment = [
				'post_mime_type' => $file_type['type'],
				'post_title'     => sanitize_file_name($title . '-' . date('YmdHis')),
				'post_content'   => '',
				'post_status'    => 'inherit',
			];

			// 插入附件
			$attach_id = wp_insert_attachment($attachment, $file_path);

			if (is_wp_error($attach_id)) {
				return false;
			}

			// 生成附件元數據
			$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
			wp_update_attachment_metadata($attach_id, $attach_data);

			return $attach_id;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 將base64圖像數據保存到WordPress媒體庫
	 *
	 * @param string $base64_data Base64編碼的圖像數據
	 * @param string $title 圖片標題前綴
	 * @return int|false 成功返回媒體ID，失敗返回false
	 */
	private function save_base64_to_media_library( $base64_data, $title = 'qavatar' ) {
		// 確保WordPress文件系統可用
		if (!function_exists('wp_upload_bits') || !function_exists('wp_handle_sideload')) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if (!function_exists('wp_generate_attachment_metadata')) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		if (!function_exists('wp_insert_attachment')) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		try {
			// 解碼base64數據
			$decoded_data = base64_decode($base64_data);

			// 創建臨時文件
			$filename = $title . '-' . time() . '.png';
			$upload   = wp_upload_bits($filename, null, $decoded_data);

			if ($upload['error']) {
				return false;
			}

			// 準備文件信息
			$file_path = $upload['file'];
			$file_type = wp_check_filetype($filename, null);

			$attachment = [
				'post_mime_type' => $file_type['type'],
				'post_title'     => sanitize_file_name($title . '-' . date('YmdHis')),
				'post_content'   => '',
				'post_status'    => 'inherit',
			];

			// 插入附件
			$attach_id = wp_insert_attachment($attachment, $file_path);

			if (is_wp_error($attach_id)) {
				return false;
			}

			// 生成附件元數據
			$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
			wp_update_attachment_metadata($attach_id, $attach_data);

			return $attach_id;
		} catch (\Exception $e) {
			return false;
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

		// 生成圖像提示詞
		$prompt  = "請生成一張純視覺圖像（不包含任何文字或標記），人物特徵為\n\n";
		$prompt .= "- {$gender}性，年齡約{$age}歲，Q版卡通風格，大頭小身體，表情保持微笑\n";
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

		// 使用當前設置的模型進行圖像生成
		return $this->createImage($prompt);
	}

	/**
	 * 處理圖像生成的回調函數
	 * 由WordPress計劃任務調用
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

		// 處理結果
		if ($image_response['success']) {
			// 獲取圖片URL - 現在應該是已保存到媒體庫的URL
			$image_url = $image_response['image_url'];

			// 創建自定義事件結果
			$result = [
				'request_id' => $request_id,
				'image_url'  => $image_url,
			];

			// 若有媒體ID，也保存
			if (isset($image_response['media_id'])) {
				$result['media_id'] = $image_response['media_id'];
			}

			// 更新請求狀態
			$request_data['status']       = 'completed';
			$request_data['image_url']    = $image_url;
			$request_data['completed_at'] = time();

			// 保存媒體庫ID (如果有)
			if (isset($image_response['media_id'])) {
				$request_data['media_id'] = $image_response['media_id'];
			}

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
