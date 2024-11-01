<?php
/**
 * Countdown timer
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

$unique_id     = uniqid();
$style         = ( isset( $style ) ) ? $style : 1;
$dynamic_class = wooopenclose()->is_open() ? 'wooopenclose-shop-open' : '';
$next_time     = wooopenclose()->get_next_time( '', '', true );
$time_diff     = $next_time - wp_date( 'U' );

?>

<div id="wooopenclose-countdown-timer-<?php echo esc_attr( $unique_id ); ?>"
     data-unique-id="<?php echo esc_attr( $unique_id ); ?>"
     class="wooopenclose-countdown-timer wooopenclose-countdown-timer-<?php echo esc_attr( $style ); ?> <?php echo esc_attr( $dynamic_class ); ?>">
    <span style="display: none;" class="distance" data-distance="<?php echo esc_attr( $time_diff ); ?>"></span>
    <span class="hours"><span class="count-number">0</span><?php printf( '<span class="count-text">%s</span>', Utils::get_option( 'woc_timer_text_hours', esc_html__( 'Hours', 'woc-open-close' ) ) ); ?></span>
    <span class="minutes"><span class="count-number">0</span><?php printf( '<span class="count-text">%s</span>', Utils::get_option( 'woc_timer_text_minutes', esc_html__( 'Minutes', 'woc-open-close' ) ) ); ?></span>
    <span class="seconds"><span class="count-number">0</span><?php printf( '<span class="count-text">%s</span>', Utils::get_option( 'woc_timer_text_seconds', esc_html__( 'Seconds', 'woc-open-close' ) ) ); ?></span>
</div>