<?php
/**
 * Schedules' Layout - 10
 */

defined( 'ABSPATH' ) || exit;

$style       = isset( $style ) ? $style : 1;
$schedule_id = isset( $id ) ? $id : wooopenclose()->get_active_schedule_id();

?>

<div <?php wooopenclose_schedules_wrapper_classes( $style ); ?>>

	<?php if ( isset( $title ) ) : ?>
        <h2 class="wooopenclose-schedules-title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

    <div class="wooopenclose-schedules">

		<?php foreach ( wooopenclose()->get_all_schedules( $schedule_id ) as $day_id => $day_schedules ) : ?>

            <div <?php wooopenclose_schedule_classes( $day_id ); ?>>

				<?php wooopenclose_day_name( $day_id ); ?>

				<?php wooopenclose_day_schedules( $day_schedules ); ?>
            </div>

		<?php endforeach; ?>

    </div>

	<?php if ( isset( $contact_link ) || ! empty( $contact_link ) || ! empty( $contact_note ) ) : ?>
        <p class="wooopenclose-schedules-contact-note"><?php echo esc_html( $contact_note ); ?></p>
        <a href="<?php echo esc_url( $contact_link ); ?>" class="wooopenclose-schedules-contact-btn"><?php echo esc_html__( 'Contact us', 'woc-open-close' ); ?></a>
	<?php endif; ?>
</div>