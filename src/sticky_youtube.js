/*!
 * Sticky YouTube - JavaScript Component
 * 
 * Part of: Sticky YouTube WordPress Plugin
 * Version: 1.0.0
 * Author: karasunouta
 * Author URI: https://www.karasunouta.com/
 * 
 * Description:
 * WordPress投稿内のYouTube動画プレイヤーをスクロール状態に応じて追従表示します。
 * 
 * Dependencies:
 * - jQuery
 * 
 * Copyright (c) 2026 karasunouta
 * Licenses can be purchased from the author's website.
 * Licensed for single site use.
 * 
 * @package Sticky_YouTube
 * @version 1.0.0
 */
(function () {
  'use strict';

  // 設定
  const config = {
    position: 'bottom-right', // 'bottom-right', 'bottom-left', 'top-right', 'top-left'
    width: 300, // Sticky時の幅（px）
    offset: 20, // 画面端からの距離（px）
    zIndex: 9999,
    closeButton: true,
    useFade: true, // フェード効果の使用
  };

  let $originalVideo = null;
  let $placeholder = null;
  let $closeButton = null;
  let isSticky = false;
  let isForceClosed = false;
  let originalStyles = {};

  function init() {
    // iframe[src*="youtube.com"], iframe[src*="youtu.be"] の最初の要素を取得
    $originalVideo = document.querySelector('iframe[src*="youtube.com"], iframe[src*="youtu.be"]');

    if (!$originalVideo) {
      return;
    }

    // 元のスタイルを保存
    const computedStyle = window.getComputedStyle($originalVideo);
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
    $placeholder = document.createElement('div');
    $placeholder.className = 'youtube-placeholder';
    const rect = $originalVideo.getBoundingClientRect();
    Object.assign($placeholder.style, {
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
    });

    // 閉じるボタンを作成
    if (config.closeButton) {
      $closeButton = document.createElement('button');
      $closeButton.className = 'sticky-video-close';
      $closeButton.innerHTML = '✕';
      Object.assign($closeButton.style, {
        position: 'fixed',
        width: '30px',
        height: '30px',
        background: 'rgba(0,0,0,0.7)',
        color: '#fff',
        border: 'none',
        borderRadius: '50%',
        cursor: 'pointer',
        fontSize: '16px',
        zIndex: config.zIndex + 1,
        display: 'none',
        alignItems: 'center',
        justifyContent: 'center',
      });

      $closeButton.addEventListener('click', function () {
        hideSticky();
        isForceClosed = true;
      });

      $closeButton.addEventListener('mouseenter', function () {
        this.style.background = 'rgba(255,0,0,0.8)';
      });

      $closeButton.addEventListener('mouseleave', function () {
        this.style.background = 'rgba(0,0,0,0.7)';
      });

      document.body.appendChild($closeButton);
    }

    window.addEventListener('scroll', checkScroll);
    window.addEventListener('resize', handleResize);
  }

  function checkScroll() {
    if (!$originalVideo) return;

    const $reference = isSticky ? $placeholder : $originalVideo;

    if (!$reference) return;

    const rect = $reference.getBoundingClientRect();
    const windowHeight = window.innerHeight;
    
    // 境界付近でのフリッカー（点滅）を防ぐための閾値（ヒステリシス）
    const threshold = 10;
    let isOutOfView;
    if (!isSticky) {
      // 画面外に threshold ピクセル以上出た場合に Sticky 化する
      isOutOfView = (rect.bottom < -threshold) || (rect.top > windowHeight + threshold);
    } else {
      // 画面内に threshold ピクセル以上戻ってきた場合に Sticky を解除する
      isOutOfView = (rect.bottom <= threshold) || (rect.top >= windowHeight - threshold);
    }

    if (isOutOfView && !isSticky) {
      showSticky();
    } else if (!isOutOfView && isSticky) {
      hideSticky();
    } else if (isForceClosed) {
      isForceClosed = false;
    }
  }

  function showSticky() {
    if (!$originalVideo || isSticky || isForceClosed) return;
    isForceClosed = false;

    // 1. プレースホルダーを元の位置に挿入
    $originalVideo.parentNode.insertBefore($placeholder, $originalVideo);
    
    let targetDisplay = originalStyles.display !== 'none' ? originalStyles.display : 'block';
    if (targetDisplay === 'inline') {
      targetDisplay = 'inline-block'; // width/height needs block or inline-block
    }
    $placeholder.style.display = targetDisplay;
    $originalVideo.style.opacity = '0';

    // 2. 目的の位置を計算
    const positions = {
      'bottom-right': { bottom: config.offset, right: config.offset },
      'bottom-left': { bottom: config.offset, left: config.offset },
      'top-right': { top: config.offset, right: config.offset },
      'top-left': { top: config.offset, left: config.offset }
    };
    const targetPos = positions[config.position] || positions['bottom-right'];

    // 3. アスペクト比を計算
    const originalWidth = parseFloat(originalStyles.width);
    const originalHeight = parseFloat(originalStyles.height);
    const aspectRatio = originalHeight / originalWidth;
    const stickyHeight = config.width * aspectRatio;

    // 4. iframeをSticky位置に移動（※DOM構造を変えると再生が維持できない）
    const newStyles = {
      position: 'fixed',
      width: config.width + 'px',
      height: stickyHeight + 'px',
      zIndex: config.zIndex,
      boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
      borderRadius: '8px',
      top: targetPos.top !== undefined ? targetPos.top + 'px' : 'auto',
      bottom: targetPos.bottom !== undefined ? targetPos.bottom + 'px' : 'auto',
      left: targetPos.left !== undefined ? targetPos.left + 'px' : 'auto',
      right: targetPos.right !== undefined ? targetPos.right + 'px' : 'auto',
    };

    Object.assign($originalVideo.style, newStyles);

    if (config.useFade) {
      // フェードあり
      $originalVideo.animate([
        { opacity: 0 },
        { opacity: 1 }
      ], { duration: 200, fill: 'forwards' });
    } else {
      // フェードなし
      $originalVideo.style.opacity = '1';
    }

    // 5. 閉じるボタンを表示
    if ($closeButton) {
      const btnOffset = 5;
      Object.assign($closeButton.style, {
        top: targetPos.top !== undefined ? (targetPos.top + btnOffset) + 'px' : 'auto',
        bottom: targetPos.bottom !== undefined ? (targetPos.bottom + stickyHeight - btnOffset - 30) + 'px' : 'auto',
        left: targetPos.left !== undefined ? (targetPos.left + config.width - btnOffset - 30) + 'px' : 'auto',
        right: targetPos.right !== undefined ? (targetPos.right + btnOffset) + 'px' : 'auto',
        display: 'flex',
      });
    }

    isSticky = true;
  }

  function hideSticky() {
    if (!isSticky) return;

    const complete = function () {
      // 1. 元のスタイルを復元
      Object.assign($originalVideo.style, originalStyles);

      // フェードあり
      if (config.useFade) {
        $originalVideo.animate([
          { opacity: 0 },
          { opacity: 1 }
        ], { duration: 200, fill: 'forwards' });
      }

      // 2. プレースホルダーを非表示
      $placeholder.style.display = 'none';
    };

    if (config.useFade) {
      // フェードあり
      const anim = $originalVideo.animate([
        { opacity: 1 },
        { opacity: 0 }
      ], { duration: 200, fill: 'forwards' });
      anim.onfinish = complete;
    } else {
      // フェードなし
      complete();
    }

    // 3. 閉じるボタンを非表示
    if ($closeButton) {
      $closeButton.style.display = 'none';
    }

    isSticky = false;
  }

  function handleResize() {
    if (!$originalVideo) return;

    if (!isSticky) {
      // 元のサイズを更新
      const computed = window.getComputedStyle($originalVideo);
      originalStyles.width = computed.width;
      originalStyles.height = computed.height;
      const rect = $originalVideo.getBoundingClientRect();
      Object.assign($placeholder.style, {
        width: rect.width + 'px',
        height: rect.height + 'px',
      });
    } else {
      // Sticky状態の場合は一度戻してから再計算
      const wasSticky = isSticky;
      isSticky = false;
      Object.assign($originalVideo.style, originalStyles);
      if ($closeButton) $closeButton.style.display = 'none';

      const computed = window.getComputedStyle($originalVideo);
      originalStyles.width = computed.width;
      originalStyles.height = computed.height;

      setTimeout(checkScroll, 100);
    }
  }

  // DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();