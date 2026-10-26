jQuery(function ($) {
	$('.b380-admin-color').wpColorPicker();
	$(document).on('click', '.b380-admin-media', function () {
		const button = $(this);
		const type = button.data('media-type');
		const input = button.siblings('input');
		const frame = wp.media({
			title: type === 'video' ? 'انتخاب ویدئوی معرفی' : 'انتخاب تصویر دوره',
			button: { text: 'استفاده از این فایل' },
			library: { type: type === 'video' ? 'video' : 'image' },
			multiple: false
		});
		frame.on('select', function () {
			const media = frame.state().get('selection').first().toJSON();
			input.val(media.url).trigger('change');
			button.closest('.b380-admin-field').find('.b380-admin-preview').attr('src', media.url).prop('hidden', false);
		});
		frame.open();
	});
	$(document).on('click', '[data-add-chapter]', function () {
		const list = $('[data-chapters]');
		const index = Date.now();
		const template = $('#b380-chapter-template').html().replaceAll('999999', String(index));
		list.append(template);
		list.children('[data-chapter]').last().find('[name$="[title]"]').trigger('focus');
		list.children('[data-chapter]').each(function (position) {
			$(this).find('[data-chapter-number]').text(position + 1);
		});
	});
	$(document).on('click', '[data-remove-chapter]', function () {
		$(this).closest('[data-chapter]').remove();
		$('[data-chapters]').children('[data-chapter]').each(function (position) {
			$(this).find('[data-chapter-number]').text(position + 1);
		});
	});
});
