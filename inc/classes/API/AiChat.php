<?php
/**
 * Ai Chat Api Register
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\API;

use R2\WpBaziPlugin\Admin\ApiSettings;
use R2\WpBaziPlugin\API\Clients\ClientFactory;

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
	 * 處理八字命格分析請求
	 */
	public function handle_bazi_analysis( $request ) {
		$data = $request->get_json_params();

		try {
			// 獲取客戶端實例
			$client = ClientFactory::createClient();

			// 處理八字分析
			$response = $client->analyzeBazi($data);

			// 處理響應
			if (!$response['success']) {
				return new \WP_Error(
					'api_error',
					$response['error'] ?? '處理API請求時出錯',
					[ 'status' => $response['code'] ?? 500 ]
				);
			}

			// 獲取分析結果
			$result = $response['data'];

			// 獲取相關商品
			$missing_elements = $result['missingElements'] ?? '';
			$weak_elements    = $result['weakElements'] ?? '';

			// 添加相關商品到結果
			$result['relatedProducts'] = $this->get_five_element_products($missing_elements, $weak_elements);

			return \rest_ensure_response($result);
		} catch (\Exception $e) {
			return new \WP_Error(
				'api_client_error',
				'處理API請求時出錯: ' . $e->getMessage(),
				[ 'status' => 500 ]
			);
		}
	}

	/**
	 * 根據五行獲取相關產品
	 */
	private function get_five_element_products( $missing_elements, $weak_elements ) {
		// 將中文五行轉換為英文標識
		$elements_map = [
			'金' => 'metal',
			'木' => 'wood',
			'水' => 'water',
			'火' => 'fire',
			'土' => 'earth',
		];

		// 解析缺失和偏弱的五行
		$elements_to_find = [];

		// 處理缺失的五行
		if (!empty($missing_elements)) {
			$missing_array = preg_split('/[,、，]/', $missing_elements);
			foreach ($missing_array as $element) {
				$element = trim($element);
				if (isset($elements_map[ $element ])) {
					$elements_to_find[] = $elements_map[ $element ];
				}
			}
		}

		// 處理偏弱的五行
		if (!empty($weak_elements)) {
			$weak_array = preg_split('/[,、，]/', $weak_elements);
			foreach ($weak_array as $element) {
				$element = trim($element);
				if (isset($elements_map[ $element ]) && !in_array($elements_map[ $element ], $elements_to_find)) {
					$elements_to_find[] = $elements_map[ $element ];
				}
			}
		}

		// 如果沒有找到需要的五行，返回空
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
	 * 獲取產品的五行分類標識（英文slug）
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
	 * 創建產品數據
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
