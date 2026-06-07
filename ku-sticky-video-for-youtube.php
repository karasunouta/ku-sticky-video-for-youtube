<?php

/**
 * Plugin Name: KU Sticky Video for YouTube
 * Plugin URI: https://www.karasunouta.com/
 * Description: Make YouTube video player in posts follow the scroll position, showing in the corner of the page.
 * Version: 1.5.0
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: karasunouta
 * Author URI: https://www.karasunouta.com/
 * Text Domain: ku-sticky-video-for-youtube
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * * Copyright (c) 2026 karasunouta
 */

// 直接ファイルにアクセスされた場合に終了
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KU Sticky Video for YouTube のメインクラス
 */
class KU_Sticky_Video_For_YouTube {

	/**
	 * プラグインバージョン
	 */
	const VERSION = '1.5.0';

	/**
	 * スラッグ
	 *
	 * @var string
	 */
	private $slug = 'ku-sticky-video-for-youtube';

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		// 多言語対応
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// フロントエンド用スクリプトを登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// 管理画面用のアクションを追加
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	/**
	 * 多言語対応
	 */
	public function load_textdomain() {
		load_plugin_textdomain( $this->slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * フロントエンド用スクリプトを読み込み
	 */
	public function enqueue_scripts() {
		// 管理画面なら処理回避
		if ( is_admin() ) {
			return;
		}

		// パスの整理
		$entry_point    = 'ku-sticky-video-for-youtube';
		$asset_path     = plugin_dir_path( __FILE__ ) . "build/{$entry_point}.asset.php";
		$script_url     = plugins_url( "/build/{$entry_point}.js", __FILE__ );
		$languages_path = plugin_dir_path( __FILE__ ) . 'languages';

		// ビルド済みファイルが存在するかチェック
		if ( ! file_exists( $asset_path ) ) {
			return;
		}
		$assets = include $asset_path;

		// フロント用JSの読み込み
		wp_enqueue_script(
			$entry_point,
			$script_url,
			$assets['dependencies'],
			$assets['version'],
			true // フッターで読み込み
		);

		$options       = $this->get_options();
		$exclude_class = isset( $options['exclude_class'] ) ? $options['exclude_class'] : 'no-sticky';

		// 将来の設定ページ実装を見据えたベタ書きフラグ
		// true: 動画埋め込み位置より上でも追従表示する / false: 表示しない（デフォルト）
		$show_above = false;

		wp_localize_script(
			$entry_point,
			'kuStickyVideoForYouTubeSettings',
			array(
				'showAbove'    => $show_above,
				'excludeClass' => $exclude_class,
			)
		);
	}

	/**
	 * 設定値を取得（デフォルト値を含む）
	 *
	 * @return array
	 */
	private function get_options() {
		$defaults = array(
			'exclude_class' => 'no-sticky',
		);
		$options  = get_option( 'ku-sticky-video-for-youtube-options', array() );
		return wp_parse_args( $options, $defaults );
	}

	/**
	 * 管理画面の設定ページを追加
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'KU Sticky Video for YouTube Settings', 'ku-sticky-video-for-youtube' ),
			'KU Sticky Video for YouTube',
			'manage_options',
			$this->slug,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * 設定の登録とサニタイズ設定
	 */
	public function register_settings() {
		register_setting(
			'ku-sticky-video-for-youtube-options-group',
			'ku-sticky-video-for-youtube-options',
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => array(
					'exclude_class' => 'no-sticky',
				),
			)
		);
	}

	/**
	 * オプションのバリデーション・サニタイズ
	 *
	 * @param array $input 入力データ.
	 * @return array
	 */
	public function sanitize_options( $input ) {
		$output = array();

		if ( isset( $input['exclude_class'] ) ) {
			$class_name = sanitize_text_field( $input['exclude_class'] );
			// 先頭のドットや余分な空白を除去
			$class_name              = ltrim( trim( $class_name ), '.' );
			$output['exclude_class'] = $class_name;
		} else {
			$output['exclude_class'] = 'no-sticky';
		}

		return $output;
	}

	/**
	 * 管理画面用スタイルとスクリプトを読み込み
	 *
	 * @param string $hook 現在の管理画面のページフック.
	 */
	public function admin_enqueue_scripts( $hook ) {
		// 設定ページ以外ではスクリプト・スタイルを読み込まない
		if ( 'settings_page_' . $this->slug !== $hook ) {
			return;
		}

		// パスの整理
		$entry_point    = 'ku-sticky-video-for-youtube-admin';
		$asset_path     = plugin_dir_path( __FILE__ ) . "build/{$entry_point}.asset.php";
		$script_url     = plugins_url( "/build/{$entry_point}.js", __FILE__ );
		$style_url      = plugins_url( "/build/{$entry_point}.css", __FILE__ );
		$languages_path = plugin_dir_path( __FILE__ ) . 'languages';

		// ビルド済みファイルが存在するかチェック
		if ( ! file_exists( $asset_path ) ) {
			return;
		}
		$assets = include $asset_path;

		// 管理画面用JSの登録
		$script_handle = $this->slug . '-admin-script';
		wp_enqueue_script(
			$script_handle,
			$script_url,
			$assets['dependencies'],
			$assets['version'],
			true // フッターで読み込み
		);

		// 翻訳の読み込み
		wp_set_script_translations( $script_handle, $this->slug, $languages_path );

		// JS変数のセット
		wp_localize_script(
			$script_handle,
			'kuStickyVideoForYouTubeAdminSettings',
			array(
				'excludeClass' => $this->get_options()['exclude_class'],
			)
		);

		// 管理画面用CSS
		wp_enqueue_style(
			$this->slug . '-admin-style',
			$style_url,
			array(),
			$assets['version'] // スクリプトと同じバージョン管理を適用
		);
	}

	/**
	 * 設定ページの描画
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options       = $this->get_options();
		$exclude_class = $options['exclude_class'];
		?>
		<div class="wrap ku-sticky-video-for-youtube-admin-wrap">

			<div class="ku-sticky-video-for-youtube-header">
				<h1>KU Sticky Video for YouTube <span><?php echo esc_html( self::VERSION ); ?></span></h1>
				<p><?php esc_html_e( 'Configure options for your floating YouTube video player.', 'ku-sticky-video-for-youtube' ); ?></p>
			</div>

			<?php
			if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
				echo '<div class="success-badge"><svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" style="margin-right: 4px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' . esc_html__( 'Settings saved successfully.', 'ku-sticky-video-for-youtube' ) . '</div>';
			}
			?>

			<form method="post" action="options.php">
				<?php settings_fields( 'ku-sticky-video-for-youtube-options-group' ); ?>

				<div class="ku-sticky-video-for-youtube-card">
					<h2 class="ku-sticky-video-for-youtube-card-title">
						<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
						</svg>
						<?php esc_html_e( 'General Settings', 'ku-sticky-video-for-youtube' ); ?>
					</h2>

					<div class="form-group">
						<label for="exclude_class"><?php esc_html_e( 'Exclude CSS Class Name', 'ku-sticky-video-for-youtube' ); ?></label>
						<div class="input-wrapper">
							<span class="prefix-dot">.</span>
							<input type="text" id="exclude_class" name="ku-sticky-video-for-youtube-options[exclude_class]" value="<?php echo esc_attr( $exclude_class ); ?>" class="input-field" placeholder="no-sticky" />
						</div>
						<p class="field-description">
							<?php esc_html_e( 'If this class is assigned to a YouTube block or its parent container, that video will not follow the scroll position.', 'ku-sticky-video-for-youtube' ); ?><br>
							<?php printf( esc_html__( 'Default: %s', 'ku-sticky-video-for-youtube' ), '<code>no-sticky</code>' ); ?>
						</p>
					</div>

					<div class="instruction-box">
						<h3><?php esc_html_e( 'How to Exclude a YouTube Block', 'ku-sticky-video-for-youtube' ); ?></h3>
						<ol>
							<li><?php esc_html_e( 'Open the post editor and select the YouTube block you want to exclude.', 'ku-sticky-video-for-youtube' ); ?></li>
							<li><?php esc_html_e( 'In the block settings sidebar, expand the "Advanced" (高度な設定) panel.', 'ku-sticky-video-for-youtube' ); ?></li>
							<li><?php printf( esc_html__( 'Add the configured class name %s to the "Additional CSS class(es)" (追加 CSS クラス) input field.', 'ku-sticky-video-for-youtube' ), '<code>' . esc_html( $exclude_class ) . '</code>' ); ?></li>
						</ol>
					</div>
				</div>

				<button type="submit" class="submit-btn">
					<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
					</svg>
					<?php esc_html_e( 'Save Changes', 'ku-sticky-video-for-youtube' ); ?>
				</button>
			</form>
		</div>
		<?php
	}
}

// プラグインを初期化
new KU_Sticky_Video_For_YouTube();
