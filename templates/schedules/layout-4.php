<?php
/**
 * Schedules' Layout - 4
 */


defined( 'ABSPATH' ) || exit;

$style       = isset( $style ) ? $style : 1;
$schedule_id = isset( $id ) ? $id : wooopenclose()->get_active_schedule_id();
?>
    <div <?php wooopenclose_schedules_wrapper_classes( $style ); ?>>

		<?php if ( isset( $title ) ) : ?>
            <h2 class="wooopenclose-schedules-title">
				<?php echo esc_html( $title ); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="228" height="62" viewBox="0 0 228 62">
                    <g transform="translate(-835 -309)">
                        <rect width="52" height="52" rx="26" transform="translate(923 309)" fill="#fff"/>
                        <rect width="52" height="52" rx="26" transform="translate(964 315)" fill="#fff"/>
                        <rect width="52" height="52" rx="26" transform="translate(1011 319)" fill="#fff"/>
                        <rect width="52" height="52" rx="26" transform="translate(882 315)" fill="#fff"/>
                        <rect width="52" height="52" rx="26" transform="translate(835 319)" fill="#fff"/>
                    </g>
                </svg>
            </h2>
		<?php endif; ?>

        <div class="wooopenclose-schedules">

			<?php foreach ( wooopenclose()->get_all_schedules( $schedule_id ) as $day_id => $day_schedules ) : ?>

                <div <?php wooopenclose_schedule_classes( $day_id ); ?>>

					<?php wooopenclose_day_name( $day_id ); ?>

					<?php wooopenclose_day_schedules( $day_schedules ); ?>
                </div>

			<?php endforeach; ?>

        </div>
    </div>

<?php
if ( isset( $note ) ) {
	printf( '<p class="wooopenclose-schedules-note">%s</p>', $note );
}