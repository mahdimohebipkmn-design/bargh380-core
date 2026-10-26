<?php
defined( 'ABSPATH' ) || exit;

/** An Elementor canvas element backed by the current WooCommerce course. */
final class B380_Academy_Elementor_Widget extends \Elementor\Widget_Base {
	public function get_name() { return 'b380_academy_course'; }
	public function get_title() { return 'برگه دوره آکادمی برق ۳۸۰'; }
	public function get_icon() { return 'eicon-product-info'; }
	public function get_categories() { return array( 'general' ); }
	public function get_keywords() { return array( 'academy', 'course', 'آکادمی', 'دوره' ); }

	protected function register_controls() {
		$this->start_controls_section( 'b380_course_settings', array( 'label' => 'تنظیمات دوره' ) );
		$this->add_control( 'product_id', array(
			'label' => 'شناسه محصول (اختیاری)',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'description' => 'در قالب تکی محصول خالی بگذارید تا اطلاعات همان محصول نمایش داده شود. متن‌ها، رنگ‌ها، تصاویر و فصل‌ها از تنظیمات محصول خوانده می‌شوند.',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo B380_Academy_Store::course_shortcode( array( 'product_id' => absint( $settings['product_id'] ?? 0 ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode returns escaped template markup.
	}
}
