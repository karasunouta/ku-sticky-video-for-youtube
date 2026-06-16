/*!
 * KU Sticky Video for YouTube - JavaScript Component
 *
 * Part of: KU Sticky Video for YouTube WordPress Plugin
 * Author: karasunouta
 * Author URI: https://www.karasunouta.com/
 *
 * Description:
 * 投稿内のYouTube動画プレイヤーをスクロール状態に応じてページ隅に追従表示します。
 *
 * Dependencies:
 * - jQuery
 *
 * Copyright (c) 2026 karasunouta
 * License: GPLv2 or later
 */

/* global kuStickyVideoForYouTubeSettings */

( function () {
	'use strict';

	// 設定
	const config = {
		position: 'bottom-right', // 'bottom-right', 'bottom-left', 'top-right', 'top-left'
		width: 400, // Sticky時の幅（pxまたはvw）
		offset: 20, // 画面端からの距離（px）
		offsetX: 20, // 横方向の余白
		offsetY: 20, // 縦方向の余白
		zIndex: 9999,
		closeButton: true,
		useFade: true, // フェード効果の使用
		hideAbove: true, // 動画より上にスクロールした場合は隠す（デフォルトはtrue）
		excludeClass: 'no-sticky', // デフォルト除外クラス
		targetingMode: 'exclude', // デフォルトの指定方法
		includeClass: '', // デフォルト対象クラス
		limitTopActive: false,
		limitTopVal: 0,
		limitBottomActive: false,
		limitBottomVal: 0,
		disableNarrowViewport: true,
		mobileBreakpointActive: false,
		mobileBreakpointVal: 768,
		keepEnded: false,
		closeBtnPos: 'top-right',
		closeBtnOnlyPaused: false,
	};

	// PHP側から設定が渡されている場合はマージする
	if ( typeof kuStickyVideoForYouTubeSettings !== 'undefined' ) {
		Object.assign( config, kuStickyVideoForYouTubeSettings );
	}

	let $originalVideo = null;
	let $placeholder = null;
	let $closeButton = null;
	let isSticky = false;
	let isForceClosed = false;
	let originalStyles = {};
	const ytPlayers = [];

	function getCurrentPlayerState() {
		if ( ! $originalVideo || ! window.YT ) {
			return null;
		}
		for ( let i = 0; i < ytPlayers.length; i++ ) {
			if ( ytPlayers[ i ].iframe === $originalVideo ) {
				try {
					return ytPlayers[ i ].player.getPlayerState();
				} catch ( e ) {
					return null;
				}
			}
		}
		return null;
	}

	function setupVideoElements( $video ) {
		if ( ! $video ) {
			return;
		}

		// 元のスタイルを保存
		const computedStyle = window.getComputedStyle( $video );
		originalStyles = {
			position: computedStyle.position,
			top: computedStyle.top,
			left: computedStyle.left,
			right: computedStyle.right,
			bottom: computedStyle.bottom,
			width: computedStyle.width,
			height: computedStyle.height,
			zIndex: computedStyle.zIndex,
			boxShadow: computedStyle.boxShadow,
			borderRadius: computedStyle.borderRadius,
			opacity: computedStyle.opacity,
			marginTop: computedStyle.marginTop,
			marginBottom: computedStyle.marginBottom,
			marginLeft: computedStyle.marginLeft,
			marginRight: computedStyle.marginRight,
			display: computedStyle.display,
		};

		// プレースホルダーを作成
		if ( ! $placeholder ) {
			$placeholder = document.createElement( 'div' );
			$placeholder.className = 'youtube-placeholder';
		}
		const rect = $video.getBoundingClientRect();
		Object.assign( $placeholder.style, {
			width: rect.width + 'px',
			height: rect.height + 'px',
			position: originalStyles.position,
			top: originalStyles.top,
			left: originalStyles.left,
			bottom: originalStyles.bottom,
			right: originalStyles.right,
			marginTop: originalStyles.marginTop,
			marginBottom: originalStyles.marginBottom,
			marginLeft: originalStyles.marginLeft,
			marginRight: originalStyles.marginRight,
			display: 'none',
		} );

		// 閉じるボタンを作成
		if ( config.closeButton && ! $closeButton ) {
			$closeButton = document.createElement( 'button' );
			$closeButton.className = 'sticky-video-close';
			$closeButton.innerHTML = '✕';
			Object.assign( $closeButton.style, {
				position: 'fixed',
				width: '30px',
				height: '30px',
				background: config.closeBtnBg || 'rgba(0,0,0,0.7)',
				color: config.closeBtnIcon || '#fff',
				border: 'none',
				borderRadius: '50%',
				cursor: 'pointer',
				fontSize: '16px',
				zIndex: config.zIndex + 1,
				display: 'none',
				alignItems: 'center',
				justifyContent: 'center',
			} );

			$closeButton.addEventListener( 'click', function () {
				hideSticky();
				isForceClosed = true;
			} );

			$closeButton.addEventListener( 'mouseenter', function () {
				this.style.background = config.closeBtnHover || 'rgba(255,0,0,0.8)';
			} );

			$closeButton.addEventListener( 'mouseleave', function () {
				this.style.background = config.closeBtnBg || 'rgba(0,0,0,0.7)';
			} );

			document.body.appendChild( $closeButton );
		}
	}

	function resetVideoElements() {
		if ( isSticky ) {
			hideSticky( true ); // 即時復帰
		}
		if ( $originalVideo ) {
			// スタイルの復元
			Object.assign( $originalVideo.style, originalStyles );
			$originalVideo = null;
		}
		if ( $placeholder && $placeholder.parentNode ) {
			$placeholder.parentNode.removeChild( $placeholder );
		}
		originalStyles = {};
	}

	function updatePlayerInstance( currentPlayer, iframeElement ) {
		let currentIframe = null;
		try {
			currentIframe = currentPlayer.getIframe();
		} catch ( e ) {
			currentIframe = iframeElement;
		}

		if ( ! currentIframe ) {
			return null;
		}

		for ( let i = 0; i < ytPlayers.length; i++ ) {
			const item = ytPlayers[ i ];
			let itemIframe = null;
			try {
				itemIframe = item.player.getIframe();
			} catch ( e ) {}

			if (
				itemIframe === currentIframe ||
				item.iframe === currentIframe ||
				item.iframe === iframeElement
			) {
				item.player = currentPlayer;
				item.iframe = currentIframe;
				return currentIframe;
			}
		}
		return currentIframe;
	}

	function handlePlayerStateChange( event, isEligible, iframeElement ) {
		const state = event.data;
		const currentPlayer = event.target;
		const currentIframe = updatePlayerInstance(
			currentPlayer,
			iframeElement
		);

		if ( ! currentIframe ) {
			return;
		}

		if ( state === window.YT.PlayerState.PLAYING ) {
			// 他のすべてのプレイヤーを一時停止
			for ( let i = 0; i < ytPlayers.length; i++ ) {
				const item = ytPlayers[ i ];
				let itemIframe = null;

				try {
					itemIframe = item.player.getIframe();
				} catch ( e ) {}

				if ( itemIframe && itemIframe !== currentIframe ) {
					try {
						item.player.pauseVideo();
					} catch ( e ) {
						// 一時停止に失敗した場合は無視
					}
				}
			}

			// 別の動画が再生開始された場合、まずは現在のStickyを無条件で解除する
			if ( $originalVideo && $originalVideo !== currentIframe ) {
				resetVideoElements();
			}

			if ( isEligible ) {
				// 新ターゲットを登録
				if ( $originalVideo !== currentIframe ) {
					$originalVideo = currentIframe;
					setupVideoElements( $originalVideo );
					isForceClosed = false;
				}
				setTimeout( checkScroll, 100 );
			}
		}

		if ( state === window.YT.PlayerState.ENDED ) {
			if ( ! config.keepEnded && $originalVideo === currentIframe ) {
				resetVideoElements();
			}
		}

		// 一時停止時のみ表示オプションの動的制御
		if ( isSticky && $originalVideo === currentIframe && config.closeBtnOnlyPaused && $closeButton ) {
			const isPlaying = ( state === window.YT.PlayerState.PLAYING || state === window.YT.PlayerState.BUFFERING );
			$closeButton.style.display = isPlaying ? 'none' : 'flex';
		}
	}

	function setupPlayers( iframes ) {
		const targetingMode = config.targetingMode || 'exclude';
		const excludeSelector = config.excludeClass
			? '.' + config.excludeClass.trim().replace( /^\.+/, '' )
			: '';
		const includeSelector = config.includeClass
			? '.' + config.includeClass.trim().replace( /^\.+/, '' )
			: '';

		let foundFirstEligible = false;

		for ( let i = 0; i < iframes.length; i++ ) {
			const iframe = iframes[ i ];
			let isEligible = false;

			if ( targetingMode === 'include' ) {
				if ( includeSelector && iframe.closest( includeSelector ) ) {
					isEligible = true;
				}
			} else if (
				! excludeSelector ||
				! iframe.closest( excludeSelector )
			) {
				isEligible = true;
			}

			// 無料版の場合、条件に合致する最初の1つのみをSticky対象(isEligible)とする
			if ( ! config.isProActive && isEligible ) {
				if ( foundFirstEligible ) {
					isEligible = false;
				} else {
					foundFirstEligible = true;
				}
			}

			// 競合回避: すでに他プラグインが作成したプレイヤーオブジェクトがあれば再利用してイベントを相乗りする
			let existingPlayer = null;
			if ( window.YT && typeof window.YT.get === 'function' ) {
				const id = iframe.id || iframe.getAttribute( 'id' );
				existingPlayer =
					( id ? window.YT.get( id ) : null ) ||
					window.YT.get( iframe );
			}

			if ( existingPlayer ) {
				try {
					existingPlayer.addEventListener(
						'onStateChange',
						function ( event ) {
							handlePlayerStateChange(
								event,
								isEligible,
								iframe
							);
						}
					);

					ytPlayers.push( {
						player: existingPlayer,
						iframe,
						isEligible,
					} );

					continue;
				} catch ( e ) {
					// addEventListenerに失敗した場合はフォールバックして新規作成へ
				}
			}

			let src = iframe.getAttribute( 'src' );
			if ( src && src.indexOf( 'enablejsapi=1' ) === -1 ) {
				const separator = src.indexOf( '?' ) === -1 ? '?' : '&';
				const origin = window.location.origin;
				src +=
					separator +
					'enablejsapi=1&origin=' +
					encodeURIComponent( origin );

				iframe.addEventListener( 'load', function () {
					createPlayer( iframe, isEligible );
				}, { once: true } );

				iframe.setAttribute( 'src', src );
			} else {
				createPlayer( iframe, isEligible );
			}
		}
	}

	function createPlayer( iframe, isEligible ) {
		// YT.Player インスタンスの生成
		const player = new window.YT.Player( iframe, {
			events: {
				onReady( event ) {
					updatePlayerInstance( event.target, iframe );
				},
				onStateChange( event ) {
					handlePlayerStateChange( event, isEligible, iframe );
				},
			},
		} );

		ytPlayers.push( {
			player,
			iframe,
			isEligible,
		} );
	}

	function initPlayingMode( iframes ) {
		if ( iframes.length === 0 ) {
			return;
		}

		// 1. window.YT が存在しない場合のみ、自ら API をロードする
		if ( ! window.YT ) {
			const tag = document.createElement( 'script' );
			tag.src = 'https://www.youtube.com/iframe_api';
			const firstScriptTag =
				document.getElementsByTagName( 'script' )[ 0 ];
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		}

		// 2. 他プラグインによる onYouTubeIframeAPIReady の上書きを考慮し、
		//    一応 previousOnReady をラップする処理も残しておくが、それに依存しない。
		const previousOnReady = window.onYouTubeIframeAPIReady;
		window.onYouTubeIframeAPIReady = function () {
			if ( typeof previousOnReady === 'function' ) {
				previousOnReady();
			}
		};

		// 3. APIのロードと他プラグインの初期化を自律的にポーリング監視する（一元化）
		let attempts = 0;
		let apiReadyAttempts = 0;
		let setupCompleted = false;

		const checkInterval = setInterval( () => {
			attempts++;

			const isApiReady =
				window.YT && typeof window.YT.Player === 'function';
			if ( isApiReady ) {
				apiReadyAttempts++;
			}

			// すべての iframe に既存プレイヤーが登録されたかチェック（相乗り確認）
			let allFound = false;
			if ( isApiReady && typeof window.YT.get === 'function' ) {
				allFound = true;
				for ( let j = 0; j < iframes.length; j++ ) {
					const id =
						iframes[ j ].id || iframes[ j ].getAttribute( 'id' );
					if (
						! (
							( id ? window.YT.get( id ) : null ) ||
							window.YT.get( iframes[ j ] )
						)
					) {
						allFound = false;
						break;
					}
				}
			}

			// 終了条件:
			// - すべてのプレイヤーが他プラグインによってバインド完了した (allFound)
			// - または、APIがロード完了しており、かつ他プラグインの初期化完了を待つマージン（300ms）を経過した
			// - または、最大タイムアウト（3秒）に達した
			const shouldStop =
				allFound || apiReadyAttempts >= 3 || attempts >= 30;

			if ( shouldStop ) {
				clearInterval( checkInterval );
				if ( ! setupCompleted ) {
					setupCompleted = true;

					setupPlayers( iframes );
				}
			}
		}, 100 );
	}

	function init() {
		const iframes = document.querySelectorAll(
			'iframe[src*="youtube.com"], iframe[src*="youtu.be"]'
		);

		// Pro版が有効か、またはトリガー設定が「再生中のみ」の場合はYouTube APIを初期化する
		if ( config.triggerMode === 'playing' || config.isProActive ) {
			initPlayingMode( iframes );
		}

		if ( config.triggerMode === 'playing' ) {
			window.addEventListener( 'scroll', checkScroll );
			window.addEventListener( 'resize', handleResize );
		} else {
			const targetingMode = config.targetingMode || 'exclude';
			let targetIframe = null;

			if ( targetingMode === 'include' ) {
				const cleanClass = config.includeClass
					? config.includeClass.trim().replace( /^\.+/, '' )
					: '';
				const selector = cleanClass ? '.' + cleanClass : '';

				if ( ! selector ) {
					return;
				}

				for ( let i = 0; i < iframes.length; i++ ) {
					const iframe = iframes[ i ];
					if ( iframe.closest( selector ) ) {
						targetIframe = iframe;
						break;
					}
				}
			} else {
				const cleanClass = config.excludeClass
					? config.excludeClass.trim().replace( /^\.+/, '' )
					: '';
				const selector = cleanClass ? '.' + cleanClass : '';

				for ( let i = 0; i < iframes.length; i++ ) {
					const iframe = iframes[ i ];
					if ( selector && iframe.closest( selector ) ) {
						continue;
					}
					targetIframe = iframe;
					break;
				}
			}

			if ( ! targetIframe ) {
				return;
			}

			$originalVideo = targetIframe;
			setupVideoElements( $originalVideo );

			window.addEventListener( 'scroll', checkScroll );
			window.addEventListener( 'resize', handleResize );
		}
	}

	function checkScroll() {
		if ( ! $originalVideo ) {
			return;
		}

		const $reference = isSticky ? $placeholder : $originalVideo;

		if ( ! $reference ) {
			return;
		}

		const rect = $reference.getBoundingClientRect();
		const windowHeight = window.innerHeight;

		const effectiveHideAbove = config.triggerMode === 'playing' ? false : config.hideAbove;

		// 境界付近でのフリッカー（点滅）を防ぐための閾値（ヒステリシス）
		const threshold = 10;
		let isOutOfView;
		if ( ! isSticky ) {
			// 画面外に threshold ピクセル以上出た場合に Sticky 化する
			if ( ! effectiveHideAbove ) {
				isOutOfView =
					rect.bottom < -threshold ||
					rect.top > windowHeight + threshold;
			} else {
				isOutOfView = rect.bottom < -threshold;
			}
		} else if ( ! effectiveHideAbove ) {
			// 画面内に threshold ピクセル以上戻ってきた場合に Sticky を解除する
			isOutOfView =
				rect.bottom <= threshold ||
				rect.top >= windowHeight - threshold;
		} else {
			isOutOfView = rect.bottom <= threshold;
		}

		// 禁止領域に入っているかの判定
		let inExclusionZone = false;
		const scrollTop = window.scrollY || document.documentElement.scrollTop;
		const documentHeight = document.documentElement.scrollHeight;
		const distanceFromBottom = documentHeight - windowHeight - scrollTop;

		if (
			config.limitTopActive &&
			scrollTop < parseFloat( config.limitTopVal )
		) {
			inExclusionZone = true;
		}
		if (
			config.limitBottomActive &&
			distanceFromBottom < parseFloat( config.limitBottomVal )
		) {
			inExclusionZone = true;
		}

		if ( inExclusionZone ) {
			isOutOfView = false;
		}

		let isMobileOrNarrow = false;

		// 1. 無料版の自動判定
		if ( config.disableNarrowViewport ) {
			let targetWidthPx = parseFloat( config.width );
			if (
				typeof config.width === 'string' &&
				config.width.endsWith( 'vw' )
			) {
				targetWidthPx = ( targetWidthPx / 100 ) * window.innerWidth;
			}
			const marginX =
				config.offsetX !== undefined
					? parseFloat( config.offsetX )
					: config.offset || 20;
			const narrowThreshold = targetWidthPx + marginX * 2;
			if ( window.innerWidth <= narrowThreshold ) {
				isMobileOrNarrow = true;
			}
		}

		// 2. Pro版のカスタムブレイクポイント判定
		if ( config.mobileBreakpointActive && config.mobileBreakpointVal ) {
			if (
				window.innerWidth <= parseFloat( config.mobileBreakpointVal )
			) {
				isMobileOrNarrow = true;
			}
		}

		if ( isMobileOrNarrow ) {
			isOutOfView = false;
		}

		if ( isOutOfView && ! isSticky ) {
			if ( config.triggerMode === 'playing' ) {
				const state = getCurrentPlayerState();
				const isPlaying = ( state === 1 || state === 3 ); // 1: PLAYING, 3: BUFFERING
				if ( ! isPlaying ) {
					return;
				}
			}
			showSticky();
		} else if ( ! isOutOfView && isSticky ) {
			hideSticky();
		} else if ( isForceClosed ) {
			isForceClosed = false;
		}
	}

	function showSticky() {
		if ( ! $originalVideo || isSticky || isForceClosed ) {
			return;
		}
		isForceClosed = false;

		// 1. プレースホルダーを元の位置に挿入
		$originalVideo.parentNode.insertBefore( $placeholder, $originalVideo );

		let targetDisplay =
			originalStyles.display !== 'none'
				? originalStyles.display
				: 'block';
		if ( targetDisplay === 'inline' ) {
			targetDisplay = 'inline-block'; // width/height needs block or inline-block
		}
		$placeholder.style.display = targetDisplay;
		$originalVideo.style.opacity = '0';

		// 2. 目的の位置を計算
		const offsetX =
			config.offsetX !== undefined
				? parseFloat( config.offsetX )
				: config.offset || 20;
		const offsetY =
			config.offsetY !== undefined
				? parseFloat( config.offsetY )
				: config.offset || 20;

		const positions = {
			'bottom-right': { bottom: offsetY, right: offsetX },
			'bottom-left': { bottom: offsetY, left: offsetX },
			'top-right': { top: offsetY, right: offsetX },
			'top-left': { top: offsetY, left: offsetX },
		};
		const targetPos =
			positions[ config.position ] || positions[ 'bottom-right' ];

		// 3. アスペクト比を計算
		const originalWidth = parseFloat( originalStyles.width );
		const originalHeight = parseFloat( originalStyles.height );
		const aspectRatio = originalHeight / originalWidth;

		const widthVal = parseFloat( config.width );
		let widthUnit = 'px';
		if (
			typeof config.width === 'string' &&
			config.width.endsWith( 'vw' )
		) {
			widthUnit = 'vw';
		}

		let finalWidthVal = widthVal;
		let finalWidthUnit = widthUnit;

		// % (vw) 指定の場合のみ、上限値オプションの判定を行う
		if (
			widthUnit === 'vw' &&
			( config.widthMaxOriginal || config.widthMaxCustomActive )
		) {
			const viewportWidth = window.innerWidth;
			let pxWidth = ( widthVal / 100 ) * viewportWidth;

			// 元動画プレイヤーの幅を超過しない
			if ( config.widthMaxOriginal && pxWidth > originalWidth ) {
				pxWidth = originalWidth;
			}

			// カスタム最大幅以下に制限する
			if ( config.widthMaxCustomActive && config.widthMaxCustomVal ) {
				const maxCustom = parseFloat( config.widthMaxCustomVal );
				if ( pxWidth > maxCustom ) {
					pxWidth = maxCustom;
				}
			}

			finalWidthVal = pxWidth;
			finalWidthUnit = 'px';
		}

		let pxWidth = finalWidthVal;
		if ( finalWidthUnit === 'vw' ) {
			pxWidth = ( finalWidthVal / 100 ) * window.innerWidth;
		}

		let borderVal = parseFloat( config.borderWidth ) || 0;

		// Calculate inner dimensions to maintain original aspect ratio inside the borders
		let pxInnerWidth = Math.max( 50, pxWidth - borderVal * 2 );
		let pxInnerHeight = pxInnerWidth * aspectRatio;
		let pxHeight = pxInnerHeight + borderVal * 2;

		// 3.5 高さの上限制限（縦長動画などの画面占有を防ぐ）
		const heightMaxVh =
			config.heightMaxVh !== undefined
				? parseFloat( config.heightMaxVh )
				: 50;

		const viewportHeight = window.innerHeight;
		const maxAllowedHeightPx = viewportHeight * ( heightMaxVh / 100 );

		if ( pxHeight > maxAllowedHeightPx ) {
			pxHeight = maxAllowedHeightPx;

			// Re-calculate width based on inner height to preserve aspect ratio
			let pxInnerHeight = Math.max( 10, pxHeight - borderVal * 2 );
			let pxInnerWidth = pxInnerHeight / aspectRatio;
			pxWidth = pxInnerWidth + borderVal * 2;

			finalWidthVal = pxWidth;
			finalWidthUnit = 'px';
		}

		let finalWidthStyle;
		let finalHeightStyle;

		if ( finalWidthUnit === 'vw' ) {
			finalWidthStyle = finalWidthVal + 'vw';
			finalHeightStyle = `calc((${ finalWidthVal }vw - ${ borderVal * 2 }px) * ${ aspectRatio } + ${ borderVal * 2 }px)`;
		} else {
			finalWidthStyle = pxWidth + 'px';
			finalHeightStyle = pxHeight + 'px';
		}

		// 4. iframeをSticky位置に移動（※DOM構造を変えると再生が維持できない）
		let boxShadowOpacity = 0.3;
		if ( config.boxShadowOpacity !== undefined ) {
			boxShadowOpacity = parseFloat( config.boxShadowOpacity ) / 100;
		}

		let borderRadiusPx = 8;
		if ( config.borderRadius !== undefined ) {
			borderRadiusPx = parseFloat( config.borderRadius );
		}

		let borderStyle = 'none';
		if ( config.borderWidth !== undefined && parseFloat( config.borderWidth ) > 0 ) {
			const bWidth = parseFloat( config.borderWidth );
			const bColor = config.borderColor || '#000000';
			borderStyle = bWidth + 'px solid ' + bColor;
		}

		const newStyles = {
			position: 'fixed',
			boxSizing: 'border-box',
			width: finalWidthStyle,
			height: finalHeightStyle,
			zIndex: config.zIndex,
			boxShadow: '0 4px 12px rgba(0,0,0,' + boxShadowOpacity + ')',
			borderRadius: borderRadiusPx + 'px',
			border: borderStyle,
			top: targetPos.top !== undefined ? targetPos.top + 'px' : 'auto',
			bottom:
				targetPos.bottom !== undefined
					? targetPos.bottom + 'px'
					: 'auto',
			left: targetPos.left !== undefined ? targetPos.left + 'px' : 'auto',
			right:
				targetPos.right !== undefined ? targetPos.right + 'px' : 'auto',
		};

		Object.assign( $originalVideo.style, newStyles );

		if ( config.useFade ) {
			// フェードあり
			$originalVideo.animate( [ { opacity: 0 }, { opacity: 1 } ], {
				duration: 200,
				fill: 'forwards',
			} );
		} else {
			// フェードなし
			$originalVideo.style.opacity = '1';
		}

		// 5. 閉じるボタンを表示
		if ( $closeButton ) {
			const btnOffset = 5;
			const closeBtnPos = config.closeBtnPos || 'top-right';

			let btnLeft = 'auto';
			let btnRight = 'auto';
			let btnTop = 'auto';
			let btnBottom = 'auto';

			// X-axis positioning
			if ( closeBtnPos.indexOf( 'left' ) !== -1 ) {
				if ( targetPos.left !== undefined ) {
					btnLeft = ( targetPos.left + btnOffset ) + 'px';
				} else {
					btnRight = `calc(${ targetPos.right }px + ${ finalWidthStyle } - ${ btnOffset + 30 }px)`;
				}
			} else { // right
				if ( targetPos.left !== undefined ) {
					btnLeft = `calc(${ targetPos.left }px + ${ finalWidthStyle } - ${ btnOffset + 30 }px)`;
				} else {
					btnRight = ( targetPos.right + btnOffset ) + 'px';
				}
			}

			// Y-axis positioning
			if ( closeBtnPos.indexOf( 'bottom' ) !== -1 ) {
				if ( targetPos.top !== undefined ) {
					btnTop = `calc(${ targetPos.top }px + ${ finalHeightStyle } - ${ btnOffset + 30 }px)`;
				} else {
					btnBottom = ( targetPos.bottom + btnOffset ) + 'px';
				}
			} else { // top
				if ( targetPos.top !== undefined ) {
					btnTop = ( targetPos.top + btnOffset ) + 'px';
				} else {
					btnBottom = `calc(${ targetPos.bottom }px + ${ finalHeightStyle } - ${ btnOffset + 30 }px)`;
				}
			}

			Object.assign( $closeButton.style, {
				top: btnTop,
				bottom: btnBottom,
				left: btnLeft,
				right: btnRight,
				margin: '0',
				padding: '0',
				boxSizing: 'border-box',
				transform: 'none',
			} );

			// 一時停止状態のみ表示するオプションの処理
			if ( config.closeBtnOnlyPaused ) {
				const state = getCurrentPlayerState();
				const isPlaying = ( state === 1 || state === 3 ); // 1: PLAYING, 3: BUFFERING
				$closeButton.style.display = isPlaying ? 'none' : 'flex';
			} else {
				$closeButton.style.display = 'flex';
			}
		}

		isSticky = true;
	}

	function hideSticky( immediate ) {
		if ( ! isSticky ) {
			return;
		}

		const complete = function () {
			if ( ! $originalVideo ) {
				return;
			}
			// 1. 元のスタイルを復元
			Object.assign( $originalVideo.style, originalStyles );

			// フェードあり（即時解除でない場合のみフェードイン）
			if ( config.useFade && ! immediate ) {
				$originalVideo.animate( [ { opacity: 0 }, { opacity: 1 } ], {
					duration: 200,
					fill: 'forwards',
				} );
			} else {
				$originalVideo.style.opacity = originalStyles.opacity || '1';
			}

			// 2. プレースホルダーを非表示
			if ( $placeholder ) {
				$placeholder.style.display = 'none';
			}
		};

		if ( config.useFade && ! immediate ) {
			// フェードあり
			const anim = $originalVideo.animate(
				[ { opacity: 1 }, { opacity: 0 } ],
				{ duration: 200, fill: 'forwards' }
			);
			anim.onfinish = complete;
		} else {
			// フェードなし
			complete();
		}

		// 3. 閉じるボタンを非表示
		if ( $closeButton ) {
			$closeButton.style.display = 'none';
		}

		isSticky = false;
	}

	function handleResize() {
		if ( ! $originalVideo ) {
			return;
		}

		if ( ! isSticky ) {
			// 元のサイズを更新
			const computed = window.getComputedStyle( $originalVideo );
			originalStyles.width = computed.width;
			originalStyles.height = computed.height;
			const rect = $originalVideo.getBoundingClientRect();
			Object.assign( $placeholder.style, {
				width: rect.width + 'px',
				height: rect.height + 'px',
			} );
		} else {
			// Sticky状態の場合は一度戻してから再計算
			isSticky = false;
			Object.assign( $originalVideo.style, originalStyles );
			if ( $closeButton ) {
				$closeButton.style.display = 'none';
			}

			const computed = window.getComputedStyle( $originalVideo );
			originalStyles.width = computed.width;
			originalStyles.height = computed.height;

			setTimeout( checkScroll, 100 );
		}
	}

	// DOMContentLoaded
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
