<?php
/**
 * API設置頁面
 */

declare(strict_types=1);

namespace R2\WpBaziPlugin\Admin;

/**
 * Class ApiSettings
 */
class ApiSettings {
	use \R2WpBaziPlugin\vendor\J7\WpUtils\Traits\SingletonTrait;

	/**
	 * 設置頁面的選項名稱
	 */
	const OPTION_NAME = 'wp_bazi_api_settings';

	/**
	 * 初始化
	 */
	public function __construct() {
		add_action('admin_menu', [ $this, 'add_admin_menu' ]);
		add_action('admin_init', [ $this, 'register_settings' ]);
	}

	/**
	 * 添加菜單頁面
	 */
	public function add_admin_menu() {
		// 添加主菜單
		add_menu_page(
			'八字命格分析設定', // 頁面標題
			'八字命格設定', // 菜單標題
			'manage_options', // 權限
			'wp-bazi-api-settings', // 菜單slug
			[ $this, 'render_settings_page' ], // 回調函數
			'dashicons-chart-pie', // 圖標
			30 // 菜單位置
		);

		// 添加子菜單 - API設定（與主菜單相同的頁面）
		add_submenu_page(
			'wp-bazi-api-settings', // 父菜單slug
			'API設定', // 頁面標題
			'API設定', // 菜單標題
			'manage_options', // 權限
			'wp-bazi-api-settings', // 菜單slug（與主菜單相同）
			[ $this, 'render_settings_page' ] // 回調函數
		);

		// 添加子菜單 - 使用說明
		add_submenu_page(
			'wp-bazi-api-settings', // 父菜單slug
			'使用說明', // 頁面標題
			'使用說明', // 菜單標題
			'manage_options', // 權限
			'wp-bazi-usage-guide', // 菜單slug
			[ $this, 'render_usage_guide_page' ] // 回調函數
		);
	}

