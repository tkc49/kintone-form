/**
 * kintone-form Admin JavaScript
 *
 * @package Kintone_Form
 */

(function($){

	var $input = $('.your-cf7-tag-name');

	$input.each(function(index, element){

		var $output = $('#short-code-'+$(this).attr("id"));
		$(this).on('input', function(event) {
			var value = $(this).val();
			$output.text(value);

			if( $(this).val() !== '' ){
				$('select[id*="cf7-mailtag-' + $(this).attr("id")).attr("disabled", true);
			}else{
				$('select[id*="cf7-mailtag-' + $(this).attr("id")).removeAttr("disabled");
				$output.text($('select[id*="cf7-mailtag-' + $(this).attr("id")).val());
			}
		});

		$('select[id*="cf7-mailtag-' + $(this).attr("id")).change(function(){
			$output.text($(this).val());
		});

	});

})(jQuery);

(function($){

	$( 'input.kintone-form-insert-tag' ).click( function() {

		var $form = $( this ).closest( 'form.tag-generator-panel' );
		var tag = $form.find( 'textarea.tag' ).val();
		wpcf7.taggen.insert( tag );
		tb_remove(); // close thickbox
		return false;
	} );


})(jQuery);

/**
 * kintone API トークン管理
 */
(function($){
	'use strict';

	var MAX_TOKENS = 9; // kintone の仕様: 1アプリあたり最大9トークン

	// トークン追加ボタン
	$(document).on('click', '.kintone-token-add', function(e) {
		e.preventDefault();
		var $container = $(this).closest('.kintone-token-fields');
		var appIndex = $container.data('app-index');
		var $rows = $container.find('.kintone-token-row');
		var newIndex = $rows.length;

		// 最大数チェック
		if ($rows.length >= MAX_TOKENS) {
			alert('トークンは最大' + MAX_TOKENS + '個までです（kintone の仕様）');
			return;
		}

		var $newRow = $('<div class="kintone-token-row" style="margin-bottom: 5px;">' +
			'<input type="password" ' +
				'name="kintone_setting_data[app_datas][' + appIndex + '][tokens][' + newIndex + ']" ' +
				'class="regular-text kintone-token-input" ' +
				'size="50" ' +
				'value="" ' +
				'placeholder="API Token を入力" ' +
				'autocomplete="new-password" />' +
			'<input type="hidden" ' +
				'name="kintone_setting_data[app_datas][' + appIndex + '][tokens_existing][' + newIndex + ']" ' +
				'value="" />' +
			'<button type="button" class="button kintone-token-remove" title="削除">×</button>' +
		'</div>');

		$(this).before($newRow);
		updateRemoveButtonVisibility($container);
		updateAddButtonVisibility($container);
	});

	// トークン削除ボタン
	$(document).on('click', '.kintone-token-remove', function(e) {
		e.preventDefault();
		var $container = $(this).closest('.kintone-token-fields');
		var $rows = $container.find('.kintone-token-row');

		// 最低1つは残す
		if ($rows.length > 1) {
			$(this).closest('.kintone-token-row').remove();
			reindexTokenFields($container);
			updateRemoveButtonVisibility($container);
			updateAddButtonVisibility($container);
		}
	});

	// フィールドのインデックスを振り直す
	function reindexTokenFields($container) {
		var appIndex = $container.data('app-index');
		$container.find('.kintone-token-row').each(function(index) {
			$(this).find('.kintone-token-input').attr(
				'name',
				'kintone_setting_data[app_datas][' + appIndex + '][tokens][' + index + ']'
			);
			$(this).find('input[type="hidden"]').attr(
				'name',
				'kintone_setting_data[app_datas][' + appIndex + '][tokens_existing][' + index + ']'
			);
		});
	}

	// 削除ボタンの表示/非表示を更新
	function updateRemoveButtonVisibility($container) {
		var $rows = $container.find('.kintone-token-row');
		if ($rows.length <= 1) {
			$rows.find('.kintone-token-remove').hide();
		} else {
			$rows.find('.kintone-token-remove').show();
		}
	}

	// 追加ボタンの表示/非表示を更新
	function updateAddButtonVisibility($container) {
		var $rows = $container.find('.kintone-token-row');
		var $addButton = $container.find('.kintone-token-add');
		if ($rows.length >= MAX_TOKENS) {
			$addButton.hide();
		} else {
			$addButton.show();
		}
	}

	// ページ読み込み時に各コンテナの状態を初期化
	$(document).ready(function() {
		$('.kintone-token-fields').each(function() {
			updateAddButtonVisibility($(this));
		});
	});

})(jQuery);

/**
 * アコーディオン機能
 */
(function($){
	'use strict';

	// アコーディオン開閉
	$(document).on('click', '.kf-accordion-header', function(e) {
		e.preventDefault();

		var $header = $(this);
		var $content = $header.next('.kf-accordion-content');
		var isExpanded = $header.attr('aria-expanded') === 'true';

		if (isExpanded) {
			// 閉じる
			$header.attr('aria-expanded', 'false');
			$content.slideUp(200);
		} else {
			// 開く
			$header.attr('aria-expanded', 'true');
			$content.slideDown(200);
		}
	});

	// キーボードアクセシビリティ
	$(document).on('keydown', '.kf-accordion-header', function(e) {
		// Enter または Space キーで開閉
		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault();
			$(this).trigger('click');
		}
	});

})(jQuery);

/**
 * フィールド検索機能
 */
