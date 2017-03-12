/**
 * @file
 * Slider Ad JS helper.
 */

(function ($) {
    Drupal.behaviors.simpleads_campaigns = {
        attach: function(context) {
            $('#edit-field-ad-end-date').show();
        }
    };
}(jQuery));
