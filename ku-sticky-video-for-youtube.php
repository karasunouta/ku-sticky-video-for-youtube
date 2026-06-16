<?php

/**
 * Plugin Name: KU Sticky Video for YouTube
 * Description: Make YouTube video player in posts follow the scroll position, showing in the corner of the page.
 * Version: 1.7.0
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Author: karasunouta
 * Author URI: https://karasunouta.com/
 * Text Domain: ku-sticky-video-for-youtube
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Source: https://github.com/karasunouta/ku-sticky-video-for-youtube
 * Copyright (c) 2026 karasunouta
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
	const VERSION = '1.7.0';

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

		$width = 400;
		if ( isset( $options['sticky_width_unit'] ) ) {
			if ( '%' === $options['sticky_width_unit'] && isset( $options['sticky_width_val_pct'] ) ) {
				$width = intval( $options['sticky_width_val_pct'] ) . 'vw';
			} elseif ( 'px' === $options['sticky_width_unit'] && isset( $options['sticky_width_val_px'] ) ) {
				$width = intval( $options['sticky_width_val_px'] );
			}
		}

		$localize_data = array(
			'showAbove'             => $show_above,
			'excludeClass'          => $exclude_class,
			'width'                 => $width,
			'widthMaxOriginal'      => ! empty( $options['sticky_width_max_original'] ) ? true : false,
			'widthMaxCustomActive'  => ! empty( $options['sticky_width_max_custom_active'] ) ? true : false,
			'widthMaxCustomVal'     => isset( $options['sticky_width_max_custom_val'] ) ? intval( $options['sticky_width_max_custom_val'] ) : 450,
			'triggerMode'           => isset( $options['sticky_trigger_mode'] ) ? $options['sticky_trigger_mode'] : 'always',
			'position'              => isset( $options['position'] ) ? $options['position'] : 'bottom-right',
			'disableNarrowViewport' => ! empty( $options['disable_narrow_viewport'] ) ? true : false,
		);

		if ( isset( $options['targeting_mode'] ) ) {
			$localize_data['targetingMode'] = $options['targeting_mode'];
		}
		if ( isset( $options['include_class'] ) ) {
			$localize_data['includeClass'] = $options['include_class'];
		}

		$localize_data = apply_filters( 'ku_sticky_video_for_youtube_localize_data', $localize_data, $options );

		wp_localize_script(
			$entry_point,
			'kuStickyVideoForYouTubeSettings',
			$localize_data
		);
	}

	/**
	 * 設定値を取得（デフォルト値を含む）
	 *
	 * @return array
	 */
	private function get_options() {
		$defaults = array(
			'exclude_class'                  => 'no-sticky',
			'position'                       => 'bottom-right',
			'disable_narrow_viewport'        => '1',
			'sticky_trigger_mode'            => 'playing',
			'sticky_width_unit'              => '%',
			'sticky_width_val_px'            => 400,
			'sticky_width_val_pct'           => 25,
			'sticky_width_max_original'      => '1',
			'sticky_width_max_custom_active' => '0',
			'sticky_width_max_custom_val'    => 450,
		);
		$defaults = apply_filters( 'ku_sticky_video_for_youtube_default_options', $defaults );

		$options = get_option( 'ku-sticky-video-for-youtube-options', array() );

		// デフォルト値に存在するキーのみを取得して返すことで、Pro版無効時にPro版オプションを無視する
		$filtered_options = array();
		foreach ( $defaults as $key => $default_value ) {
			if ( isset( $options[ $key ] ) ) {
				$filtered_options[ $key ] = $options[ $key ];
			} else {
				$filtered_options[ $key ] = $default_value;
			}
		}

		// 無料版での位置（値レベル）の制限
		if ( isset( $filtered_options['position'] ) && ! in_array( $filtered_options['position'], array( 'bottom-right', 'bottom-left' ), true ) ) {
			$filtered_options['position'] = 'bottom-right';
		}

		return apply_filters( 'ku_sticky_video_for_youtube_get_options', $filtered_options );
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
					'exclude_class'                  => 'no-sticky',
					'sticky_trigger_mode'            => 'playing',
					'sticky_width_unit'              => '%',
					'sticky_width_val_px'            => 400,
					'sticky_width_val_pct'           => 25,
					'sticky_width_max_original'      => '1',
					'sticky_width_max_custom_active' => '0',
					'sticky_width_max_custom_val'    => 450,
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

		if ( isset( $input['position'] ) && in_array( $input['position'], array( 'bottom-right', 'bottom-left' ), true ) ) {
			$output['position'] = $input['position'];
		} else {
			$output['position'] = 'bottom-right';
		}

		$output['disable_narrow_viewport'] = ! empty( $input['disable_narrow_viewport'] ) ? '1' : '0';

		if ( isset( $input['sticky_trigger_mode'] ) && in_array( $input['sticky_trigger_mode'], array( 'always', 'playing' ), true ) ) {
			$output['sticky_trigger_mode'] = $input['sticky_trigger_mode'];
		} else {
			$output['sticky_trigger_mode'] = 'playing';
		}

		if ( isset( $input['sticky_width_unit'] ) && in_array( $input['sticky_width_unit'], array( 'px', '%' ), true ) ) {
			$output['sticky_width_unit'] = $input['sticky_width_unit'];
		} else {
			$output['sticky_width_unit'] = 'px';
		}

		if ( isset( $input['sticky_width_val_px'] ) ) {
			$output['sticky_width_val_px'] = max( 100, intval( $input['sticky_width_val_px'] ) );
		} else {
			$output['sticky_width_val_px'] = 400;
		}

		if ( isset( $input['sticky_width_val_pct'] ) ) {
			$output['sticky_width_val_pct'] = max( 5, min( 100, intval( $input['sticky_width_val_pct'] ) ) );
		} else {
			$output['sticky_width_val_pct'] = 25;
		}

		$output['sticky_width_max_original']      = ! empty( $input['sticky_width_max_original'] ) ? '1' : '0';
		$output['sticky_width_max_custom_active'] = ! empty( $input['sticky_width_max_custom_active'] ) ? '1' : '0';

		if ( isset( $input['sticky_width_max_custom_val'] ) ) {
			$output['sticky_width_max_custom_val'] = max( 100, intval( $input['sticky_width_max_custom_val'] ) );
		} else {
			$output['sticky_width_max_custom_val'] = 450;
		}

		return apply_filters( 'ku_sticky_video_for_youtube_sanitize_options', $output, $input );
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
		$options                 = $this->get_options();
		$exclude_class           = $options['exclude_class'];
		$position                = isset( $options['position'] ) ? $options['position'] : 'bottom-right';
		$disable_narrow_viewport = isset( $options['disable_narrow_viewport'] ) ? $options['disable_narrow_viewport'] : '1';

		$template_path = plugin_dir_path( __FILE__ ) . 'templates/settings.php';
		$template_path = apply_filters( 'ku_sticky_video_for_youtube_settings_template', $template_path );

		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}
}

// プラグインを初期化
new KU_Sticky_Video_For_YouTube();
