<?php
/**
 * Widget: Schedule
 */

use \WPDK\Utils;

defined( 'ABSPATH' ) || exit;


class WOOOPENCLOSE_WIDGET_Schedule extends WP_Widget {

	/**
	 * WOOOPENCLOSE_WIDGET_Schedule constructor.
	 */
	function __construct() {
		parent::__construct(
			'woc_widget_schedules', esc_html__( 'Store Open Close - Schedules', 'woc-open-close' ),
			array( 'description' => esc_html__( 'Display your store business hours or schedule.', 'woc-open-close' ), )
		);
	}


	/**
	 * @param $args
	 * @param $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$title       = apply_filters( 'widget_title', Utils::get_args_option( 'title', $instance ) );
		$schedule_id = Utils::get_args_option( 'hour_set', $instance );

		ob_start();

		if ( ! empty( $title ) ) {
			printf( '%s%s%s', Utils::get_args_option( 'before_title', $args ), $title, Utils::get_args_option( 'after_title', $args ) );
		}

		echo wp_kses_data( do_shortcode( '[schedule set="' . $schedule_id . '"]' ) );

		printf( '%s%s%s', Utils::get_args_option( 'before_widget', $args ), ob_get_clean(), Utils::get_args_option( 'after_widget', $args ) );
	}


	/**
	 * @param $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		$title       = Utils::get_args_option( 'title', $instance, esc_html__( 'Our business schedules', 'woc-open-close' ) );
		$schedule_id = Utils::get_args_option( 'hour_set', $instance );

		?>
        <div class='woc_section woc_section_mini'>
            <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Widget Title', 'woc-open-close' ); ?></div>
            <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_html__( 'Set Title for this Widget', 'woc-open-close' ); ?>">?</div>
            <div class='woc_section_inline woc_section_inputs'>
                <input name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" placeholder="<?php esc_attr( 'Our Business Schedules' ) ?>" value="<?php echo esc_attr( $title ); ?>">
            </div>
        </div>

        <div class='woc_section woc_section_mini'>
            <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Schedule', 'woc-open-close' ); ?></div>
            <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_html__( 'Select which schedules you want to Display. Leave empty to display default Schedule', 'woc-open-close' ); ?>">?</div>
            <div class='woc_section_inline woc_section_inputs'>

                <select name="<?php echo esc_attr( $this->get_field_name( 'hour_set' ) ); ?>">

                    <option value=""><?php echo esc_html__( 'Select a Schedule', 'woc-open-close' ); ?></option>

					<?php foreach ( get_posts( 'post_type=woc_hour&posts_per_page=-1' ) as $post ) : ?>

                        <option <?php echo esc_attr( $post->ID == $schedule_id ? 'selected' : '' ); ?> value="<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></option>

					<?php endforeach; ?>

                </select>

            </div>
        </div>

		<?php
	}


	/**
	 * @param $new_instance
	 * @param $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		return array(
			'title'    => sanitize_text_field( Utils::get_args_option( 'title', $new_instance ) ),
			'hour_set' => sanitize_text_field( Utils::get_args_option( 'hour_set', $new_instance ) ),
		);
	}
}