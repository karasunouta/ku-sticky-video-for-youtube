<?php
/**
 * Plugin Name: Karasunouta Sticky YouTube
 * Plugin URI: https://www.karasunouta.com/
 * Description: WordPress投稿内のYouTube動画プレイヤーをスクロール状態に応じて追従表示
 * Version: 1.1.0
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
	 * コンストラクタ
	 */
	public function __construct() {
		// フロントエンド用スクリプトを登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * フロントエンド用スクリプトを読み込み
	 */
	public function enqueue_scripts() {		
		// プラグイン用JSファイルを読み込み
		$js_flag = 'karasunouta-sticky-youtube';
		$js_file_location = 'build/index.js';

		$js_url = plugin_dir_url( __FILE__ ) . $js_file_location; 
		$js_path = plugin_dir_path( __FILE__ ) . $js_file_location; 

		if ( file_exists( $js_path ) ) {
			wp_enqueue_script(
				$js_flag,
				$js_url,
				array(),
				filemtime( $js_path ),
				true
			);
		}
	}
}

// プラグインを初期化
new Karasunouta_Sticky_YouTube();