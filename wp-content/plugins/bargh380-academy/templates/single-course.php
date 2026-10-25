<?php
defined( 'ABSPATH' ) || exit;

global $product;
$product = wc_get_product( get_queried_object_id() );
if ( ! $product ) {
	return;
}
$duration   = $product->get_meta( '_b380_course_duration' );
$level      = $product->get_meta( '_b380_course_level' );
$instructor = $product->get_meta( '_b380_course_instructor' );
$description = $product->get_description() ?: $product->get_short_description();
$syllabus   = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $product->get_meta( '_b380_course_syllabus' ) ) ) );
$related    = new WP_Query( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'post__not_in'   => array( $product->get_id() ),
	'tax_query'      => array( array(
		'taxonomy'         => 'product_cat',
		'field'            => 'term_id',
		'terms'            => array( B380_Academy_Store::category_id() ),
		'include_children' => true,
	) ),
) );

get_header();
?>
<main class="b380-academy b380-course-page" id="content" dir="rtl">
	<div class="b380-academy__shell">
		<nav class="b380-academy__nav" aria-label="ناوبری آکادمی">
			<a class="b380-academy__brand" href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>"><span class="b380-academy__brand-mark">B</span><span>آکادمی <b>برق ۳۸۰</b></span></a>
			<div class="b380-academy__nav-links"><a href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>">همه دوره‌ها</a><a class="b380-academy__cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">سبد خرید</a></div>
		</nav>
		<?php wc_print_notices(); ?>
		<nav class="b380-course-page__crumbs" aria-label="مسیر صفحه"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">خانه</a><span aria-hidden="true">/</span><a href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>">آکادمی</a><span aria-hidden="true">/</span><span><?php echo esc_html( $product->get_name() ); ?></span></nav>
		<section class="b380-course-page__hero" aria-labelledby="b380-course-title">
			<div class="b380-course-page__cover">
				<?php echo wp_kses_post( $product->get_image( 'large', array( 'loading' => 'eager' ) ) ); ?>
				<span class="b380-course-page__cover-tag">دوره آموزشی</span>
			</div>
			<div class="b380-course-page__intro">
				<span class="b380-academy__kicker">BARGH380 ACADEMY</span>
				<h1 id="b380-course-title"><?php echo esc_html( $product->get_name() ); ?></h1>
				<?php if ( $product->get_short_description() ) : ?><div class="b380-course-page__summary"><?php echo wp_kses_post( $product->get_short_description() ); ?></div><?php endif; ?>
				<?php if ( $duration || $level || $instructor ) : ?>
					<dl class="b380-course-page__facts">
						<?php if ( $duration ) : ?><div><dt>مدت دوره</dt><dd><?php echo esc_html( $duration ); ?></dd></div><?php endif; ?>
						<?php if ( $level ) : ?><div><dt>سطح</dt><dd><?php echo esc_html( $level ); ?></dd></div><?php endif; ?>
						<?php if ( $instructor ) : ?><div><dt>مدرس</dt><dd><?php echo esc_html( $instructor ); ?></dd></div><?php endif; ?>
					</dl>
				<?php endif; ?>
				<a class="b380-course-page__intro-link" href="#b380-course-details">آشنایی بیشتر با دوره <span aria-hidden="true">↙</span></a>
			</div>
			<aside class="b380-course-page__purchase" aria-label="ثبت‌نام دوره">
				<span class="b380-course-page__purchase-label">ثبت‌نام در این دوره</span>
				<div class="b380-course-page__price"><?php echo wp_kses_post( $product->get_price_html() ?: 'رایگان' ); ?></div>
				<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
					<?php woocommerce_template_single_add_to_cart(); ?>
				<?php else : ?>
					<p class="b380-course-page__unavailable">ثبت‌نام این دوره اکنون فعال نیست.</p>
				<?php endif; ?>
				<a class="b380-course-page__cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">مشاهده سبد خرید</a>
				<p class="b380-course-page__purchase-note">جزئیات دوره را پیش از تکمیل سفارش مطالعه کنید.</p>
			</aside>
		</section>

		<div class="b380-course-page__below" id="b380-course-details">
			<div class="b380-course-page__main">
				<?php if ( $description ) : ?><section class="b380-course-page__panel" aria-labelledby="b380-about-title"><span class="b380-academy__kicker">درباره آموزش</span><h2 id="b380-about-title">معرفی دوره</h2><div class="b380-course-page__description"><?php echo apply_filters( 'the_content', $description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Product descriptions use WordPress content filters. ?></div></section><?php endif; ?>
				<?php if ( $syllabus ) : ?><section class="b380-course-page__panel" aria-labelledby="b380-syllabus-title"><span class="b380-academy__kicker">آنچه یاد می‌گیرید</span><h2 id="b380-syllabus-title">سرفصل‌ها</h2><ol class="b380-course-page__syllabus"><?php foreach ( $syllabus as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ol></section><?php endif; ?>
			</div>
			<aside class="b380-course-page__help"><span class="b380-academy__kicker">پیش از خرید</span><h2>پرسشی درباره دوره دارید؟</h2><p>اگر برای انتخاب دوره به راهنمایی نیاز دارید، با پشتیبانی برق ۳۸۰ در تماس باشید.</p><a href="tel:09922335706">تماس با پشتیبانی <span aria-hidden="true">↗</span></a></aside>
		</div>

		<?php if ( $related->have_posts() ) : ?>
			<section class="b380-course-page__related" aria-labelledby="b380-related-title"><div class="b380-academy__section-head"><div><span class="b380-academy__kicker">مسیرهای دیگر</span><h2 id="b380-related-title">دوره‌های مرتبط</h2></div><a href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>">همه دوره‌ها</a></div><div class="b380-academy__grid"><?php while ( $related->have_posts() ) : $related->the_post(); B380_Academy_Store::card( wc_get_product( get_the_ID() ) ); endwhile; ?></div></section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
