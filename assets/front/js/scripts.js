(function ($, window, document, pluginObject) {

    function wooOpenCloseUpdateTime($element) {

        let timerUniqueID = $element.data('unique-id'),
            timerArea = $element.parent().find("#wooopenclose-countdown-timer-" + timerUniqueID),
            spanDistance = timerArea.find('span.distance'),
            distance = parseInt(spanDistance.data('distance')),
            spanHours = timerArea.find('span.hours > span.count-number'),
            spanMinutes = timerArea.find('span.minutes > span.count-number'),
            spanSeconds = timerArea.find('span.seconds > span.count-number'),
            days = 0, hours = 0, minutes = 0, seconds = 0;

        if (distance > 0) {
            days = Math.floor(distance / (60 * 60 * 24));
            hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60) + days * 24);
            minutes = Math.floor((distance % (60 * 60)) / (60));
            seconds = Math.floor((distance % (60)));
        }

        spanHours.html(hours);
        spanMinutes.html(minutes);
        spanSeconds.html(seconds);
        spanDistance.data('distance', distance - 1);

        setTimeout(wooOpenCloseUpdateTime, 1000, $element);
    }

    $(document).on('ready updated_cart_totals', function () {
        $('.wooopenclose-countdown-timer').each(function () {
            wooOpenCloseUpdateTime($(this));
        });
    });


    $(document).on('click', '.wooopenclose-layout-1 .wooopenclose-schedules .wooopenclose-schedule .wooopenclose-day-name, .wooopenclose-layout-5 .wooopenclose-schedules .wooopenclose-schedule .wooopenclose-day-name', function () {

        let is_self = !!$(this).parent().hasClass('opened');

        $(this).parent().parent().find('.wooopenclose-schedule').removeClass('opened').find('.wooopenclose-day-schedules').slideUp('slow');

        if (is_self) {
            return;
        }

        $(this).parent().addClass('opened').find('.wooopenclose-day-schedules').slideDown('slow');
    });


    $(document).on('click', '.shop-status-bar .shop-status-bar-inline.close-bar', function () {
        $(this).parent().slideUp('slow');
    });


    $(document).on('click', '.wooopenclose-add-to-cart', function () {
        let disAllowMessage = $(this).data('disallowmessage'),
            wocPopupBox = $('#wooopenclose-box-container').find('.wooopenclose-box');
        if (typeof disAllowMessage !== "undefined" && disAllowMessage.length && disAllowMessage.length > 0) {
            wocPopupBox.html(disAllowMessage);
        }
    });


    // jBox popup
    $(document).on('click', '.wooopenclose-add-to-cart', function () {
        let wocPopupBox = $('#wooopenclose-box-container'),
            effect = $(this).data('effect'),
            popUp = new jBox('Modal');

        popUp.setContent(wocPopupBox).animate(effect);
        popUp.open();

        return false;
    });


    //Set cookie
    $(document).on('click', '.close-bar-permanently', function () {
        const currentTime = new Date();
        currentTime.setTime(currentTime.getTime() + (24 * 60 * 60 * 1000));
        Cookies.set('wooopenclose-ignore-status-bar', 1, {expires: currentTime});
    });


    // Hide status bar
    $(document).ready(function () {

        let shop_status_bar = $('.shop-status-bar'),
            getCookie = Cookies.get('wooopenclose-ignore-status-bar');

        if (getCookie === 'null' || getCookie === null || typeof getCookie === 'undefined' || getCookie !== '1') {
            shop_status_bar.css('visibility', 'visible');
        }

        if (getCookie === '1') {
            shop_status_bar.css('visibility', 'hidden');
        }
    });


})(jQuery, window, document, wooopenclose);