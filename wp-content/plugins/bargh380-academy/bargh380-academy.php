<?php
/**
 * Plugin Name: Bargh380 Academy Store
 * Description: A focused, RTL WooCommerce storefront for Bargh380 training courses.
 * Version: 0.1.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Text Domain: bargh380-academy
 */

defined( 'ABSPATH' ) || exit;

final class B380_Academy_Store {
	const VERSION = '0.1.0';
	const ACADEMY_PAGE_OPTION = 'b380_academy_page_id';
	const CATEGORY_OPTION = 'b380_academy_category_id';

	public static function boot() {
		add_shortcode( 'b380_academy', array( __CLASS__, 'catalog' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'template_include', array( __CLASS__, 'course_template' ), 999 );
		add_action( 'admin_notices', array( __CLASS__, 'dependency_notice' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'course_fields' ) );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_course_fields' ) );
	}

	public static function activate() {
		$page_id = self::ensure_page( 'academy', 'آکادمی برق ۳۸۰', '[b380_academy]' );
		if ( $page_id ) {
			update_option( self::ACADEMY_PAGE_OPTION, $page_id );
		}

		// Only fill genuinely missing WooCommerce pages. Existing store pages stay untouched.
		if ( class_exists( 'WooCommerce' ) ) {
			foreach ( array(
				'cart'     => array( 'academy-cart', 'سبد خرید', '[woocommerce_cart]' ),
				'checkout' => array( 'academy-checkout', 'تکمیل خرید', '[woocommerce_checkout]' ),
			) as $kind => $details ) {
				$existing = (int) get_option( 'woocommerce_' . $kind . '_page_id' );
				if ( $existing && 'publish' === get_post_status( $existing ) ) {
					continue;
				}
				$new_page = self::ensure_page( $details[0], $details[1], $details[2] );
				if ( $new_page ) {
					update_option( 'woocommerce_' . $kind . '_page_id', $new_page );
				}
			}
		}
	}

	private static function ensure_page( $slug, $title, $shortcode ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			// Do not replace a manually maintained page with the same slug.
			return false !== strpos( $existing->post_content, $shortcode ) ? (int) $existing->ID : 0;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $shortcode,
		), true );
		return is_wp_error( $id ) ? 0 : (int) $id;
	}

	public static function dependency_notice() {
		if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-warning"><p>برای نمایش فروشگاه آکادمی برق ۳۸۰، ووکامرس باید فعال باشد.</p></div>';
		}
	}

	public static function academy_url() {
		$page_id = (int) get_option( self::ACADEMY_PAGE_OPTION );
		return $page_id && 'publish' === get_post_status( $page_id ) ? get_permalink( $page_id ) : home_url( '/academy/' );
	}

	public static function category_id() {
		$saved = (int) get_option( self::CATEGORY_OPTION, 586 );
		$term  = get_term( $saved, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			return $saved;
		}
		$term = get_term_by( 'name', 'آکادمی برق 380', 'product_cat' );
		return $term ? (int) $term->term_id : 0;
	}

	public static function is_course( $product_id ) {
		$academy_id = self::category_id();
		if ( ! $academy_id || ! $product_id ) {
			return false;
		}
		$ids = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $ids ) ) {
			return false;
		}
		foreach ( $ids as $id ) {
			if ( $id === $academy_id || in_array( $academy_id, get_ancestors( $id, 'product_cat', 'taxonomy' ), true ) ) {
				return true;
			}
		}
		return false;
	}

	public static function enqueue_assets() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$page_id = (int) get_option( self::ACADEMY_PAGE_OPTION );
		if ( ( $page_id && is_page( $page_id ) ) || ( function_exists( 'is_product' ) && is_product() && self::is_course( get_queried_object_id() ) ) ) {
			wp_enqueue_style( 'b380-academy', plugins_url( 'assets/academy.css', __FILE__ ), array(), self::VERSION );
		}
	}

	public static function course_template( $template ) {
		if ( class_exists( 'WooCommerce' ) && function_exists( 'is_product' ) && is_product() && self::is_course( get_queried_object_id() ) ) {
			$course_template = plugin_dir_path( __FILE__ ) . 'templates/single-course.php';
			if ( file_exists( $course_template ) ) {
				return $course_template;
			}
		}
		return $template;
	}

	public static function catalog() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '<p>فروشگاه دوره‌ها موقتاً در دسترس نیست.</p>';
		}
		$academy_id = self::category_id();
		if ( ! $academy_id ) {
			return '<p>دستهٔ آکادمی برق ۳۸۰ در ووکامرس پیدا نشد.</p>';
		}

		$search = isset( $_GET['course_q'] ) ? sanitize_text_field( wp_unslash( $_GET['course_q'] ) ) : '';
		$sort   = isset( $_GET['course_sort'] ) ? sanitize_key( wp_unslash( $_GET['course_sort'] ) ) : 'newest';
		$page   = isset( $_GET['course_page'] ) ? max( 1, absint( $_GET['course_page'] ) ) : 1;
		$sort   = in_array( $sort, array( 'newest', 'title', 'price_asc', 'price_desc' ), true ) ? $sort : 'newest';
		$chosen = isset( $_GET['course_category'] ) ? absint( $_GET['course_category'] ) : 0;
		$child_ids = get_term_children( $academy_id, 'product_cat' );
		$child_ids = is_wp_error( $child_ids ) ? array() : array_map( 'intval', $child_ids );
		$chosen = in_array( $chosen, $child_ids, true ) ? $chosen : 0;

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 9,
			'paged'          => $page,
			'tax_query'      => array( array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => array( $chosen ? $chosen : $academy_id ),
				'include_children' => true,
			) ),
		);
		if ( $search ) {
			$args['s'] = $search;
		}
		switch ( $sort ) {
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			case 'price_asc':
			case 'price_desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'price_asc' === $sort ? 'ASC' : 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}
		$courses = new WP_Query( $args );
		$categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => $academy_id,
			'hide_empty' => true,
		) );
		$categories = is_wp_error( $categories ) ? array() : $categories;

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'templates/catalog.php';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public static function card( $product ) {
		if ( ! $product || ! self::is_course( $product->get_id() ) ) {
			return;
		}
		$description = wp_trim_words( wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ), 20, '…' );
		?>
		<article class="b380-course-card">
			<a class="b380-course-card__media" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ) ); ?>
				<span>دوره آموزشی</span>
			</a>
			<div class="b380-course-card__body">
				<h3><a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
				<p><?php echo esc_html( $description ); ?></p>
				<div class="b380-course-card__bottom">
				<strong><?php echo wp_kses_post( $product->get_price_html() ?: 'رایگان' ); ?></strong>
				<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">مشاهده دوره <span aria-hidden="true">↗</span></a>
				</div>
			</div>
		</article>
		<?php
	}

	public static function course_fields() {
		global $product_object;
		if ( ! $product_object || ! self::is_course( $product_object->get_id() ) ) {
			return;
		}
		echo '<div class="options_group">';
		foreach ( array(
			'_b380_course_duration' => 'مدت دوره',
			'_b380_course_level' => 'سطح دوره',
			'_b380_course_instructor' => 'مدرس',
		) as $id => $label ) {
			woocommerce_wp_text_input( array( 'id' => $id, 'label' => $label ) );
		}
		woocommerce_wp_textarea_input( array( 'id' => '_b380_course_syllabus', 'label' => 'سرفصل‌ها (هر سطر یک مورد)' ) );
		echo '</div>';
	}

	public static function save_course_fields( $product ) {
		if ( ! self::is_course( $product->get_id() ) ) {
			return;
		}
		foreach ( array( '_b380_course_duration', '_b380_course_level', '_b380_course_instructor', '_b380_course_syllabus' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = wp_unslash( $_POST[ $key ] );
				$product->update_meta_data( $key, '_b380_course_syllabus' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ) );
			}
		}
	}
}

add_action( 'plugins_loaded', array( 'B380_Academy_Store', 'boot' ) );
register_activation_hook( __FILE__, array( 'B380_Academy_Store', 'activate' ) );
