<?php

/**
 * Plugin Name: Sticky YouTube
 * Plugin URI: https://www.karasunouta.com/
 * Description: Make YouTube video player in posts follow the scroll position, showing in the corner of the page.
 * Version: 1.4.1
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: karasunouta
 * Author URI: https://www.karasunouta.com/
 * Text Domain: sticky-youtube
 * Domain Path: /languages
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
 * Sticky YouTube のメインクラス
 */
class Sticky_YouTube {

	/**
	 * プラグインバージョン
	 */
	const VERSION = '1.4.1';

	/**
	 * スラッグ
	 *
	 * @var string
	 */
	private $slug = 'sticky-youtube';

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		// 多言語対応
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// フロントエンド用スクリプトを登録
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
		$entry_point = 'sticky-youtube';
		$asset_path  = plugin_dir_path( __FILE__ ) . "build/{$entry_point}.asset.php";
		$script_url  = plugins_url( "/build/{$entry_point}.js", __FILE__ );
		// $style_url      = plugins_url( "/build/{$entry_point}.css", __FILE__ );
		// $style_path     = plugin_dir_path( __FILE__ ) . "build/{$entry_point}.css";
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
	}
}

// プラグインを初期化
new Sticky_YouTube();
