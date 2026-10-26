<?php
defined( 'ABSPATH' ) || exit;
global $product;
$product = wc_get_product( get_queried_object_id() );
if ( ! $product ) {
	return;
}

$id = $product->get_id();
$get = static function ( $key, $fallback = '' ) use ( $id ) { return B380_Academy_Course_Meta::get( $id, $key, $fallback ); };
$plain_lines = static function ( $key ) use ( $get ) { return B380_Academy_Course_Meta::lines( $get( $key ) ); };
$icon_lines = static function ( $key ) use ( $get ) { return B380_Academy_Course_Meta::icon_lines( $get( $key ) ); };
$accent = sanitize_hex_color( $get( 'accent_color', '#14e884' ) ) ?: '#14e884';
$accent_secondary = sanitize_hex_color( $get( 'accent_secondary', '#315af2' ) ) ?: '#315af2';
$cta = $get( 'cta_label', 'ثبت‌نام و شروع یادگیری' );
$facts = $icon_lines( 'hero_facts' );
$benefits = $icon_lines( 'benefits' );
$learning = $icon_lines( 'learning_cards' );
if ( ! $learning ) {
	foreach ( B380_Academy_Course_Meta::lines( $product->get_meta( '_b380_course_syllabus' ) ) as $item ) {
		$learning[] = array( '✦', $item, '' );
	}
}
$audience = $plain_lines( 'audience_items' );
$not_for = $plain_lines( 'not_for_items' );
$instructor = $get( 'instructor_name', $product->get_meta( '_b380_course_instructor' ) );
$achievements = $plain_lines( 'instructor_achievements' );
$faq = B380_Academy_Course_Meta::lines( $get( 'faq_items' ), true );
$detail_icons = array_map( 'trim', explode( '|', $get( 'detail_icons', '✦ | ✓ | ◆' ) ) );
$support_url = $get( 'support_url', 'tel:09922335706' );
$support_label = $get( 'support_label', 'مشاوره خرید' );
$video = $get( 'hero_video' );
$background = $get( 'hero_background' );

