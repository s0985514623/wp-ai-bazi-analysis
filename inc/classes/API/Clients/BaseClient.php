<?php
/**
 * API 客戶端抽象基類
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API\Clients;

use R2\WpBaziPlugin\Admin\ApiSettings;

/**
 * BaseClient 抽象類
 * 所有API客戶端的基礎實現
 */
abstract class BaseClient {
	/**
	 * API設置
	 *
	 * @var array
	 */
	protected $api_settings;

	/**
	 * 構造函數
	 */
	public function __construct() {
		$this->api_settings = ApiSettings::get_api_settings();
	}

	/**
	 * 創建聊天完成請求
	 *
	 * @param string $prompt 提示文本
	 * @param array  $options 可選參數
	 * @return array 處理後的響應
	 */
	abstract public function createChatCompletion( string $prompt, array $options = [] );

	/**
	 * 處理八字分析請求
	 *
	 * @param array $data 用戶提交的數據
	 * @return array 處理後的響應
	 */
	abstract public function analyzeBazi( array $data );

	/**
	 * 構建八字分析提示文本
	 *
	 * @param string $gender 性別
	 * @param int    $year 年
	 * @param int    $month 月
	 * @param int    $day 日
	 * @param string $hour 時辰代碼
	 * @param string $chinese_hour 中文時辰
	 * @param string $city 城市
	 * @param string $country 國家
	 * @param string $analysis_type 分析類型
	 * @return string 提示文本
	 */
	protected function buildBaziPrompt(
		string $gender,
		int $year,
		int $month,
		int $day,
		string $hour,
		string $chinese_hour,
		string $city,
		string $country,
		string $analysis_type
	) {
		$prompt  = "請根據以下八字資料進行命格分析：\n";
		$prompt .= "性別：{$gender}\n";
		$prompt .= "生年：{$year}\n";
		$prompt .= "生月：{$month}\n";
		$prompt .= "生日：{$day}\n";

		if ($hour !== 'unknown') {
			$prompt .= "生時：{$chinese_hour}\n";
		} else {
			$prompt .= "生時：不詳\n";
		}

		$prompt .= "出生地：{$city}, {$country}\n";
		$prompt .= '分析項目：';

		switch ($analysis_type) {
			case 'general':
				$prompt .= "總體運勢\n\n";
				break;
			case 'love':
				$prompt .= "感情姻緣\n\n";
				break;
			case 'career':
				$prompt .= "工作事業\n\n";
				break;
			case 'wealth':
				$prompt .= "財運\n\n";
				break;
			case 'health':
				$prompt .= "健康\n\n";
				break;
			default:
				$prompt .= "總體運勢\n\n";
		}

		$prompt .= "請提供以下信息：\n";
		$prompt .= '1. 完整的八字（年柱、月柱、日柱、時柱的天干地支）';
		if ($hour === 'unknown') {
			$prompt .= "，由於時辰不詳，請根據年月日推算可能的命盤\n";
		} else {
			$prompt .= "\n";
		}
		$prompt .= "2. 五行分佈（金、木、水、火、土各自的數量）\n";
		$prompt .= "3. 缺失的五行\n";
		$prompt .= "4. 偏弱的五行（不缺失但需要補充的五行）\n";
		$prompt .= "5. 日主\n";

		switch ($analysis_type) {
			case 'general':
				$prompt .= "6. 整體性格分析與運勢\n";
				break;
			case 'love':
				$prompt .= "6. 感情姻緣方面的詳細分析\n";
				break;
			case 'career':
				$prompt .= "6. 工作事業方面的詳細分析\n";
				break;
			case 'wealth':
				$prompt .= "6. 財運方面的詳細分析\n";
				break;
			case 'health':
				$prompt .= "6. 健康方面的詳細分析\n";
				break;
			default:
				$prompt .= "6. 整體性格分析與運勢\n";
		}

		$prompt .= "7. 五行補充建議（針對缺失或偏弱的五行，提供補充建議，包括顏色、方位、飲食等方面的調整建議）\n\n";
		$prompt .= "8. 服飾配飾補充建議（針對缺失或偏弱的五行，提供服飾或配飾的調整建議）\n\n";

		$prompt .= "【重要提示】請將以上所有分析結果（1-7點）都統一以JSON格式回覆，不要有任何其他格式的文本說明。你的回應必須是一個有效的JSON對象，包含以下字段：\n";
		$prompt .= "```json\n";
		$prompt .= "{\n";
		$prompt .= "  \"bazi\": {\n";
		$prompt .= "    \"year\": {\"heavenly\": \"天干\", \"earthly\": \"地支\"},\n";
		$prompt .= "    \"month\": {\"heavenly\": \"天干\", \"earthly\": \"地支\"},\n";
		$prompt .= "    \"day\": {\"heavenly\": \"天干\", \"earthly\": \"地支\"},\n";
		$prompt .= "    \"hour\": {\"heavenly\": \"天干\", \"earthly\": \"地支\"}\n";
		$prompt .= "  },\n";
		$prompt .= "  \"fiveElements\": {\"metal\": 數量, \"wood\": 數量, \"water\": 數量, \"fire\": 數量, \"earth\": 數量},\n";
		$prompt .= "  \"missingElements\": \"缺失五行\",\n";
		$prompt .= "  \"weakElements\": \"偏弱五行\",\n";
		$prompt .= "  \"dayMaster\": \"日主\",\n";
		// $prompt .= "  \"dayMasterStrength\": \"日主強弱程度\",\n";
		$prompt .= "  \"personalityAnalysis\": \"分析內容\",\n";
		$prompt .= "  \"elementSuggestions\": \"五行補充建議內容\"\n";
		$prompt .= "  \"clothingSuggestions\": \"服飾配飾補充建議內容\"\n";
		$prompt .= "}\n";
		$prompt .= "```\n";
		$prompt .= '請確保所有分析內容都填入對應的JSON字段，不要在JSON之外提供任何解釋或文本內容。';

		return $prompt;
	}

