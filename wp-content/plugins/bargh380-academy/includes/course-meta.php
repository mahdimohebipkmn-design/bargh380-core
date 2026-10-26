<?php
defined( 'ABSPATH' ) || exit;

/** Product-owned content and appearance for the course landing page. */
final class B380_Academy_Course_Meta {
	const PREFIX = '_b380_course_';

	public static function boot() {
		add_action( 'add_meta_boxes_product', array( __CLASS__, 'add_box' ) );
		add_action( 'save_post_product', array( __CLASS__, 'save' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_action( 'init', array( __CLASS__, 'seed_reference_course' ), 30 );
	}

	public static function groups() {
		return array(
			'hero' => array( 'title' => '۱. بخش آغازین و ثبت‌نام', 'fields' => array(
				'eyebrow' => array( 'label' => 'برچسب بالای عنوان', 'type' => 'text' ),
				'hero_title' => array( 'label' => 'عنوان اصلی', 'type' => 'text', 'hint' => 'اگر خالی باشد، نام محصول نمایش داده می‌شود.' ),
				'hero_subtitle' => array( 'label' => 'زیرعنوان', 'type' => 'textarea', 'hint' => 'اگر خالی باشد، توضیح کوتاه محصول نمایش داده می‌شود.' ),
				'hero_video' => array( 'label' => 'ویدئوی معرفی', 'type' => 'video', 'hint' => 'فایل MP4/WebM از کتابخانه رسانه یا نشانی مستقیم. در نبود ویدئو، تصویر محصول نمایش داده می‌شود.' ),
				'hero_background' => array( 'label' => 'تصویر پس‌زمینه بخش آغازین', 'type' => 'image' ),
				'hero_icon' => array( 'label' => 'آیکون کنار برچسب آغازین', 'type' => 'text', 'hint' => 'یک ایموجی یا نماد کوتاه مثل ⚡، 🎓 یا ✦.' ),
				'accent_color' => array( 'label' => 'رنگ اصلی صفحه', 'type' => 'color', 'hint' => 'رنگ دکمه‌ها، خطوط و پس‌زمینه‌های ملایم؛ پیش‌فرض سبز.' ),
				'accent_secondary' => array( 'label' => 'رنگ دوم شیشه و دکمه‌ها', 'type' => 'color', 'hint' => 'برای گرادیان و انعکاس؛ پیش‌فرض آبی.' ),
				'cta_label' => array( 'label' => 'متن دکمه ثبت‌نام', 'type' => 'text' ),
				'hero_facts' => array( 'label' => 'سه مزیت نخست', 'type' => 'lines', 'hint' => 'هر سطر: آیکون | عنوان | توضیح. نمونه: ⏱ | مدت دوره | ۸۰ ساعت آموزش' ),
			) ),
			'overview' => array( 'title' => '۲. معرفی و ویژگی‌ها', 'fields' => array(
				'promise_title' => array( 'label' => 'عنوان بخش ویژگی‌ها', 'type' => 'text' ),
				'promise_subtitle' => array( 'label' => 'زیرعنوان بخش ویژگی‌ها', 'type' => 'textarea' ),
				'benefits' => array( 'label' => 'کارت‌های ویژگی', 'type' => 'lines', 'hint' => 'هر سطر: آیکون | عنوان | توضیح' ),
				'overview_title' => array( 'label' => 'عنوان معرفی تصویری', 'type' => 'text' ),
				'overview_body' => array( 'label' => 'متن معرفی تصویری', 'type' => 'textarea', 'hint' => 'اگر خالی باشد، توضیح اصلی محصول نمایش داده می‌شود.' ),
				'overview_image' => array( 'label' => 'تصویر معرفی', 'type' => 'image', 'hint' => 'در نبود تصویر، تصویر اصلی محصول نمایش داده می‌شود.' ),
			) ),
			'curriculum' => array( 'title' => '۳. سرفصل‌ها و مخاطبان', 'fields' => array(
				'learning_title' => array( 'label' => 'عنوان سرفصل‌ها', 'type' => 'text' ),
				'learning_subtitle' => array( 'label' => 'توضیح کوتاه سرفصل‌ها', 'type' => 'textarea' ),
				'chapters' => array( 'label' => 'فصل‌ها و سرفصل‌ها', 'type' => 'chapters', 'hint' => 'هر تعداد فصل لازم دارید اضافه کنید. برای هر کادر، عنوان، توضیح و تصویر یا کد SVG جداگانه وارد کنید.' ),
				'audience_title' => array( 'label' => 'عنوان مخاطبان', 'type' => 'text' ),
				'audience_icon' => array( 'label' => 'آیکون هر مخاطب مناسب', 'type' => 'text' ),
				'audience_items' => array( 'label' => 'این دوره برای چه کسانی است؟', 'type' => 'lines', 'hint' => 'هر سطر یک مورد.' ),
				'not_for_title' => array( 'label' => 'عنوان «برای چه کسانی نیست؟»', 'type' => 'text' ),
				'not_for_icon' => array( 'label' => 'آیکون هر مخاطب نامناسب', 'type' => 'text' ),
				'not_for_items' => array( 'label' => 'این دوره برای چه کسانی نیست؟', 'type' => 'lines', 'hint' => 'هر سطر یک مورد.' ),
			) ),
			'details' => array( 'title' => '۴. چرایی دوره، پیش‌نیاز و مدرس', 'fields' => array(
				'why_title' => array( 'label' => 'عنوان بخش چرایی دوره', 'type' => 'text' ),
				'why_body' => array( 'label' => 'متن چرایی دوره', 'type' => 'textarea' ),
				'prerequisite_title' => array( 'label' => 'عنوان پیش‌نیاز', 'type' => 'text' ),
				'prerequisite_body' => array( 'label' => 'متن پیش‌نیاز', 'type' => 'textarea' ),
				'difference_title' => array( 'label' => 'عنوان تمایز دوره', 'type' => 'text' ),
				'difference_body' => array( 'label' => 'متن تمایز دوره', 'type' => 'textarea' ),
				'detail_icons' => array( 'label' => 'آیکون‌های چرایی، پیش‌نیاز و تمایز', 'type' => 'text', 'hint' => 'سه نماد به ترتیب و با | جدا شوند. نمونه: ✦ | ✓ | ◆' ),
				'instructor_name' => array( 'label' => 'نام مدرس', 'type' => 'text', 'hint' => 'در نبود مقدار، از فیلد «مدرس» قبلی استفاده می‌شود.' ),
				'instructor_role' => array( 'label' => 'عنوان شغلی مدرس', 'type' => 'text' ),
				'instructor_image' => array( 'label' => 'تصویر مدرس', 'type' => 'image' ),
				'instructor_icon' => array( 'label' => 'آیکون دستاوردهای مدرس', 'type' => 'text' ),
				'instructor_achievements' => array( 'label' => 'سوابق و دستاوردهای مدرس', 'type' => 'lines', 'hint' => 'هر سطر یک مورد.' ),
			) ),
			'closing' => array( 'title' => '۵. پرسش‌های متداول و دعوت پایانی', 'fields' => array(
				'faq_title' => array( 'label' => 'عنوان پرسش‌های متداول', 'type' => 'text' ),
				'faq_items' => array( 'label' => 'پرسش‌ها و پاسخ‌ها', 'type' => 'lines', 'hint' => 'هر سطر: پرسش | پاسخ. فقط پرسش‌های همین محصول را وارد کنید.' ),
				'closing_title' => array( 'label' => 'عنوان دعوت پایانی', 'type' => 'text' ),
				'closing_body' => array( 'label' => 'متن دعوت پایانی', 'type' => 'textarea' ),
				'support_label' => array( 'label' => 'متن دکمه مشاوره', 'type' => 'text' ),
				'support_icon' => array( 'label' => 'آیکون دکمه مشاوره', 'type' => 'text' ),
				'support_url' => array( 'label' => 'نشانی دکمه مشاوره', 'type' => 'url', 'hint' => 'مانند tel:09922335706 یا لینک صفحه تماس.' ),
			) ),
			'labels' => array( 'title' => '۶. ریزنوشته‌ها و متن ناوبری', 'fields' => array(
				'elementor_mode' => array( 'label' => 'نمایش چیدمان المنتور', 'type' => 'checkbox', 'hint' => 'پس از قرار دادن ابزارک «برگه دوره آکادمی» در ویرایشگر المنتور، این گزینه را فعال کنید. در حالت غیرفعال، قالب آمادهٔ افزونه نمایش داده می‌شود.' ),
				'price_label' => array( 'label' => 'برچسب قیمت', 'type' => 'text' ),
				'promise_kicker' => array( 'label' => 'برچسب بخش ویژگی‌ها', 'type' => 'text' ),
				'overview_kicker' => array( 'label' => 'برچسب معرفی', 'type' => 'text' ),
				'learning_kicker' => array( 'label' => 'برچسب سرفصل‌ها', 'type' => 'text' ),
				'audience_kicker' => array( 'label' => 'برچسب مخاطبان', 'type' => 'text' ),
				'not_for_kicker' => array( 'label' => 'برچسب مخاطبان نامناسب', 'type' => 'text' ),
				'instructor_kicker' => array( 'label' => 'برچسب مدرس', 'type' => 'text' ),
				'faq_kicker' => array( 'label' => 'برچسب پرسش‌ها', 'type' => 'text' ),
				'closing_kicker' => array( 'label' => 'برچسب دعوت پایانی', 'type' => 'text' ),
				'scroll_label' => array( 'label' => 'متن پیوند اسکرول در آغاز', 'type' => 'text' ),
				'unavailable_label' => array( 'label' => 'متن هنگام بسته بودن ثبت‌نام', 'type' => 'text' ),
				'all_courses_label' => array( 'label' => 'متن پیوند همهٔ دوره‌ها', 'type' => 'text' ),
				'cart_label' => array( 'label' => 'متن پیوند سبد خرید', 'type' => 'text' ),
				'nav_overview' => array( 'label' => 'متن ناوبری معرفی', 'type' => 'text' ),
				'nav_learning' => array( 'label' => 'متن ناوبری سرفصل‌ها', 'type' => 'text' ),
				'nav_instructor' => array( 'label' => 'متن ناوبری مدرس', 'type' => 'text' ),
				'nav_faq' => array( 'label' => 'متن ناوبری سؤال‌ها', 'type' => 'text' ),
			) ),
		);
	}

	public static function get( $product_id, $key, $fallback = '' ) {
		$value = get_post_meta( $product_id, self::PREFIX . $key, true );
		return '' === $value ? $fallback : $value;
	}

	public static function lines( $value, $paired = false ) {
		$rows = preg_split( '/\r\n|\r|\n/', (string) $value );
		$result = array();
		foreach ( $rows as $row ) {
			$row = trim( $row );
			if ( '' === $row ) {
				continue;
			}
			if ( $paired ) {
				$parts = array_map( 'trim', explode( '|', $row, 2 ) );
				$result[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
			} else {
				$result[] = $row;
			}
		}
		return $result;
	}

	public static function icon_lines( $value ) {
		$result = array();
		foreach ( self::lines( $value ) as $row ) {
			$parts = array_map( 'trim', explode( '|', $row, 3 ) );
			if ( count( $parts ) >= 3 ) {
				$result[] = array( $parts[0], $parts[1], $parts[2] );
			} elseif ( count( $parts ) === 2 ) {
				$result[] = array( '✦', $parts[0], $parts[1] );
			} else {
				$result[] = array( '✦', $parts[0], '' );
			}
		}
		return $result;
	}

	public static function chapters( $product_id ) {
		if ( metadata_exists( 'post', $product_id, self::PREFIX . 'chapters' ) ) {
			$chapters = get_post_meta( $product_id, self::PREFIX . 'chapters', true );
			return is_array( $chapters ) ? $chapters : array();
		}
		$legacy = self::icon_lines( self::get( $product_id, 'learning_cards' ) );
		if ( ! $legacy ) {
			$legacy = array_map( static function ( $title ) { return array( '✦', $title, '' ); }, self::lines( get_post_meta( $product_id, '_b380_course_syllabus', true ) ) );
		}
		return array_map( static function ( $item ) {
			return array( 'icon' => $item[0], 'title' => $item[1], 'description' => $item[2], 'image' => '', 'svg' => '' );
		}, $legacy );
	}

	private static function render_chapter( $chapter, $index ) {
		$chapter = wp_parse_args( $chapter, array( 'icon' => '', 'title' => '', 'description' => '', 'image' => '', 'svg' => '' ) );
		$base = 'b380_course_chapters[' . $index . ']';
		echo '<div class="b380-admin-chapter" data-chapter><div class="b380-admin-chapter__top"><strong>فصل <span data-chapter-number>' . esc_html( $index + 1 ) . '</span></strong><button type="button" class="button-link-delete" data-remove-chapter>حذف فصل</button></div>';
		foreach ( array( 'title' => 'عنوان فصل', 'description' => 'توضیحات فصل', 'icon' => 'نماد کوتاه، در نبود تصویر/SVG', 'image' => 'نشانی تصویر یا فایل SVG', 'svg' => 'کد SVG اختیاری' ) as $key => $label ) {
			$name = $base . '[' . $key . ']';
			echo '<label class="b380-admin-chapter__field"><span>' . esc_html( $label ) . '</span>';
			if ( in_array( $key, array( 'description', 'svg' ), true ) ) {
				echo '<textarea name="' . esc_attr( $name ) . '" rows="' . ( 'svg' === $key ? 4 : 2 ) . '" dir="' . ( 'svg' === $key ? 'ltr' : 'rtl' ) . '">' . esc_textarea( $chapter[ $key ] ) . '</textarea>';
			} else {
				echo '<div class="b380-admin-input-row"><input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $chapter[ $key ] ) . '" class="regular-text"' . ( 'image' === $key ? ' dir="ltr"' : '' ) . '>';
				if ( 'image' === $key ) {
					echo '<button type="button" class="button b380-admin-media" data-media-type="image">انتخاب از رسانه</button>';
				}
				echo '</div>';
			}
			echo '</label>';
		}
		echo '</div>';
	}

	public static function add_box() {
		add_meta_box( 'b380-academy-course-settings', 'طراحی برگه دوره آکادمی', array( __CLASS__, 'render_box' ), 'product', 'normal', 'default' );
	}

	public static function render_box( $post ) {
		wp_nonce_field( 'b380_academy_save_course', 'b380_academy_nonce' );
		echo '<p class="b380-admin-intro">این تنظیمات فقط برای محصولاتی که در دستهٔ آکادمی هستند نمایش داده می‌شود. قیمت، موجودی و تصویر اصلی را در تنظیمات معمول ووکامرس تغییر دهید. بخش‌های خالی در صفحه نمایش داده نمی‌شوند.</p>';
		foreach ( self::groups() as $group ) {
			echo '<details class="b380-admin-group" open><summary>' . esc_html( $group['title'] ) . '</summary><div class="b380-admin-grid">';
			foreach ( $group['fields'] as $key => $field ) {
				$value = self::get( $post->ID, $key );
				$name  = 'b380_course_meta[' . esc_attr( $key ) . ']';
				$id    = 'b380-course-' . esc_attr( $key );
				$type  = $field['type'];
				if ( 'chapters' === $type ) {
					echo '<div class="b380-admin-field b380-admin-field--wide"><strong>' . esc_html( $field['label'] ) . '</strong><p class="description">' . esc_html( $field['hint'] ) . '</p><input type="hidden" name="b380_course_chapters_present" value="1"><div class="b380-admin-chapters" data-chapters>';
					foreach ( self::chapters( $post->ID ) as $index => $chapter ) {
						self::render_chapter( $chapter, $index );
					}
					echo '</div><button type="button" class="button button-secondary" data-add-chapter>+ افزودن فصل</button><template id="b380-chapter-template">';
					self::render_chapter( array(), 999999 );
					echo '</template></div>';
					continue;
				}
				if ( 'checkbox' === $type ) {
					echo '<div class="b380-admin-field b380-admin-field--wide"><label><input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( $value, '1', false ) . '> ' . esc_html( $field['label'] ) . '</label><p class="description">' . esc_html( $field['hint'] ) . '</p></div>';
					continue;
				}
				$wide  = in_array( $type, array( 'textarea', 'lines', 'image', 'video' ), true );
				echo '<div class="b380-admin-field' . ( $wide ? ' b380-admin-field--wide' : '' ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
				if ( in_array( $type, array( 'textarea', 'lines' ), true ) ) {
					echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="' . ( 'lines' === $type ? '5' : '3' ) . '">' . esc_textarea( $value ) . '</textarea>';
				} else {
					$input_type = in_array( $type, array( 'url', 'color' ), true ) ? ( 'color' === $type ? 'text' : 'url' ) : 'text';
					echo '<div class="b380-admin-input-row"><input id="' . esc_attr( $id ) . '" type="' . esc_attr( $input_type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" class="' . ( 'color' === $type ? 'b380-admin-color' : 'regular-text' ) . '" dir="' . ( in_array( $type, array( 'image', 'video', 'url' ), true ) ? 'ltr' : 'rtl' ) . '">';
					if ( in_array( $type, array( 'image', 'video' ), true ) ) {
						echo '<button type="button" class="button b380-admin-media" data-media-type="' . esc_attr( $type ) . '">انتخاب از رسانه</button>';
					}
					echo '</div>';
					if ( 'image' === $type ) {
						echo '<img class="b380-admin-preview" src="' . esc_url( $value ) . '" alt=""' . ( $value ? '' : ' hidden' ) . '>';
					}
				}
				if ( ! empty( $field['hint'] ) ) {
					echo '<p class="description">' . esc_html( $field['hint'] ) . '</p>';
				}
				echo '</div>';
			}
			echo '</div></details>';
		}
	}

	public static function save( $post_id ) {
		if ( ! isset( $_POST['b380_academy_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['b380_academy_nonce'] ) ), 'b380_academy_save_course' ) || ! current_user_can( 'edit_post', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}
		$submitted = isset( $_POST['b380_course_meta'] ) && is_array( $_POST['b380_course_meta'] ) ? wp_unslash( $_POST['b380_course_meta'] ) : array();
		foreach ( self::groups() as $group ) {
			foreach ( $group['fields'] as $key => $field ) {
				if ( ! isset( $submitted[ $key ] ) || ! is_string( $submitted[ $key ] ) ) {
					continue;
				}
				$raw = trim( $submitted[ $key ] );
				if ( 'color' === $field['type'] ) {
					$value = sanitize_hex_color( $raw );
				} elseif ( 'checkbox' === $field['type'] ) {
					$value = '1' === $raw ? '1' : '0';
				} elseif ( in_array( $field['type'], array( 'url', 'image', 'video' ), true ) ) {
					$value = esc_url_raw( $raw, 'url' === $field['type'] ? array( 'http', 'https', 'tel' ) : array( 'http', 'https' ) );
				} elseif ( in_array( $field['type'], array( 'textarea', 'lines' ), true ) ) {
					$value = sanitize_textarea_field( $raw );
				} else {
					$value = sanitize_text_field( $raw );
				}
				update_post_meta( $post_id, self::PREFIX . $key, $value );
			}
		}
		if ( isset( $_POST['b380_course_chapters_present'] ) ) {
			$rows = isset( $_POST['b380_course_chapters'] ) && is_array( $_POST['b380_course_chapters'] ) ? wp_unslash( $_POST['b380_course_chapters'] ) : array();
			$chapters = array();
			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) { continue; }
				$title = sanitize_text_field( $row['title'] ?? '' );
				if ( '' === $title ) { continue; }
				$chapters[] = array(
					'title' => $title,
					'description' => sanitize_textarea_field( $row['description'] ?? '' ),
					'icon' => sanitize_text_field( $row['icon'] ?? '' ),
					'image' => esc_url_raw( $row['image'] ?? '' ),
					'svg' => self::sanitize_svg( $row['svg'] ?? '' ),
				);
			}
			update_post_meta( $post_id, self::PREFIX . 'chapters', $chapters );
		}
	}

	public static function sanitize_svg( $svg ) {
		$svg = trim( (string) $svg );
		if ( '' === $svg || false === stripos( $svg, '<svg' ) ) { return ''; }
		$allowed = array(
			'svg' => array( 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'role' => true, 'aria-hidden' => true ),
			'g' => array( 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'transform' => true ),
			'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'transform' => true ),
			'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
			'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
			'line' => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true ),
			'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
			'polygon' => array( 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
			'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
		);
		return wp_kses( $svg, $allowed );
	}

	public static function admin_assets( $hook ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! $screen || 'product' !== $screen->post_type ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		$plugin_file = dirname( __DIR__ ) . '/bargh380-academy.php';
		wp_enqueue_style( 'b380-academy-admin', plugins_url( 'assets/admin.css', $plugin_file ), array(), B380_Academy_Store::VERSION );
		wp_enqueue_script( 'b380-academy-admin', plugins_url( 'assets/admin.js', $plugin_file ), array( 'jquery', 'wp-color-picker' ), B380_Academy_Store::VERSION, true );
	}

	/** Copy the owner's existing AI course page once into editable product fields. */
	public static function seed_reference_course() {
		if ( get_option( 'b380_academy_reference_seed_v1' ) || ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$product = wc_get_product( 12506 );
		if ( ! $product || false === strpos( $product->get_name(), 'هوش مصنوعی' ) ) {
			return;
		}
		$seed = array(
			'eyebrow' => 'برای اولین بار در ایران',
			'hero_title' => 'مهندس هوش مصنوعی',
			'hero_subtitle' => 'آموزش جامع هوش مصنوعی برای مهندس برق و اتوماسیون صنعتی، کاملاً پروژه‌محور',
			'hero_video' => 'https://bargh380.ir/wp-content/uploads/2026/02/8194733-hd_1920_1080_25fps.mp4',
			'hero_icon' => '⚡',
			'accent_color' => '#14e884',
			'accent_secondary' => '#315af2',
			'cta_label' => 'ثبت‌نام و شروع یادگیری',
			'hero_facts' => "پروژه‌محور | از مفاهیم AI تا اجرای پروژه‌های واقعی صنعتی\nمدت زمان دوره | ۸۰ ساعت آموزش تخصصی\nجلسات جدید | انتشار منظم آموزش‌های تازه",
			'promise_title' => 'کامل‌ترین دوره هوش مصنوعی برای مهندسان برق و اتوماسیون صنعتی',
			'promise_subtitle' => 'یادگیری کاملاً پروژه‌محور با تمرکز بر کاربردهای واقعی صنعت',
			'benefits' => "سطح آموزش بالا | استفاده از جدیدترین تجهیزات آموزشی\nکاملاً عملی و پروژه‌محور | ساختار مرحله‌به‌مرحله از پایه تا پیشرفته\nپشتیبانی اختصاصی | در مسیر یادگیری همراه شما هستیم",
			'overview_title' => 'هوش مصنوعی را وارد دنیای برق و اتوماسیون صنعتی کنید',
			'overview_body' => 'در دورهٔ «هوش مصنوعی برای مهندسان برق و اتوماسیون صنعتی» یاد می‌گیرید چطور طراحی، برنامه‌نویسی، تحلیل و عیب‌یابی پروژه‌های برق و اتوماسیون را با کمک هوش مصنوعی سریع‌تر و هوشمندتر انجام دهید. از کار با PLC، HMI و SCADA و طراحی تابلو برق گرفته تا تحلیل پروژه، تولید مستندات و خودکارسازی فرایندهای مهندسی را متناسب با نیازهای واقعی صنعت می‌آموزید.',
			'overview_image' => 'https://bargh380.ir/wp-content/uploads/2026/08/bargh380-7-2-1024x576.png',
			'learning_title' => 'در این دوره به‌صورت پروژه‌محور چه می‌آموزید؟',
			'learning_subtitle' => 'هوش مصنوعی را وارد دنیای برق و اتوماسیون صنعتی کنید',
			'learning_cards' => "اصول حرفه‌ای استفاده از AI | روش درست و حرفه‌ای به‌کارگیری هوش مصنوعی\nطراحی تابلو برق با AI | تحلیل نیازمندی‌ها و بررسی اطلاعات پروژه\nبرنامه‌نویسی PLC | تولید، بررسی و اصلاح برنامه‌های PLC\nعیب‌یابی PLC | تحلیل مرحله‌به‌مرحله خطاها و علائم سیستم\nطراحی HMI و SCADA | طراحی منطق، صفحات، تگ‌ها و سناریوهای کنترلی\nطراحی و تحلیل نقشه | بررسی نقشه تابلوها و اسناد پروژه\nمستندسازی پروژه | تولید گزارش، BOM و Vendor List\nساخت Agent | ساخت دستیارهای هوشمند برای وظایف مشخص\nRAG و دانش اختصاصی | اتصال AI به مستندات و داده‌های پروژه\nMCP و ابزارهای AI | اتصال مدل‌های هوش مصنوعی به ابزارها\nاتوماسیون کارها | خودکارسازی فرایندهای تکراری مهندسی\nمدیریت پروژه با AI | تحلیل و تصمیم‌گیری در مراحل پروژه",
			'audience_title' => 'این دوره برای چه کسانی ساخته شده است؟',
			'audience_icon' => '✓',
			'audience_items' => "صاحبان شرکت‌ها و کسب‌وکارهایی که می‌خواهند پروژه‌هایشان را هوشمندتر اداره کنند.\nتعمیرکاران صنعتی که می‌خواهند سریع‌تر و دقیق‌تر کار کنند.\nافرادی که می‌خواهند با کمک AI پروژه‌هایشان را توسعه دهند.\nعلاقه‌مندان به کاربرد عملی هوش مصنوعی در PLC و اتوماسیون.",
			'not_for_title' => 'چه کسانی نباید این دوره را بخرند؟',
			'not_for_icon' => '−',
			'not_for_items' => "کسانی که فقط تولید متن ساده با ChatGPT می‌خواهند.\nکسانی که صرفاً دنبال آموزش تئوری هوش مصنوعی هستند.",
			'why_title' => 'چرا هوش مصنوعی؟',
			'why_body' => 'هوش مصنوعی سرعت و توان یک تیم فنی را در اختیار شما می‌گذارد. در این دوره یاد می‌گیرید از آن برای کارهای واقعی مهندسی استفاده کنید.',
			'prerequisite_title' => 'پیش‌نیاز دوره',
			'prerequisite_body' => 'این دوره پیش‌نیاز تخصصی ندارد و از صفر شروع می‌شود. آشنایی مقدماتی با کامپیوتر کمک می‌کند بیشترین بهره را از آموزش ببرید.',
			'difference_title' => 'فراتر از ابزارهای معمولی',
			'difference_body' => 'قرار نیست فقط چند ابزار برای استفاده روزانه معرفی کنیم؛ اصول درست و حرفه‌ای کار با هوش مصنوعی را می‌آموزید تا در پروژه‌های واقعی نتیجه بگیرید.',
			'detail_icons' => '✦ | ✓ | ◆',
			'instructor_name' => 'مهدی محبی',
			'instructor_role' => 'مدرس و مؤسس برق ۳۸۰',
			'instructor_icon' => '★',
			'instructor_image' => 'https://bargh380.ir/wp-content/uploads/2026/10/ChatGPT-Image-Oct-4-2026-02_55_04-PM-1024x1024.webp',
			'instructor_achievements' => "مدیر و مؤسس وب‌سایت برق ۳۸۰\nبیش از ۸ سال تجربه در مهندسی برق و اتوماسیون\nبیش از ۱۰۰ پروژه صنعتی\nطراح و برنامه‌نویس سیستم‌های کنترل صنعتی",
			'faq_title' => 'پرسش‌های متداول این دوره',
			'faq_items' => "اگر برنامه‌نویسی بلد نباشم، می‌توانم شرکت کنم؟ | بله. مفاهیم از پایه توضیح داده می‌شوند و بخش‌های عملی مرحله‌به‌مرحله پیش می‌روند.\nآیا آشنایی با PLC لازم است؟ | آشنایی قبلی کمک می‌کند، اما دوره از مفاهیم پایه و کاربردهای عملی شروع می‌شود.",
			'closing_title' => 'آینده مهندسی برق با مهارت‌های تازه ساخته می‌شود',
			'closing_body' => 'هوش مصنوعی شیوهٔ طراحی، برنامه‌نویسی، تحلیل و مستندسازی پروژه‌های مهندسی را تغییر می‌دهد. مسیر یادگیری را از همین‌جا شروع کنید.',
			'support_label' => 'مشاوره خرید',
			'support_icon' => '💬',
			'support_url' => 'tel:09922335706',
		);
		foreach ( $seed as $key => $value ) {
			if ( ! metadata_exists( 'post', 12506, self::PREFIX . $key ) ) {
				update_post_meta( 12506, self::PREFIX . $key, $value );
			}
		}
		update_option( 'b380_academy_reference_seed_v1', 1, false );
	}
}
