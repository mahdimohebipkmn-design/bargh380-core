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
});
