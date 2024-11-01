<?php
/**
 * Metadata box: Publish
 */

defined( 'ABSPATH' ) || exit;

global $post;

if ( $post->post_status === 'publish' ) :
	?>
    <div class='woc_section woc_section_mini'>
        <div class='woc_section_inline woc_section_title'>
			<?php echo esc_html__( 'Default', 'woc-open-close' ); ?>
        </div>
        <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Make this hour schedule as default for your Shop', 'woc-open-close' ); ?>">?</div>
        <div class='woc_section_inline woc_section_inputs'>
            <label class="woc_switch">
                <input <?php checked( wooopenclose()->get_active_schedule_id(), $post->ID ); ?> type="checkbox" class="woc_switch_checkbox" data-id="<?php echo esc_attr( $post->ID ); ?>" data-woc-nonce="<?php echo wp_create_nonce( 'woc-verify' ) ?>">
                <span class="woc_switch_slider woc_switch_round"></span>
            </label>
        </div>
    </div>
<?php endif; ?>

<div class='woc_section woc_section_mini'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Date - Now', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Current Date for your WooCommerce Shop', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs'>
        <div class="woc_current_time"><?php echo esc_html__( wp_date( 'jS F Y' ) ); ?></div>
    </div>
</div>

<div class='woc_section woc_section_mini'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Time - Now', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Current time for your WooCommerce Shop', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs'>
        <div class="woc_current_time"><?php echo esc_html__( wp_date( 'h:i A' ) ); ?></div>
    </div>
</div>

<div class='woc_section woc_section_mini'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Day - Now', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Current Day for your WooCommerce Shop', 'woc-open-close' ); ?>">?</div>
    <div class='woc_section_inline woc_section_inputs'>
        <div class="woc_current_time"><?php echo esc_html__( wp_date( 'l' ) ); ?></div>
    </div>
</div>

<div class='woc_section woc_section_mini'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Timezone', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top" aria-label="<?php echo esc_attr__( 'Current timezone for your WooCommerce Shop', 'woc-open-close' ); ?>">?
    </div>
    <div class='woc_section_inline woc_section_inputs'>
        <div class="woc_current_time"><?php echo wp_kses_data( wooopenclose()->get_timezone_string() ); ?></div>
        <div class='woc_note hint--top-left hint--medium hint--error' aria-label='<?php echo esc_attr__( 'You must update your time zone or time according to your city where you want to manage Shop', 'woc-open-close' ); ?>'><?php echo esc_html__( 'Note', 'woc-open-close' ); ?></div>
        <p class="woc_update_timezone"><?php echo esc_html__( 'Update Time Now', 'woc-open-close' ); ?></p>
    </div>
</div>

<div class='woc_section woc_section_mini'>
    <div class='woc_section_inline woc_section_title'><?php echo esc_html__( 'Shortcode', 'woc-open-close' ); ?></div>
    <div class="woc_section_inline woc_section_hint hint--top"
         aria-label="<?php echo esc_attr__( 'Copy this shortcode to display the schedule anywhere you want!', 'woc-open-close' ); ?>">
        ?
    </div>
    <div class='woc_section_inline woc_section_inputs'>
        <span class='wooopenclose-shortcode hint--top' aria-label='<?php echo esc_attr__( 'Click to Copy', 'woc-open-close' ); ?>'><?php printf( '[schedule id="%s"]', $post->ID ); ?></span>
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
            <input type="hidden" class="woc_timezone_nonce" data-woc-timezone-nonce="<?php echo wp_create_nonce( 'woc-verify-timezone' ) ?>"></input>
            <button type="submit" class="timezone_update"><?php echo esc_html__( 'Update', 'woc-open-close' ) ?></button>
        </form>
    </div>
</div>


<style>
    #minor-publishing-actions, .misc-pub-post-status, .misc-pub-curtime, .misc-pub-visibility {
        display: none;
    !important;
    }

    #major-publishing-actions {
        border: none !important;
        background: #fff !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    #major-publishing-actions .clear {
        display: none;
    }

    input#publish {
        background: #1dbf73;
        border: none;
    }
</style>