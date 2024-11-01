<?php
/**
 * Store status bar
 */

use WPDK\Utils;

defined( 'ABSPATH' ) || exit;

$button_classes = '';

if ( Utils::get_option( 'woc_bar_where' ) === 'wooopenclose-bar-none' ) {
	return;
}

if ( true === Utils::get_option( 'woc_bar_hide_permanently', false ) ) {
	$button_classes = 'close-bar-permanently';
}

?>

<div class="<?php echo esc_attr( woc_get_status_bar_classes( 'shop-status-bar' ) ); ?>">

    <div class="shop-status-bar-inline status-message">
		<?php printf( '<span>%s</span>', wooopenclose()->get_message() ); ?>
    </div>

	<?php if ( wooopenclose()->is_display_bar_btn() ) : ?>

        <div class="shop-status-bar-inline close-bar <?php echo esc_attr( $button_classes ) ?>">
			<?php printf( '<span>%s</span>', wooopenclose()->get_bar_btn_text() ); ?>
        </div>

	<?php endif;; ?>

</div>