	/**
	 * 解析JSON格式的響應
	 *
	 * @param string $content API響應內容
	 * @return array|null 解析後的數據或null（解析失敗）
	 */
	protected function parseJsonResponse( string $content ) {
		// 嘗試從返回結果中提取JSON
		$json_match = preg_match('/```json\n([\s\S]*?)\n```/', $content, $matches) ||
		preg_match('/```\n([\s\S]*?)\n```/', $content, $matches) ||
		preg_match('/{[\s\S]*?}/', $content, $matches);

		if (!$json_match) {
			return null;
		}

		// 清理並解析JSON
		$bazi_data = json_decode(preg_replace('/```json\n|```\n|```/', '', $matches[0]), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			return null;
		}

		// 確保分析結果為字符串格式
		if (isset($bazi_data['personalityAnalysis']) && is_array($bazi_data['personalityAnalysis'])) {
			$bazi_data['personalityAnalysis'] = implode("\n", $bazi_data['personalityAnalysis']);
		}

		if (isset($bazi_data['elementSuggestions']) && is_array($bazi_data['elementSuggestions'])) {
			$bazi_data['elementSuggestions'] = implode("\n", $bazi_data['elementSuggestions']);
		}

		// 確保必要字段都存在，即使為空
		return array_merge(
			[
				'bazi'                => [
					'year' => [
						'heavenly' => '',
						'earthly'  => '',
					],
					'month' => [
						'heavenly' => '',
						'earthly'  => '',
					],
					'day' => [
						'heavenly' => '',
						'earthly'  => '',
					],
					'hour' => [
						'heavenly' => '',
						'earthly'  => '',
					],
				],
				'fiveElements'        => [
					'metal' => 0,
					'wood'  => 0,
					'water' => 0,
					'fire'  => 0,
					'earth' => 0,
				],
				'dayMaster'           => '',
				'dayMasterStrength'   => '',
				'missingElements'     => '',
				'weakElements'        => '',
				'personalityAnalysis' => '',
				'elementSuggestions'  => '',
				'clothingSuggestions' => '',
			],
			$bazi_data
		);
	}

	/**
	 * 獲取中文時辰
	 *
	 * @param string $hour 時辰代碼
	 * @return string 中文時辰
	 */
	protected function getChineseHour( string $hour ) {
		if ($hour === 'unknown') {
			return '';
		}

		$chinese_hours = [
			'子時 (23:00-1:00)',
			'丑時 (1:00-3:00)',
			'寅時 (3:00-5:00)',
			'卯時 (5:00-7:00)',
			'辰時 (7:00-9:00)',
			'巳時 (9:00-11:00)',
			'午時 (11:00-13:00)',
			'未時 (13:00-15:00)',
			'申時 (15:00-17:00)',
			'酉時 (17:00-19:00)',
			'戌時 (19:00-21:00)',
			'亥時 (21:00-23:00)',
		];

		return $chinese_hours[ intval($hour) ];
	}

	/**
	 * 觸發非阻塞的圖像生成過程
	 * 使用WordPress的wp_schedule_single_event來非阻塞處理
	 *
	 * @param array $baziData 八字分析結果
	 * @param array $userData 用戶基本信息
	 * @return string 生成的請求ID
	 */
	public function triggerAvatarGeneration( array $baziData, array $userData ) {
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
		// 鉤子的註冊已在AiChat類的register_hooks方法中集中處理
		wp_schedule_single_event(time(), 'wp_bazi_generate_avatar', [ $request_id ]);

		// 返回請求ID供前端使用
		return $request_id;
	}
}