(function($){
	'use strict';

	var searchTimeout;

	// 検索入力
	$(document).on('input', '.kf-search-input', function() {
		var $input = $(this);
		var query = $input.val().toLowerCase().trim();
		var $appSection = $input.closest('.kf-app-section');
		var $wrapper = $input.closest('.kf-search-wrapper');
		var $resultsInfo = $appSection.find('.kf-search-results-info');

		// 検索値の有無でクリアボタン表示
		if (query.length > 0) {
			$wrapper.addClass('has-value');
		} else {
			$wrapper.removeClass('has-value');
		}

		// デバウンス処理
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(function() {
			performSearch($appSection, query, $resultsInfo);
		}, 150);
	});

	// 検索クリアボタン
	$(document).on('click', '.kf-search-clear', function(e) {
		e.preventDefault();
		var $appSection = $(this).closest('.kf-app-section');
		var $input = $appSection.find('.kf-search-input');
		var $wrapper = $(this).closest('.kf-search-wrapper');
		var $resultsInfo = $appSection.find('.kf-search-results-info');

		$input.val('').trigger('focus');
		$wrapper.removeClass('has-value');
		clearSearch($appSection, $resultsInfo);
	});

	/**
	 * 検索実行
	 */
	function performSearch($appSection, query, $resultsInfo) {
		var $accordionGroups = $appSection.find('.kf-accordion-group');

		if (query.length === 0) {
			clearSearch($appSection, $resultsInfo);
			return;
		}

		var totalMatches = 0;
		var matchedGroups = 0;

		$accordionGroups.each(function() {
			var $group = $(this);
			var $header = $group.find('.kf-accordion-header');
			var $content = $group.find('.kf-accordion-content');
			var $rows = $group.find('.kf-field-row');
			var $notSupportedItems = $group.find('.kf-not-supported-item');
			var groupMatches = 0;

			// フィールド行の検索
			$rows.each(function() {
				var $row = $(this);
				var label = $row.data('field-label') || '';
				var code = $row.data('field-code') || '';

				if (label.indexOf(query) !== -1 || code.indexOf(query) !== -1) {
					$row.removeClass('kf-field-row--hidden').addClass('kf-field-row--highlighted');
					groupMatches++;
				} else {
					$row.addClass('kf-field-row--hidden').removeClass('kf-field-row--highlighted');
				}
			});

			// Not Supported アイテムの検索
			$notSupportedItems.each(function() {
				var $item = $(this);
				var text = $item.text().toLowerCase();

				if (text.indexOf(query) !== -1) {
					$item.show();
					groupMatches++;
				} else {
					$item.hide();
				}
			});

			// グループのカウント更新
			var $count = $header.find('.kf-accordion-count');
			$count.text(groupMatches);

			// マッチがあるグループは展開、なければ非表示
			if (groupMatches > 0) {
				$group.show();
				if ($header.attr('aria-expanded') !== 'true') {
					$header.attr('aria-expanded', 'true');
					$content.slideDown(200);
				}
				matchedGroups++;
			} else {
				$group.hide();
			}

			totalMatches += groupMatches;
		});

		// 結果情報を表示
		if (totalMatches > 0) {
			$resultsInfo.text(totalMatches + ' 件のフィールドが見つかりました').show();
		} else {
			$resultsInfo.text('該当するフィールドが見つかりません').show();
		}
	}

	/**
	 * 検索クリア
	 */
	function clearSearch($appSection, $resultsInfo) {
		var $accordionGroups = $appSection.find('.kf-accordion-group');

		$accordionGroups.each(function() {
			var $group = $(this);
			var $header = $group.find('.kf-accordion-header');
			var $content = $group.find('.kf-accordion-content');
			var $rows = $group.find('.kf-field-row');
			var $notSupportedItems = $group.find('.kf-not-supported-item');
			var $count = $header.find('.kf-accordion-count');
			var originalCount = $count.data('original-count');
			var isNotSupported = $group.hasClass('kf-accordion-group--not-supported');

			// すべての行を表示
			$rows.removeClass('kf-field-row--hidden kf-field-row--highlighted');
			$notSupportedItems.show();

			// グループを表示
			$group.show();

			// カウントを元に戻す
			$count.text(originalCount);

			// Not Supported グループは折りたたむ
			if (isNotSupported) {
				$header.attr('aria-expanded', 'false');
				$content.slideUp(200);
			}
		});

		// 結果情報を非表示
		$resultsInfo.hide();
	}

})(jQuery);

/**
 * Select2 初期化（CF7 Mail Tag セレクトボックス）
 */
(function($){
	'use strict';

	// Select2 初期化関数
	function initSelect2() {
		$('.kf-cf7-mailtag-select').each(function() {
			// 既にSelect2が初期化されている場合はスキップ
			if ($(this).hasClass('select2-hidden-accessible')) {
				return;
			}

			$(this).select2({
				placeholder: '-- 選択 --',
				allowClear: true,
				width: '200px',
				dropdownAutoWidth: true,
				language: {
					noResults: function() {
						return '該当するタグが見つかりません';
					},
					searching: function() {
						return '検索中...';
					}
				}
			});
		});
	}

	// ページ読み込み時に初期化
	$(document).ready(function() {
		initSelect2();
	});

	// GETボタンクリック後のページリロードに対応
	// （フォーム保存後に再描画されるため、MutationObserverで監視）
	if (typeof MutationObserver !== 'undefined') {
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (mutation.addedNodes.length > 0) {
					// 新しいノードが追加されたら Select2 を再初期化
					setTimeout(initSelect2, 100);
				}
			});
		});

		$(document).ready(function() {
			var container = document.querySelector('#kintone_form_setting');
			if (container) {
				observer.observe(container, {
					childList: true,
					subtree: true
				});
			}
		});
	}

})(jQuery);
