/**
 * Admin Scripts
 */

(function ($, window, document, pluginObject) {

    "use strict";

    $(document).on('click', '.wooopenclose-shortcode', function () {

        let inputField = document.createElement('input'),
            htmlElement = $(this),
            ariaLabel = htmlElement.attr('aria-label');

        document.body.appendChild(inputField);
        inputField.value = htmlElement.html();
        inputField.select();
        document.execCommand('copy', false);
        inputField.remove();

        htmlElement.attr('aria-label', pluginObject.copyText);

        setTimeout(function () {
            htmlElement.attr('aria-label', ariaLabel);
        }, 5000);
    });


    $(document).on('change', '.woc_section .woc_switch_checkbox', function () {

        let checkBox = $(this),
            post_id = checkBox.data('id'),
            woc_nonce = checkBox.data('woc-nonce'),
            woc_active = !!checkBox.is(":checked");

        jQuery.ajax({
            type: 'POST',
            url: pluginObject.ajaxurl,
            context: this,
            data: {
                "action": "woc_switch_active",
                "post_id": post_id,
                "woc_active": woc_active,
                "woc_nonce": woc_nonce,
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });

    $(document).on('click', '.woc_section .woc_update_timezone', function () {

        $('.woc_update_timezone_overlay').fadeIn();

        $('#update_timezone').chosen({
            max_shown_results: 10,
            no_results_text: 'Oops, nothing found!',
            include_group_label_in_selected: true,
        });
    });


    $(document).on('click', '.timezone_popup_box .close', function (e) {

        e.preventDefault();
        $('.woc_update_timezone_overlay').fadeOut();
    });

    $(document).on('click', '.timezone_update', function (e) {

        e.preventDefault();
        let timeZone = $('#update_timezone').val(),
            woc_timezone_nonce = $('.woc_timezone_nonce').data('woc-timezone-nonce')

        jQuery.ajax({
            type: 'POST',
            url: pluginObject.ajaxurl,
            context: this,
            data: {
                "action": "woc_update_timezone",
                "time_zone": timeZone,
                "woc_timezone_nonce": woc_timezone_nonce,
            },
            success: function (response) {
                if (response.success) {
                    $('.woc_update_timezone_overlay').fadeOut();
                    location.reload();
                }
            }
        });
    });

})(jQuery, window, document, wooopenclose);