get_header();
?>
<main class="b380-academy b380-course-landing" id="content" dir="rtl" style="--course-accent:<?php echo esc_attr( $accent ); ?>;--course-secondary:<?php echo esc_attr( $accent_secondary ); ?>;<?php echo $background ? '--course-background:url(&quot;' . esc_url( $background ) . '&quot;);' : ''; ?>">
	<nav class="b380-course-landing__topnav" aria-label="ناوبری آکادمی"><a class="b380-course-landing__topbrand" href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>">B <span>آکادمی برق ۳۸۰</span></a><a href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>"><?php echo esc_html( $get( 'all_courses_label', 'همهٔ دوره‌ها' ) ); ?></a><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo esc_html( $get( 'cart_label', 'سبد خرید' ) ); ?></a></nav>
	<div class="b380-course-landing__wrap">
		<?php wc_print_notices(); ?>
		<section class="b380-course-landing__hero" id="b380-course-intro" aria-labelledby="b380-course-title">
			<div class="b380-course-landing__hero-copy">
				<?php if ( $get( 'eyebrow' ) ) : ?><span class="b380-course-landing__eyebrow"><span aria-hidden="true"><?php echo esc_html( $get( 'hero_icon', '✦' ) ); ?></span><?php echo esc_html( $get( 'eyebrow' ) ); ?></span><?php endif; ?>
				<h1 id="b380-course-title"><?php echo esc_html( $get( 'hero_title', $product->get_name() ) ); ?></h1>
				<?php if ( $get( 'hero_subtitle' ) ) : ?><p class="b380-course-landing__subtitle"><?php echo nl2br( esc_html( $get( 'hero_subtitle' ) ) ); ?></p><?php elseif ( $product->get_short_description() ) : ?><div class="b380-course-landing__subtitle"><?php echo wp_kses_post( $product->get_short_description() ); ?></div><?php endif; ?>
				<div class="b380-course-landing__price"><span><?php echo esc_html( $get( 'price_label', 'هزینه ثبت‌نام در دوره' ) ); ?></span><strong><?php echo wp_kses_post( $product->get_price_html() ?: 'رایگان' ); ?></strong></div>
				<div class="b380-course-landing__enroll" id="b380-course-enroll"><?php if ( $product->is_purchasable() && $product->is_in_stock() ) : woocommerce_template_single_add_to_cart(); else : ?><p><?php echo esc_html( $get( 'unavailable_label', 'ثبت‌نام این دوره اکنون فعال نیست.' ) ); ?></p><?php endif; ?></div>
				<a class="b380-course-landing__scroll" href="#b380-course-overview"><?php echo esc_html( $get( 'scroll_label', 'معرفی دوره' ) ); ?> <span aria-hidden="true">↓</span></a>
			</div>
			<div class="b380-course-landing__hero-media">
				<?php if ( $video && preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $video ) ) : ?><video controls preload="metadata" playsinline aria-label="ویدئوی معرفی <?php echo esc_attr( $product->get_name() ); ?>"><source src="<?php echo esc_url( $video ); ?>"></video>
				<?php elseif ( $video && wp_oembed_get( $video ) ) : echo wp_oembed_get( $video ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted oEmbed provider output.
				else : echo wp_kses_post( $product->get_image( 'large', array( 'loading' => 'eager' ) ) ); endif; ?></div>
		</section>

		<?php if ( $facts ) : ?><section class="b380-course-landing__facts" aria-label="اطلاعات کوتاه دوره"><?php foreach ( $facts as $fact ) : ?><article class="b380-course-landing__glass"><span class="b380-course-landing__icon" aria-hidden="true"><?php echo esc_html( $fact[0] ); ?></span><h2><?php echo esc_html( $fact[1] ); ?></h2><?php if ( $fact[2] ) : ?><p><?php echo esc_html( $fact[2] ); ?></p><?php endif; ?></article><?php endforeach; ?></section><?php endif; ?>

		<?php if ( $benefits || $get( 'promise_title' ) ) : ?><section class="b380-course-landing__section" aria-labelledby="b380-promise-title"><div class="b380-course-landing__section-heading"><span><?php echo esc_html( $get( 'promise_kicker', 'چرا این دوره؟' ) ); ?></span><h2 id="b380-promise-title"><?php echo esc_html( $get( 'promise_title', 'یادگیری برای دنیای واقعی صنعت' ) ); ?></h2><?php if ( $get( 'promise_subtitle' ) ) : ?><p><?php echo esc_html( $get( 'promise_subtitle' ) ); ?></p><?php endif; ?></div><?php if ( $benefits ) : ?><div class="b380-course-landing__benefits"><?php foreach ( $benefits as $item ) : ?><article class="b380-course-landing__glass"><span class="b380-course-landing__icon" aria-hidden="true"><?php echo esc_html( $item[0] ); ?></span><h3><?php echo esc_html( $item[1] ); ?></h3><?php if ( $item[2] ) : ?><p><?php echo esc_html( $item[2] ); ?></p><?php endif; ?></article><?php endforeach; ?></div><?php endif; ?></section><?php endif; ?>

		<?php $overview_body = $get( 'overview_body' ); $overview_image = $get( 'overview_image' ); ?>
		<?php if ( $overview_body || $product->get_description() ) : ?><section class="b380-course-landing__overview b380-course-landing__glass" id="b380-course-overview" aria-labelledby="b380-overview-title"><div><span class="b380-course-landing__kicker"><?php echo esc_html( $get( 'overview_kicker', 'معرفی دوره' ) ); ?></span><h2 id="b380-overview-title"><?php echo esc_html( $get( 'overview_title', $product->get_name() ) ); ?></h2><div class="b380-course-landing__prose"><?php if ( $overview_body ) : echo wpautop( esc_html( $overview_body ) ); else : echo apply_filters( 'the_content', $product->get_description() ); endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Product description uses standard WP filters. ?></div></div><div class="b380-course-landing__overview-image"><?php if ( $overview_image ) : ?><img src="<?php echo esc_url( $overview_image ); ?>" alt="<?php echo esc_attr( $get( 'overview_title', $product->get_name() ) ); ?>" loading="lazy"><?php else : echo wp_kses_post( $product->get_image( 'large', array( 'loading' => 'lazy' ) ) ); endif; ?></div></section><?php endif; ?>

		<?php if ( $learning ) : ?><section class="b380-course-landing__section b380-course-landing__learning" id="b380-course-learning" aria-labelledby="b380-learning-title"><div class="b380-course-landing__section-heading"><span><?php echo esc_html( $get( 'learning_kicker', 'سرفصل‌ها' ) ); ?></span><h2 id="b380-learning-title"><?php echo esc_html( $get( 'learning_title', 'در این دوره چه می‌آموزید؟' ) ); ?></h2><?php if ( $get( 'learning_subtitle' ) ) : ?><p><?php echo esc_html( $get( 'learning_subtitle' ) ); ?></p><?php endif; ?></div><div class="b380-course-landing__learning-grid"><?php foreach ( $learning as $index => $item ) : ?><article class="b380-course-landing__glass"><span class="b380-course-landing__card-index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span><span class="b380-course-landing__icon" aria-hidden="true"><?php echo esc_html( $item[0] ); ?></span><h3><?php echo esc_html( $item[1] ); ?></h3><?php if ( $item[2] ) : ?><p><?php echo esc_html( $item[2] ); ?></p><?php endif; ?></article><?php endforeach; ?></div></section><?php endif; ?>

		<?php if ( $audience || $not_for ) : ?><section class="b380-course-landing__audience" aria-label="مخاطبان دوره">
			<?php if ( $audience ) : ?><div class="b380-course-landing__glass"><span class="b380-course-landing__kicker"><?php echo esc_html( $get( 'audience_kicker', 'برای شما' ) ); ?></span><h2><?php echo esc_html( $get( 'audience_title', 'این دوره برای چه کسانی است؟' ) ); ?></h2><ul><?php foreach ( $audience as $item ) : ?><li><span class="b380-course-landing__list-icon" aria-hidden="true"><?php echo esc_html( $get( 'audience_icon', '✓' ) ); ?></span><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
			<?php if ( $not_for ) : ?><div class="b380-course-landing__glass b380-course-landing__audience-no"><span class="b380-course-landing__kicker"><?php echo esc_html( $get( 'not_for_kicker', 'انتخاب آگاهانه' ) ); ?></span><h2><?php echo esc_html( $get( 'not_for_title', 'این دوره برای چه کسانی نیست؟' ) ); ?></h2><ul><?php foreach ( $not_for as $item ) : ?><li><span class="b380-course-landing__list-icon" aria-hidden="true"><?php echo esc_html( $get( 'not_for_icon', '−' ) ); ?></span><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
		</section><?php endif; ?>

		<?php if ( $get( 'why_body' ) || $get( 'prerequisite_body' ) || $get( 'difference_body' ) ) : ?><section class="b380-course-landing__explain" aria-label="جزئیات بیشتر دوره"><?php foreach ( array( 'why' => 'چرا این دوره؟', 'prerequisite' => 'پیش‌نیاز دوره', 'difference' => 'تمایز این دوره' ) as $icon_index => $key ) : if ( ! $get( $key . '_body' ) ) { continue; } ?><article class="b380-course-landing__glass"><span class="b380-course-landing__icon" aria-hidden="true"><?php echo esc_html( $detail_icons[ $icon_index ] ?? '✦' ); ?></span><h2><?php echo esc_html( $get( $key . '_title', array( 'why' => 'چرا این دوره؟', 'prerequisite' => 'پیش‌نیاز دوره', 'difference' => 'تمایز این دوره' )[ $key ] ) ); ?></h2><p><?php echo nl2br( esc_html( $get( $key . '_body' ) ) ); ?></p></article><?php endforeach; ?></section><?php endif; ?>

		<?php if ( $instructor ) : ?><section class="b380-course-landing__instructor b380-course-landing__glass" id="b380-course-instructor" aria-labelledby="b380-instructor-title"><div class="b380-course-landing__instructor-photo"><?php if ( $get( 'instructor_image' ) ) : ?><img src="<?php echo esc_url( $get( 'instructor_image' ) ); ?>" alt="<?php echo esc_attr( $instructor ); ?>" loading="lazy"><?php else : ?><span aria-hidden="true">👤</span><?php endif; ?></div><div><span class="b380-course-landing__kicker"><?php echo esc_html( $get( 'instructor_kicker', 'مدرس دوره' ) ); ?></span><h2 id="b380-instructor-title"><?php echo esc_html( $instructor ); ?></h2><?php if ( $get( 'instructor_role' ) ) : ?><p class="b380-course-landing__instructor-role"><?php echo esc_html( $get( 'instructor_role' ) ); ?></p><?php endif; ?><?php if ( $achievements ) : ?><ul><?php foreach ( $achievements as $item ) : ?><li><span class="b380-course-landing__list-icon" aria-hidden="true"><?php echo esc_html( $get( 'instructor_icon', '★' ) ); ?></span><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul><?php endif; ?></div></section><?php endif; ?>

		<?php if ( $faq ) : ?><section class="b380-course-landing__section b380-course-landing__faq" id="b380-course-faq" aria-labelledby="b380-faq-title"><div class="b380-course-landing__section-heading"><span><?php echo esc_html( $get( 'faq_kicker', 'پیش از ثبت‌نام' ) ); ?></span><h2 id="b380-faq-title"><?php echo esc_html( $get( 'faq_title', 'پرسش‌های متداول' ) ); ?></h2></div><div class="b380-course-landing__faq-list"><?php foreach ( $faq as $index => $item ) : ?><details class="b380-course-landing__glass"<?php echo 0 === $index ? ' open' : ''; ?>><summary><?php echo esc_html( $item[0] ); ?></summary><p><?php echo esc_html( $item[1] ); ?></p></details><?php endforeach; ?></div></section><?php endif; ?>

		<section class="b380-course-landing__closing b380-course-landing__glass" aria-labelledby="b380-closing-title"><span class="b380-course-landing__kicker"><?php echo esc_html( $get( 'closing_kicker', 'شروع مسیر یادگیری' ) ); ?></span><h2 id="b380-closing-title"><?php echo esc_html( $get( 'closing_title', 'برای مهارت بعدی خود آماده‌اید؟' ) ); ?></h2><?php if ( $get( 'closing_body' ) ) : ?><p><?php echo esc_html( $get( 'closing_body' ) ); ?></p><?php endif; ?><div><a class="b380-course-landing__button" href="#b380-course-enroll"><?php echo esc_html( $cta ); ?></a><a class="b380-course-landing__support" href="<?php echo esc_url( $support_url ); ?>"><span aria-hidden="true"><?php echo esc_html( $get( 'support_icon', '💬' ) ); ?></span><?php echo esc_html( $support_label ); ?></a></div></section>
	</div>
	<nav class="b380-course-landing__sticky" aria-label="دسترسی سریع دوره"><a href="#b380-course-overview"><?php echo esc_html( $get( 'nav_overview', 'معرفی' ) ); ?></a><a href="#b380-course-learning"><?php echo esc_html( $get( 'nav_learning', 'سرفصل‌ها' ) ); ?></a><a href="#b380-course-instructor"><?php echo esc_html( $get( 'nav_instructor', 'مدرس' ) ); ?></a><a href="#b380-course-faq"><?php echo esc_html( $get( 'nav_faq', 'سؤال‌ها' ) ); ?></a><a class="b380-course-landing__sticky-cta" href="#b380-course-enroll"><?php echo esc_html( $cta ); ?></a></nav>
</main>
<?php get_footer(); ?>
