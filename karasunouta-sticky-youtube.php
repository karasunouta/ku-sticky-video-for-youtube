<?php
/**
 * Plugin Name: Karasunouta Sticky YouTube
 * Plugin URI: https://www.karasunouta.com/
 * Description: WordPress投稿内のYouTube動画プレイヤーをスクロール状態に応じて追従表示
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: karasunouta
 * Author URI: https://www.karasunouta.com/
 * Text Domain: ku-sticky-yt
 * License: Commercial
 * License URI: https://www.karasunouta.com/
 * 
 * Copyright (c) 2026 karasunouta
 * Licensed for two sites use.
 */

// 直接ファイルにアクセスされた場合に終了
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Karasunouta YouTube Controller のメインクラス
 */
class Karasunouta_Sticky_YouTube {

	/**
	 * プラグインバージョン
	 */
	const VERSION = '1.0.0';
	
	/**
	 * ライセンスキーを格納するWPオプション名
	 */
	public $license_key_option_name = 'karasunouta_sticky_youtube_license_key' ;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		// ライセンスキー制御
		$this->maybe_add_license();
		
		// フロントエンド用スクリプトを登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * ライセンスキー制御
	 */
	private function maybe_add_license() {
		// 使用オプション名
		$license_key = get_option( $this->license_key_option_name );
		
		// 有効化時
		register_activation_hook( __FILE__, function () use ( $license_key ) {
			if ( $license_key ) {
				return;
			}

			$license_key = wp_generate_password( 12, false, false );
			update_option( $this->license_key_option_name, $license_key  );
		});
		
		// フロントエンドのmetaタグに出力
		if  ( $license_key && ! is_admin() ){
			add_action('wp_head', function () use ( $license_key ) {
				echo '<meta name="ksylk-meta" content="' .
					esc_attr( $license_key ) .
				'">' . "\n";
			});
		}
		
		// 無効化時
		register_deactivation_hook(__FILE__, function () {
			delete_option( $this->license_key_option_name );
		});
	}

	/**
	 * フロントエンド用スクリプトを読み込み
	 */
	public function enqueue_scripts() {
		// jQueryを読み込み
		wp_enqueue_script( 'jquery' );
		
		// プラグイン用JSファイルを読み込み
		$js_flag = 'karasunouta-sticky-youtube';
		$js_file_location = 'assets/js/karasunouta_sticky_youtube.min.js';
		$js_url = plugin_dir_url( __FILE__ ) . $js_file_location; 
		$js_path = plugin_dir_path( __FILE__ ) . $js_file_location; 
		if ( file_exists( $js_path ) ) {
			wp_enqueue_script(
				$js_flag,
				$js_url,
				array( 'jquery' ),
				filemtime( $js_path ),
				true
			);
			
			$license_key = get_option(  $this->license_key_option_name );
			if ( $license_key ) {
				wp_add_inline_script(
					$js_flag,
					'const KSYLK_JS = ' . json_encode( $license_key ) . ';',
					'before'
				);
			}
		}
	}
}

// プラグインを初期化
new Karasunouta_Sticky_YouTube();