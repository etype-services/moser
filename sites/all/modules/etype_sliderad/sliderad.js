/**
 * @file
 * Slider Ad JS helper.
 */

/**
 * Function overides Simple Ads js
 */
(function ($) {
    Drupal.behaviors.sliderad = {
        attach: function(context) {
            $('.node-sliderad-form #edit-field-ad-end-date').show();
        }
    };
}(jQuery));