	/**
	 * 註冊設置
	 */
	public function register_settings() {
		register_setting(
			'wp_bazi_api_settings_group',
			self::OPTION_NAME,
			[ $this, 'sanitize_settings' ]
		);

		// API配置部分
		add_settings_section(
			'wp_bazi_api_main_section',
			'API配置',
			[ $this, 'render_section_description' ],
			'wp-bazi-api-settings'
		);

		add_settings_field(
			'api_key',
			'API密鑰',
			[ $this, 'render_api_key_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_api_main_section'
		);

		add_settings_field(
			'api_url',
			'API地址',
			[ $this, 'render_api_url_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_api_main_section'
		);

		add_settings_field(
			'api_provider',
			'API提供者',
			[ $this, 'render_api_provider_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_api_main_section'
		);

		add_settings_field(
			'api_model',
			'API模型',
			[ $this, 'render_api_model_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_api_main_section'
		);

		// Q版人像相關設置
		add_settings_section(
			'wp_bazi_qavatar_section',
			'命格Q版形象設置',
			[ $this, 'render_qavatar_section_description' ],
			'wp-bazi-api-settings'
		);

		add_settings_field(
			'enable_qavatar',
			'啟用命格Q版形象',
			[ $this, 'render_enable_qavatar_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_qavatar_section'
		);

		add_settings_field(
			'qavatar_api_key',
			'圖像API密鑰',
			[ $this, 'render_qavatar_api_key_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_qavatar_section'
		);

		add_settings_field(
			'qavatar_model',
			'圖像生成模型',
			[ $this, 'render_qavatar_model_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_qavatar_section'
		);

		// 高級設置部分
		add_settings_section(
			'wp_bazi_advanced_section',
			'高級設置',
			[ $this, 'render_advanced_section_description' ],
			'wp-bazi-api-settings'
		);

		add_settings_field(
			'temperature',
			'溫度參數',
			[ $this, 'render_temperature_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_advanced_section'
		);

		add_settings_field(
			'max_tokens',
			'最大生成長度',
			[ $this, 'render_max_tokens_field' ],
			'wp-bazi-api-settings',
			'wp_bazi_advanced_section'
		);
	}

	/**
	 * 驗證和清理設置數據
	 */
	public function sanitize_settings( $input ) {
		$output = [];

		if (isset($input['api_key'])) {
			$output['api_key'] = sanitize_text_field($input['api_key']);
		}

		if (isset($input['api_url'])) {
			$output['api_url'] = esc_url_raw($input['api_url']);
		}

		if (isset($input['api_provider'])) {
			$output['api_provider'] = sanitize_text_field($input['api_provider']);
		}

		if (isset($input['api_model'])) {
			$output['api_model'] = sanitize_text_field($input['api_model']);
		}

		// Q版人像相關設置
		if (isset($input['enable_qavatar'])) {
			$output['enable_qavatar'] = sanitize_text_field($input['enable_qavatar']);
		}

		if (isset($input['qavatar_api_key'])) {
			$output['qavatar_api_key'] = sanitize_text_field($input['qavatar_api_key']);
		}

		if (isset($input['qavatar_model'])) {
			$output['qavatar_model'] = sanitize_text_field($input['qavatar_model']);
		}

		// 高級設置
		if (isset($input['temperature'])) {
			$temperature           = (float) $input['temperature'];
			$output['temperature'] = min(max(0, $temperature), 1); // 確保在0-1之間
		}

		if (isset($input['max_tokens'])) {
			$max_tokens           = (int) $input['max_tokens'];
			$output['max_tokens'] = min(max(100, $max_tokens), 4000); // 確保在100-4000之間
		}

		return $output;
	}

	/**
	 * 渲染設置頁面描述
	 */
	public function render_section_description() {
		echo '<p>配置八字命格分析API的主要設置項。請確保輸入正確的API密鑰和地址。</p>';
	}

	/**
	 * 渲染高級設置頁面描述
	 */
	public function render_advanced_section_description() {
		echo '<p>高級API參數配置，僅在需要微調API行為時修改。</p>';
	}

	/**
	 * 渲染API密鑰字段
	 */
	public function render_api_key_field() {
		$options = get_option(self::OPTION_NAME);
		$api_key = isset($options['api_key']) ? $options['api_key'] : '';

		echo '<input type="text" name="' . self::OPTION_NAME . '[api_key]" value="' . esc_attr($api_key) . '" class="regular-text" />';
		echo '<p class="description">輸入您的API密鑰（如：sk-xxxxxxxx）</p>';
	}

	/**
	 * 渲染API地址字段
	 */
	public function render_api_url_field() {
		$options = get_option(self::OPTION_NAME);
		$api_url = isset($options['api_url']) ? $options['api_url'] : 'https://api.deepseek.com/chat/completions';

		echo '<input type="url" name="' . self::OPTION_NAME . '[api_url]" value="' . esc_attr($api_url) . '" class="regular-text" />';
		echo '<p class="description">API請求地址，默認為：https://api.deepseek.com/chat/completions</p>';
	}

	/**
	 * 渲染API提供者字段
	 */
	public function render_api_provider_field() {
		$options      = get_option(self::OPTION_NAME);
		$api_provider = isset($options['api_provider']) ? $options['api_provider'] : 'deepseek';
		?>
		<select name="<?php echo self::OPTION_NAME; ?>[api_provider]" id="api_provider" onchange="updateModelOptions()">
			<option value="deepseek" <?php selected($api_provider, 'deepseek'); ?>>DeepSeek</option>
			<option value="openai" <?php selected($api_provider, 'openai'); ?>>OpenAI</option>
		</select>
		<p class="description">選擇API提供者，影響模型選擇和API格式</p>
		
		<script>
		function updateModelOptions() {
			const provider = document.getElementById('api_provider').value;
			const modelSelect = document.getElementById('api_model');
			const urlInput = document.querySelector('input[name="<?php echo self::OPTION_NAME; ?>[api_url]"]');
			
			// 保存當前選中的模型值
			const currentSelectedModel = modelSelect.value;
			
			// 清空現有選項
			modelSelect.innerHTML = '';
			
			if (provider === 'deepseek') {
				urlInput.value = 'https://api.deepseek.com/chat/completions';
				
				const deepseekModels = [
					{value: 'deepseek-chat', text: 'DeepSeek Chat'},
					{value: 'deepseek-coder', text: 'DeepSeek Coder'}
				];
				
				deepseekModels.forEach(model => {
					const option = document.createElement('option');
					option.value = model.value;
					option.text = model.text;
					// 如果是之前選中的模型，設置selected屬性
					if (currentSelectedModel === model.value) {
						option.selected = true;
					}
					modelSelect.appendChild(option);
				});
				
				// 如果當前選中的不在列表中，選擇默認值
				if (!deepseekModels.some(model => model.value === currentSelectedModel)) {
					modelSelect.value = 'deepseek-chat';
				}
			} else if (provider === 'openai') {
				urlInput.value = 'https://api.openai.com/v1/chat/completions';
				
				const openaiModels = [
					{value: 'gpt-3.5-turbo', text: 'GPT-3.5 Turbo'},
					{value: 'gpt-4', text: 'GPT-4'},
					{value: 'gpt-4-turbo', text: 'GPT-4 Turbo'},
					{value: 'gpt-4o', text: 'GPT-4o'}
				];
				
				openaiModels.forEach(model => {
					const option = document.createElement('option');
					option.value = model.value;
					option.text = model.text;
					// 如果是之前選中的模型，設置selected屬性
					if (currentSelectedModel === model.value) {
						option.selected = true;
					}
					modelSelect.appendChild(option);
				});
				
				// 如果當前選中的不在列表中，選擇默認值
				if (!openaiModels.some(model => model.value === currentSelectedModel)) {
					modelSelect.value = 'gpt-3.5-turbo';
				}
			}
		}

		// 只在切換提供者時執行更新，而不是頁面載入時
		document.getElementById('api_provider').addEventListener('change', updateModelOptions);
		</script>
		<?php
	}

	/**
	 * 渲染API模型字段
	 */
	public function render_api_model_field() {
		$options      = get_option(self::OPTION_NAME);
		$api_provider = isset($options['api_provider']) ? $options['api_provider'] : 'deepseek';
		$api_model    = isset($options['api_model']) ? $options['api_model'] : ( $api_provider === 'deepseek' ? 'deepseek-chat' : 'gpt-3.5-turbo' );
		?>
		<select name="<?php echo self::OPTION_NAME; ?>[api_model]" id="api_model">
		<?php if ($api_provider === 'deepseek' || empty($api_provider)) : ?>
				<option value="deepseek-chat" <?php selected($api_model, 'deepseek-chat'); ?>>DeepSeek Chat</option>
				<option value="deepseek-coder" <?php selected($api_model, 'deepseek-coder'); ?>>DeepSeek Coder</option>
			<?php elseif ($api_provider === 'openai') : ?>
				<option value="gpt-3.5-turbo" <?php selected($api_model, 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo</option>
				<option value="gpt-4" <?php selected($api_model, 'gpt-4'); ?>>GPT-4</option>
				<option value="gpt-4-turbo" <?php selected($api_model, 'gpt-4-turbo'); ?>>GPT-4 Turbo</option>
				<option value="gpt-4o" <?php selected($api_model, 'gpt-4o'); ?>>GPT-4o</option>
			<?php endif; ?>
		</select>
		<p class="description">選擇要使用的AI模型</p>
		<?php
	}

	/**
	 * 渲染溫度參數字段
	 */
	public function render_temperature_field() {
		$options     = get_option(self::OPTION_NAME);
		$temperature = isset($options['temperature']) ? $options['temperature'] : 0.7;
		?>
		<input 
			type="range" 
			min="0" 
			max="1" 
			step="0.1" 
			name="<?php echo self::OPTION_NAME; ?>[temperature]" 
			value="<?php echo esc_attr($temperature); ?>" 
			style="width: 200px;" 
			oninput="document.getElementById('temp_output').innerText = this.value" 
		/>
		<span id="temp_output" style="margin-left: 10px; display: inline-block; min-width: 30px;">
		<?php echo esc_attr($temperature); ?>
		</span>
		<p class="description">調整AI創造性程度（0-1）。值越低，回答越確定；值越高，回答越多樣化。默認為0.7</p>
		<?php
	}

	/**
	 * 渲染最大生成長度字段
	 */
	public function render_max_tokens_field() {
		$options    = get_option(self::OPTION_NAME);
		$max_tokens = isset($options['max_tokens']) ? $options['max_tokens'] : 2000;

		echo '<input type="number" min="100" max="4000" step="100" name="' . self::OPTION_NAME . '[max_tokens]" value="' . esc_attr($max_tokens) . '" class="regular-text" style="width: 100px;" />';
		echo '<p class="description">設置AI回應的最大字符數量。推薦值：2000</p>';
	}

	/**
	 * 渲染Q版人像設置區塊說明
	 */
	public function render_qavatar_section_description() {
		echo '<p>設置是否啟用命格Q版形象功能。啟用後將使用AI生成用戶的命格Q版人像。</p>';
	}

	/**
	 * 渲染啟用Q版人像選項
	 */
	public function render_enable_qavatar_field() {
		$options        = get_option(self::OPTION_NAME);
		$enable_qavatar = isset($options['enable_qavatar']) ? $options['enable_qavatar'] : 'no';
		?>
		<label>
			<input type="radio" name="<?php echo self::OPTION_NAME; ?>[enable_qavatar]" value="yes" <?php checked($enable_qavatar, 'yes'); ?> onclick="toggleQAvatarFields(true)" />
			啟用
		</label>
		<label style="margin-left: 15px;">
			<input type="radio" name="<?php echo self::OPTION_NAME; ?>[enable_qavatar]" value="no" <?php checked($enable_qavatar, 'no'); ?> onclick="toggleQAvatarFields(false)" />
			停用
		</label>
		<p class="description">選擇是否啟用命格Q版人像生成功能</p>

		<script>
		function toggleQAvatarFields(show) {
			const qavatar_fields = document.querySelectorAll('.qavatar-dependent-field');
			qavatar_fields.forEach(field => {
				field.style.display = show ? 'table-row' : 'none';
			});
		}

		// 頁面載入時執行一次
		document.addEventListener('DOMContentLoaded', function() {
			const enable_qavatar = document.querySelector('input[name="<?php echo self::OPTION_NAME; ?>[enable_qavatar]"][value="yes"]').checked;
			toggleQAvatarFields(enable_qavatar);
		});
		</script>
		<?php
	}

	/**
	 * 渲染Q版人像API密鑰字段
	 */
	public function render_qavatar_api_key_field() {
		$options         = get_option(self::OPTION_NAME);
		$qavatar_api_key = isset($options['qavatar_api_key']) ? $options['qavatar_api_key'] : '';
		?>
			<div class="qavatar-dependent-field">
				<input type="text" name="<?php echo self::OPTION_NAME; ?>[qavatar_api_key]" value="<?php echo esc_attr($qavatar_api_key); ?>" class="regular-text" />
				<p class="description">輸入用於圖像生成的API密鑰（可與上方API密鑰相同或不同）</p>
			</div>
		<?php
	}

	/**
	 * 渲染Q版人像模型字段
	 */
	public function render_qavatar_model_field() {
		$options       = get_option(self::OPTION_NAME);
		$qavatar_model = isset($options['qavatar_model']) ? $options['qavatar_model'] : 'gpt-image-1';
		?>
		<div class="qavatar-dependent-field">
			<select name="<?php echo self::OPTION_NAME; ?>[qavatar_model]">
				<option value="gpt-image-1" <?php selected($qavatar_model, 'gpt-image-1'); ?>>GPT-Image-1 (OpenAI-需進行身分驗證)</option>
				<option value="dall-e-3" <?php selected($qavatar_model, 'dall-e-3'); ?>>DALL-E 3 (OpenAI)</option>
			</select>
			<p class="description">選擇用於生成Q版人像的AI模型</p>
		</div>
		<?php
	}

	/**
	 * 渲染設置頁面
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1>八字命格分析 API設定</h1>
			<p>在此配置八字命格分析所需的API參數，包括API密鑰、地址等信息。</p>
			
			<form method="post" action="options.php">
		<?php
		settings_fields('wp_bazi_api_settings_group');
		do_settings_sections('wp-bazi-api-settings');
		submit_button('保存設定');
		?>
			</form>
		</div>
		<?php
	}

	/**
	 * 渲染使用說明頁面
	 */
	public function render_usage_guide_page() {
		?>
		<div class="wrap">
			<h1>八字命格分析使用說明</h1>
			
			<div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
				<h2>如何開始使用</h2>
				<ol>
					<li>首先在「API設定」頁面配置您的API密鑰和相關設置</li>
					<li>使用短代碼 <code>[bazi_analyzer]</code> 在任意頁面添加八字分析表單</li>
					<li>前台用戶可以填寫生辰八字信息進行分析</li>
				</ol>
				
				<h2>功能說明</h2>
				<div style="margin-bottom: 20px;">
					<h3>基本命格分析</h3>
					<p>根據用戶的生辰八字信息，提供以下分析結果：</p>
					<ul>
						<li>完整的八字盤（年柱、月柱、日柱、時柱）</li>
						<li>五行分析（金、木、水、火、土的分佈）</li>
						<li>日主分析及強弱判斷</li>
						<li>缺失五行和偏弱五行分析</li>
						<li>性格特點分析</li>
						<li>五行補充建議</li>
					</ul>
				</div>
				
				<div style="margin-bottom: 20px;">
					<h3>命格Q版人像功能</h3>
					<p>此功能可以根據用戶的八字命格特點，生成對應的Q版人像：</p>
					<ul>
						<li><strong>啟用方式</strong>：在「API設定」頁面的「命格Q版形象設置」區塊中啟用</li>
						<li><strong>API要求</strong>：需要使用OpenAI的圖像生成API，可使用GPT-Image-1或DALL-E 3模型</li>
						<li><strong>生成過程</strong>：用戶提交分析後，會先顯示分析結果，同時在後台非同步生成Q版人像</li>
						<li><strong>呈現方式</strong>：Q版人像生成完成後會自動顯示在分析結果頁面的頂部</li>
						<li><strong>形象特點</strong>：根據用戶的性別、年齡、日主特點和八字分析結果來定制人物形象</li>
					</ul>
					<p><em>注意：啟用此功能需要額外的API使用費用，請確保您的OpenAI賬戶有足夠的額度。</em></p>
				</div>
				
				<div style="margin-bottom: 20px;">
					<h3>商品推薦功能</h3>
					<p>根據命格分析中的缺失或偏弱五行，自動推薦相關商品：</p>
					<ul>
						<li>系統會根據用戶缺失或偏弱的五行屬性，從WooCommerce商品中篩選對應的商品</li>
						<li>商品需要按五行進行歸類（金、木、水、火、土）才能被正確推薦</li>
						<li>在分析結果頁面底部顯示推薦商品列表</li>
					</ul>
				</div>
				
				<h2>支持的API供應商</h2>
				<p>目前支持以下API供應商：</p>
				<ul>
					<li><strong>DeepSeek</strong> - 默認API供應商，用於八字分析</li>
					<li><strong>OpenAI</strong> - 可用於八字分析和Q版人像生成</li>
					<li><strong>其他兼容OpenAI接口的供應商</strong> - 可以通過修改API地址使用</li>
				</ul>
				
				<h2>常見問題</h2>
				<div style="margin-bottom: 15px;">
					<p><strong>問：API密鑰在哪裡獲取？</strong></p>
					<p>答：您需要在相應的API供應商（如DeepSeek或OpenAI）網站上註冊賬戶，然後在個人設置中獲取API密鑰。</p>
				</div>
				<div style="margin-bottom: 15px;">
					<p><strong>問：為什麼分析結果未顯示？</strong></p>
					<p>答：請檢查API密鑰是否正確，以及API供應商服務是否正常運行。您還可以查看瀏覽器控制台日誌獲取更多信息。</p>
				</div>
				<div style="margin-bottom: 15px;">
					<p><strong>問：Q版人像生成需要多長時間？</strong></p>
					<p>答：根據API服務器負載和圖像複雜度，通常需要5-30秒不等。生成過程在後台進行，用戶可以先查看分析結果。</p>
				</div>
				<div style="margin-bottom: 15px;">
					<p><strong>問：如何設置商品的五行屬性？</strong></p>
					<p>答：在WooCommerce商品分類中創建"金"、"木"、"水"、"火"、"土"五個分類，然後將商品歸入相應分類即可。</p>
				</div>
				
				<h2>更多資源</h2>
				<p>如需更多幫助，請訪問以下資源：</p>
				<ul>
					<li><a href="https://deepseek.com" target="_blank">DeepSeek官方網站</a></li>
					<li><a href="https://openai.com" target="_blank">OpenAI官方網站</a></li>
					<li><a href="https://github.com/s0985514623/wp-ai-bazi-analysis" target="_blank">插件GitHub倉庫</a></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * 獲取API設置
	 *
	 * @return array API設置數組
	 */
	public static function get_api_settings() {
		$default_settings = [
			'api_key'         => 'sk-996a8b520bbb40a191eb9e30e632e22b',
			'api_url'         => 'https://api.deepseek.com/chat/completions',
			'api_provider'    => 'deepseek',
			'api_model'       => 'deepseek-chat',
			'temperature'     => 0.7,
			'max_tokens'      => 2000,
			'enable_qavatar'  => 'no',
			'qavatar_api_key' => '',
			'qavatar_model'   => 'gpt-image-1',
		];

		$options = get_option(self::OPTION_NAME, []);

		return wp_parse_args($options, $default_settings);
	}
}
