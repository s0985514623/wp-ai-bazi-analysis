<?php
/**
 * Ai Chat Api Register
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API;

/**
 * Class AiChat
 */
class AiChat {
	use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

	public function __construct() {
		\add_action('rest_api_init', [ $this, 'register_routes' ]);
	}

	public function register_routes() {
		\register_rest_route(
			'bft/v1',
			'/analyze',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'handle_bazi_analysis' ],
				'permission_callback' => '__return_true',
			]
			);
	}

	/**
	 * 处理八字命格分析请求
	 */
	public function handle_bazi_analysis( $request ) {
		$data    = $request->get_json_params();
		$api_key = 'sk-996a8b520bbb40a191eb9e30e632e22b'; // 从环境变量或配置中获取
		$api_url = 'https://api.deepseek.com/chat/completions';

		// 获取表单数据
		$gender        = $data['gender'] === 'male' ? '男' : '女';
		$year          = intval($data['year']);
		$month         = intval($data['month']);
		$day           = intval($data['day']);
		$hour          = $data['hour'];
		$city          = sanitize_text_field($data['city']);
		$country       = sanitize_text_field($data['country']);
		$analysis_type = sanitize_text_field($data['analysis_type']);

		// 获取中文时辰
		$chinese_hour = '';
		if ($hour !== 'unknown') {
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
			$chinese_hour  = $chinese_hours[ intval($hour) ];
		}

		// 构建发送到DeepSeek API的提示
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
		$prompt .= "5. 日主及其強弱\n";

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

		$prompt .= "7. 五行補充建議（針對缺失或偏弱的五行，提供補充建議，包括顏色、方位、飲食等方面的調整建議）\n";
		$prompt .= "8. 格式化為JSON，包含以下字段：\n";
		$prompt .= "   - bazi: {year: {heavenly: \"天干\", earthly: \"地支\"}, month: {heavenly: \"天干\", earthly: \"地支\"}, day: {heavenly: \"天干\", earthly: \"地支\"}, hour: {heavenly: \"天干\", earthly: \"地支\"}}\n";
		$prompt .= "   - fiveElements: {metal: 數量, wood: 數量, water: 數量, fire: 數量, earth: 數量}\n";
		$prompt .= "   - missingElements: \"缺失五行\"\n";
		$prompt .= "   - weakElements: \"偏弱五行\"\n";
		$prompt .= "   - dayMaster: \"日主\"\n";
		// $prompt .= "   - dayMasterStrength: \"強弱程度\"\n";
		$prompt .= "   - personalityAnalysis: \"分析內容\"\n";
		$prompt .= "   - elementSuggestions: \"五行補充建議內容\"\n";

		// 准备DeepSeek API请求数据
		$request_data = [
			'model'       => 'deepseek-chat',
			'messages'    => [
				[
					'role'    => 'user',
					'content' => $prompt,
				],
			],
			'temperature' => 0.7,
			'max_tokens'  => 2000,
		];
		// error log
		\error_log(print_r($request_data, true));
		// 发送请求到DeepSeek API
		$response = \wp_remote_post(
			$api_url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				],
				'body'    => json_encode($request_data),
				'timeout' => 60,
			]
			);

		if (is_wp_error($response)) {
			return new \WP_Error('api_error', 'API请求失败', [ 'status' => 500 ]);
		}
		// error log
		\error_log(print_r($response, true));
		$body   = \wp_remote_retrieve_body($response);
		$result = json_decode($body, true);

		// 处理DeepSeek API返回的结果
		try {
			$content = $result['choices'][0]['message']['content'];

			// 尝试从返回结果中提取JSON
			$json_match = preg_match('/```json\n([\s\S]*?)\n```/', $content, $matches) ||
			preg_match('/```\n([\s\S]*?)\n```/', $content, $matches) ||
			preg_match('/{[\s\S]*?}/', $content, $matches);

			if ($json_match) {
				$bazi_data = json_decode(preg_replace('/```json\n|```\n|```/', '', $matches[0]), true);

				// 確保分析結果為字符串格式
				if (isset($bazi_data['personalityAnalysis']) && is_array($bazi_data['personalityAnalysis'])) {
					$bazi_data['personalityAnalysis'] = implode("\n", $bazi_data['personalityAnalysis']);
				}

				if (isset($bazi_data['elementSuggestions']) && is_array($bazi_data['elementSuggestions'])) {
					$bazi_data['elementSuggestions'] = implode("\n", $bazi_data['elementSuggestions']);
				}

				// 確保必要字段都存在，即使為空
				$bazi_data = array_merge(
					[
						'bazi'                => [
							'year'  => [
								'heavenly' => '',
								'earthly'  => '',
							],
							'month' => [
								'heavenly' => '',
								'earthly'  => '',
							],
							'day'   => [
								'heavenly' => '',
								'earthly'  => '',
							],
							'hour'  => [
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
					],
					$bazi_data
					);
			} else {
				// 如果没有找到JSON格式，返回错误
				return new \WP_Error('parse_error', '无法解析分析结果', [ 'status' => 500 ]);
			}

			// 合并用户数据和分析结果
			$response_data = array_merge($data, $bazi_data);

			// 根据缺失和偏弱的五行获取相关商品
			$missing_elements = $bazi_data['missingElements'] ?? '';
			$weak_elements    = $bazi_data['weakElements'] ?? '';

			// 获取相关商品
			$related_products                 = $this->get_five_element_products($missing_elements, $weak_elements);
			$response_data['relatedProducts'] = $related_products;

			return \rest_ensure_response($response_data);
		} catch (\Exception $e) {
			return new \WP_Error('parse_error', '处理分析结果时出错: ' . $e->getMessage(), [ 'status' => 500 ]);
		}
	}

	/**
	 * 根据五行获取相关产品
	 */
	private function get_five_element_products( $missing_elements, $weak_elements ) {
		// 将中文五行转换为英文标识
		$elements_map = [
			'金' => 'metal',
			'木' => 'wood',
			'水' => 'water',
			'火' => 'fire',
			'土' => 'earth',
		];

		// 解析缺失和偏弱的五行
		$elements_to_find = [];

		// 处理缺失的五行
		if (!empty($missing_elements)) {
			$missing_array = preg_split('/[,、，]/', $missing_elements);
			foreach ($missing_array as $element) {
				$element = trim($element);
				if (isset($elements_map[ $element ])) {
					$elements_to_find[] = $elements_map[ $element ];
				}
			}
		}

		// 处理偏弱的五行
		if (!empty($weak_elements)) {
			$weak_array = preg_split('/[,、，]/', $weak_elements);
			foreach ($weak_array as $element) {
				$element = trim($element);
				if (isset($elements_map[ $element ]) && !in_array($elements_map[ $element ], $elements_to_find)) {
					$elements_to_find[] = $elements_map[ $element ];
				}
			}
		}

		// 如果没有找到需要的五行，返回空
		if (empty($elements_to_find)) {
			return [];
		}

		// 根據五行獲取對應的產品
		$products = [];

		if (count($elements_to_find) === 1) {
			// 如果只有一個五行，用精確的稅務查詢
			$element = $elements_to_find[0];
			$args    = [
				'post_type'      => 'product',
				'posts_per_page' => 20,
				'post_status'    => 'publish',
				'tax_query'      => [
					[
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $element,
						'operator' => 'IN',
					],
					'relation' => 'AND', // 確保下面的排除條件同時滿足
					[
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => array_diff([ 'metal', 'wood', 'water', 'fire', 'earth' ], [ $element ]),
						'operator' => 'NOT IN', // 排除其他五行分類
					],
				],
			];

			// 執行查詢，正好只獲取符合單一五行的商品
			$query = new \WP_Query($args);

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$product_id = \get_the_ID();
					$products[] = $this->create_product_data($product_id, [ $element ]);
				}
				\wp_reset_postdata();
			}
		} else {
			// 多五行的情況較為複雜，先獲取相關商品再進行精確篩選
			$args = [
				'post_type'      => 'product',
				'posts_per_page' => 30,
				'post_status'    => 'publish',
				'tax_query'      => [
					[
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $elements_to_find,
						'operator' => 'AND', // 必須同時包含所有指定的五行
					],
				],
			];

			$query = new \WP_Query($args);

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$product_id = \get_the_ID();

					// 獲取產品的分類標籤
					$product_elements = $this->get_product_element_slugs($product_id);

					// 產品不能包含不需要的五行
					if (count($product_elements) === count($elements_to_find)) {
						$products[] = $this->create_product_data($product_id, $product_elements);
					}
				}
				\wp_reset_postdata();
			}
		}

		return $products;
	}

	/**
	 * 获取产品的五行分类标识（英文slug）
	 */
	private function get_product_element_slugs( $product_id ) {
		$element_slugs    = [ 'metal', 'wood', 'water', 'fire', 'earth' ];
		$product_elements = [];

		$terms = \get_the_terms($product_id, 'product_cat');

		if ($terms && !is_wp_error($terms)) {
			foreach ($terms as $term) {
				if (in_array($term->slug, $element_slugs)) {
					$product_elements[] = $term->slug;
				}
			}
		}

		return $product_elements;
	}

	/**
	 * 创建产品数据
	 */
	private function create_product_data( $product_id, $element_slugs ) {
		$element_map = [
			'metal' => '金',
			'wood'  => '木',
			'water' => '水',
			'fire'  => '火',
			'earth' => '土',
		];

		$element_names = [];
		foreach ($element_slugs as $slug) {
			if (isset($element_map[ $slug ])) {
				$element_names[] = $element_map[ $slug ];
			}
		}

		$price = \get_post_meta($product_id, '_price', true);
		if (is_numeric($price)) {
			$price = 'NT$' . number_format( (float) $price, 0, '.', ',');
		}

		return [
			'id'          => (string) $product_id,
			'name'        => \get_the_title(),
			'description' => \get_the_excerpt(),
			'price'       => $price,
			'image'       => \get_the_post_thumbnail_url($product_id, 'medium') ?: 'https://via.placeholder.com/300x200?text=商品圖片',
			'url'         => \get_permalink($product_id),
			'elements'    => $element_names,
		];
	}
}
