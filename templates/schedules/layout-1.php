<?php
/**
 * Schedules' Layout - 1
 */

defined( 'ABSPATH' ) || exit;

$style            = $style ?? 1;
$schedule_id      = $id ?? wooopenclose()->get_active_schedule_id();
$status_image     = wooopenclose()->get_status_image();
$status_image_alt = wooopenclose()->is_open() ? esc_html__( 'Shop open', 'woc-open-close' ) : esc_html__( 'Shop close', 'woc-open-close' );

//echo "<pre>"; print_r(  wooopenclose()->get_all_schedules( $schedule_id ) ); echo "</pre>";

?>

<div <?php wooopenclose_schedules_wrapper_classes( $style ); ?>>

    <div class="wooopenclose-schedules">

		<?php if ( ! empty( $status_image ) ) : ?>
            <div class="wooopenclose-status-img">
                <img src="<?php echo esc_url( $status_image ); ?>" alt="<?php echo esc_attr( $status_image_alt ); ?>">
            </div>
		<?php endif; ?>

		<?php foreach ( wooopenclose()->get_all_schedules( $schedule_id ) as $day_id => $day_schedules ) : ?>

            <div <?php wooopenclose_schedule_classes( $day_id ); ?>>

				<?php wooopenclose_day_name( $day_id ); ?>

				<?php wooopenclose_day_schedules( $day_schedules ); ?>
            </div>

		<?php endforeach; ?>

    </div>

</div>