<?php
/**
 * Schedules' Layout - 9
 */

defined( 'ABSPATH' ) || exit;

$style       = isset( $style ) ? $style : 1;
$schedule_id = isset( $id ) ? $id : wooopenclose()->get_active_schedule_id();

?>
<div<?php wooopenclose_schedules_wrapper_classes( $style ); ?>>

    <div class="pb-row">
        <div class="pb-col-md-6">
            <div class="wooopenclose-image-wrap">
                <img src="<?php echo WOOOPENCLOSE_PLUGIN_URL . 'assets/images/layouts/layout-9-style-1.jpg'; ?>" alt="<?php echo esc_attr__( 'Layout 9 Image', 'woc-open-close' ); ?>">
            </div>
        </div>
        <div class="pb-col-md-6">

			<?php if ( isset( $title ) ) : ?>
				<?php printf( '<h2 class="wooopenclose-schedules-title">%s</h2>', $title ); ?>
			<?php endif; ?>

            <div class="wooopenclose-schedules">

				<?php foreach ( wooopenclose()->get_all_schedules( $schedule_id ) as $day_id => $day_schedules ) : ?>

                    <div <?php wooopenclose_schedule_classes( $day_id ); ?>>

						<?php wooopenclose_day_name( $day_id ); ?>

						<?php wooopenclose_day_schedules( $day_schedules ); ?>
                    </div>

				<?php endforeach; ?>
            </div>


			<?php if ( isset( $note ) ) : ?>
				<?php printf( '<p class="wooopenclose-schedules-note">%s</p>', $note ); ?>
			<?php endif; ?>
        </div>
    </div>
</div>