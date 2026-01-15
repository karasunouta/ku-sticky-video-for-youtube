/*!
 * Karasunouta Sticky YouTube - JavaScript Component
 * 
 * Part of: Karasunouta Sticky YouTube WordPress Plugin
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
 * @package Karasunouta_Sticky_YouTube
 * @version 1.0.0
 */

(function($) {
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
    $originalVideo = $('iframe[src*="youtube.com"], iframe[src*="youtu.be"]').first();
    
    if ($originalVideo.length === 0) {
      return;
    }

    // 元のスタイルを保存
    originalStyles = {
      position: $originalVideo.css('position'),
      top: $originalVideo.css('top'),
      left: $originalVideo.css('left'),
      right: $originalVideo.css('right'),
      bottom: $originalVideo.css('bottom'),
      width: $originalVideo.css('width'),
      height: $originalVideo.css('height'),
      zIndex: $originalVideo.css('z-index'),
      boxShadow: $originalVideo.css('box-shadow'),
      borderRadius: $originalVideo.css('border-radius'),
    };

    // プレースホルダーを作成
    $placeholder = $('<div>', {
      class: 'youtube-placeholder',
      css: {
        width: $originalVideo.outerWidth() + 'px',
        height: $originalVideo.outerHeight() + 'px',
        display: 'none',
      }
    });

    // 閉じるボタンを作成
    if (config.closeButton) {
      $closeButton = $('<button>', {
        class: 'sticky-video-close',
        html: '✕',
        css: {
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
        },
        click: function() {
          hideSticky();
          isForceClosed = true;
        },
        mouseenter: function() {
          $(this).css('background', 'rgba(255,0,0,0.8)');
        },
        mouseleave: function() {
          $(this).css('background', 'rgba(0,0,0,0.7)');
        }
      });
      $('body').append($closeButton);
    }

    $(window).on('scroll', checkScroll);
    $(window).on('resize', handleResize);
  }

  function checkScroll() {
    if (!$originalVideo || $originalVideo.length === 0) return;

    const $reference = isSticky ? $placeholder : $originalVideo;
    
    if ($reference.length === 0) return;
    
    const rect = $reference[0].getBoundingClientRect();
    const windowHeight = $(window).height();
    const isOutOfView = (rect.bottom < 0) || (rect.top > windowHeight);

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
    $originalVideo.before($placeholder);
    $placeholder.show();
    $originalVideo.css({ opacity: 0 });

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

    if (config.useFade) {
      // フェードあり
      $originalVideo.css({ ...newStyles });
      $originalVideo.animate({ opacity: 1 }, 200);
    } else {
      // フェードなし
      $originalVideo.css({ ...newStyles, opacity: 1 });
    }

    // 5. 閉じるボタンを表示
    if ($closeButton) {
      const btnOffset = 5;
      $closeButton.css({
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
    
    const complete = function() {
      // 1. 元のスタイルを復元
      $originalVideo.css(originalStyles);
      
      // フェードあり
      if (config.useFade) {
           $originalVideo.animate({ opacity: 1 }, 200);
      }

      // 2. プレースホルダーを非表示
      $placeholder.hide();
    };

    if (config.useFade) {
      // フェードあり
      $originalVideo.animate({ opacity: 0 }, 200, complete);
    } else {
      // フェードなし
      complete();
    }

    // 3. 閉じるボタンを非表示
    if ($closeButton) {
      $closeButton.hide();
    }
    
    isSticky = false;
  }

  function handleResize() {
    if (!$originalVideo || $originalVideo.length === 0) return;

    if (!isSticky) {
      // 元のサイズを更新
      originalStyles.width = $originalVideo.css('width');
      originalStyles.height = $originalVideo.css('height');
      $placeholder.css({
        width: $originalVideo.outerWidth() + 'px',
        height: $originalVideo.outerHeight() + 'px',
      });
    } else {
      // Sticky状態の場合は一度戻してから再計算
      const wasSticky = isSticky;
      isSticky = false;
      $originalVideo.css(originalStyles);
      if ($closeButton) $closeButton.hide();
      
      originalStyles.width = $originalVideo.css('width');
      originalStyles.height = $originalVideo.css('height');
      
      setTimeout(checkScroll, 100);
    }
  }

  $(document).ready(function() {
    // 処理開始条件の確認
    if (typeof KSYLK_JS !== 'string') {
        return;
    }

    const meta = document.querySelector(
        'meta[name="ksylk-meta"]'
    );

    if (!meta) {
        return;
    }

    const metaValue = meta.getAttribute('content');

    if (metaValue !== KSYLK_JS) {
        return;
    }

    // 処理開始
    init();
  });

})(jQuery);