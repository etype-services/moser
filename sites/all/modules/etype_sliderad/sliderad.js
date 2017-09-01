/**
 * @file
 * Slider Ad JS helper.
 */

/**
 * Function overides Simple Ads js
 */
(function ($) {
    Drupal.behaviors.sliderad_admin = {
        attach: function(context) {
            $('.node-sliderad-form #edit-field-ad-end-date').show();
        }
    };
}(jQuery));

(function ($) {
    Drupal.behaviors.sliderad = {
        attach: function(context) {

            $('#sliderad').mouseenter(function() {
                $('#adtwo').slideDown();
            });
            $('#sliderad').mouseleave(function() {
                $('#adtwo').delay(500).slideUp();
            });

        }
    };
}(jQuery));
