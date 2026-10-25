<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="b380-academy" dir="rtl">
	<div class="b380-academy__shell">
		<nav class="b380-academy__nav" aria-label="ناوبری آکادمی">
			<a class="b380-academy__brand" href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>"><span class="b380-academy__brand-mark">B</span><span>آکادمی <b>برق ۳۸۰</b></span></a>
			<div class="b380-academy__nav-links">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">سایت برق ۳۸۰</a>
				<a href="#b380-courses">دوره‌ها</a>
				<a class="b380-academy__cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">سبد خرید</a>
			</div>
		</nav>

		<section class="b380-academy__hero" aria-labelledby="b380-academy-title">
			<div class="b380-academy__hero-copy">
				<span class="b380-academy__eyebrow">BARGH380 / ACADEMY</span>
				<h1 id="b380-academy-title">یادگیری مهارت‌های <em>فردای صنعت</em> از همین‌جا شروع می‌شود.</h1>
				<p>دوره‌های تخصصی برق، PLC، اتوماسیون صنعتی و هوش مصنوعی را پیدا کنید و پیش از خرید، جزئیات هر دوره را دقیق ببینید.</p>
				<div class="b380-academy__hero-actions">
					<a class="b380-academy__button b380-academy__button--accent" href="#b380-courses">مشاهده دوره‌ها <span aria-hidden="true">↙</span></a>
					<a class="b380-academy__button b380-academy__button--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>">آشنایی با برق ۳۸۰</a>
				</div>
			</div>
			<div class="b380-academy__hero-art" aria-hidden="true">
				<div class="b380-academy__orbit b380-academy__orbit--one"></div>
				<div class="b380-academy__orbit b380-academy__orbit--two"></div>
				<div class="b380-academy__hero-core"><span>380</span><small>ACADEMY</small></div>
				<span class="b380-academy__spark b380-academy__spark--one"></span>
				<span class="b380-academy__spark b380-academy__spark--two"></span>
			</div>
		</section>

		<section class="b380-academy__catalog" id="b380-courses" aria-labelledby="b380-courses-title">
			<div class="b380-academy__section-head">
				<div><span class="b380-academy__kicker">مسیر یادگیری شما</span><h2 id="b380-courses-title">دوره‌های آموزشی</h2><p>دوره مناسب خود را با جست‌وجو و فیلتر پیدا کنید.</p></div>
				<span class="b380-academy__count"><?php echo esc_html( number_format_i18n( $courses->found_posts ) ); ?> دوره</span>
			</div>
			<form class="b380-academy__filters" action="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>" method="get" role="search">
				<label class="b380-academy__search"><span class="screen-reader-text">جست‌وجوی دوره</span><span aria-hidden="true">⌕</span><input type="search" name="course_q" placeholder="نام دوره یا مهارت موردنظرتان…" value="<?php echo esc_attr( $search ); ?>"></label>
				<label><span class="screen-reader-text">دسته‌بندی دوره</span><select name="course_category"><option value="0">همه موضوع‌ها</option><?php foreach ( $categories as $category ) : ?><option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $chosen, (int) $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option><?php endforeach; ?></select></label>
				<label><span class="screen-reader-text">مرتب‌سازی دوره‌ها</span><select name="course_sort"><option value="newest" <?php selected( $sort, 'newest' ); ?>>جدیدترین</option><option value="title" <?php selected( $sort, 'title' ); ?>>نام دوره</option><option value="price_asc" <?php selected( $sort, 'price_asc' ); ?>>قیمت: کم به زیاد</option><option value="price_desc" <?php selected( $sort, 'price_desc' ); ?>>قیمت: زیاد به کم</option></select></label>
				<button class="b380-academy__button b380-academy__button--dark" type="submit">نمایش دوره‌ها</button>
			</form>

			<?php if ( $courses->have_posts() ) : ?>
				<div class="b380-academy__grid<?php echo 1 === (int) $courses->post_count ? ' b380-academy__grid--single' : ''; ?>">
					<?php while ( $courses->have_posts() ) : $courses->the_post(); B380_Academy_Store::card( wc_get_product( get_the_ID() ) ); endwhile; ?>
				</div>
				<?php if ( $courses->max_num_pages > 1 ) : ?>
					<nav class="b380-academy__pagination" aria-label="صفحه‌بندی دوره‌ها">
						<?php
						echo wp_kses_post( paginate_links( array(
							'base'      => add_query_arg( array( 'course_q' => $search, 'course_category' => $chosen, 'course_sort' => $sort, 'course_page' => '%#%' ), B380_Academy_Store::academy_url() ),
							'format'    => '',
							'current'   => $page,
							'total'     => $courses->max_num_pages,
							'prev_text' => 'قبلی',
							'next_text' => 'بعدی',
						) ) );
						?>
					</nav>
				<?php endif; ?>
			<?php else : ?>
				<div class="b380-academy__empty"><span aria-hidden="true">⌕</span><h3>دوره‌ای با این مشخصات پیدا نشد</h3><p>عبارت جست‌وجو یا موضوع را تغییر دهید و دوباره تلاش کنید.</p><a href="<?php echo esc_url( B380_Academy_Store::academy_url() ); ?>">نمایش همه دوره‌ها</a></div>
			<?php endif; ?>
		</section>
	</div>
</div>
