<?php
/**
 * Plugin Name: Sticky YouTube
 * Plugin URI: https://www.karasunouta.com/
 * Description: Make YouTube video player in posts follow the scroll position, showing in the corner of the page.
 * Version: 1.4.0
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
	const VERSION = '1.4.0';

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

		// 依存関係定義ファイルがなければ処理回避
		$asset_file = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		// 依存関係定義ファイルの読み込み
		$assets = include $asset_file;

		// フロント用JSの読み込み
		wp_enqueue_script(
			'sticky-youtube',
			plugins_url( 'build/index.js', __FILE__ ),
			$assets['dependencies'], // 依存関係の自動解決
			$assets['version'], // バージョン情報の自動制御
			true // フッターで読み込み
		);
	}
}

// プラグインを初期化
new Sticky_YouTube();
