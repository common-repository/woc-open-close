<?php
/**
 * Metadata box: Schedules
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

$woc_hours_meta = WPDK\Utils::get_meta( 'woc_hours_meta' );
$woc_hours_meta = ! is_array( $woc_hours_meta ) ? array() : $woc_hours_meta;
$woc_message    = WPDK\Utils::get_args_option( 'woc_message', $woc_hours_meta );

?>

<div class='woc_section'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Label', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_html__( 'Define a title for this working hour', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs'>
        <input type="text" name="post_title" value="<?php the_title(); ?>" placeholder="<?php echo esc_attr__( 'Business hour', 'woc-open-close' ); ?>"/>
    </div>
</div>

<div class='woc_section'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Schedules', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Update working schedules data here', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs woc_days'>

		<?php foreach ( wooopenclose()->get_days() as $day_id => $day ) : ?>

            <div class="woc_day <?php echo esc_attr( $day_id ); ?>" id='<?php echo esc_attr( $day_id ); ?>'>

				<?php printf( '<div class="woc_day_header">%s</div>', Utils::get_args_option( 'label', $day ) ); ?>

                <div class="woc_day_content">
                    <div class='woc_repeats'>
						<?php
						foreach ( (array) Utils::get_args_option( $day_id, $woc_hours_meta, array() ) as $unique_id => $schedule ) {
							wooopenclose()->generate_woc_schedule( array_merge( array( 'day_id' => $day_id, 'unique_id' => $unique_id ), $schedule ) );
						}
						?>
                    </div>
                    <div class="button woc_add_schedule" data-day-id="<?php echo esc_attr( $day_id ); ?>"><?php echo esc_html__( 'Add Sub-Schedule', 'woc-open-close' ); ?></div>
                </div>
            </div>

		<?php endforeach; ?>

    </div>
</div>

<div class='woc_section'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Message', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Write your custom message what your visitors will see', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs'>
        <textarea name="woc_hours_meta[woc_message]" rows="5" placeholder="<?php echo esc_attr__( 'Offline ! We will start taking orders in %countdown%', 'woc-open-close' ); ?>"><?php echo wp_kses_data( $woc_message ); ?></textarea>
    </div>
</div>

<div id="woc_update_timezone_popup" class="woc_update_timezone_overlay">
    <div class="timezone_popup_box">
        <span class="close"><?php echo esc_html__( 'X', 'woc-open-close' ) ?></span>
        <p><?php echo esc_html__( 'Choose either a city in the same timezone as you or a UTC (Coordinated Universal Time) time offset.', 'woc-open-close' ); ?></p>
        <form method="post" id="update_timezone">
            <select name="update_timezone" id="update_timezone">
				<?php echo wp_timezone_choice( wooopenclose()->get_timezone_string() ) ?>
            </select>
            <button type="submit" class="timezone_update"><?php echo esc_html__( 'Update', 'woc-open-close' ) ?></button>
        </form>
    </div>
</div>



