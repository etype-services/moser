(function ($) {
    Drupal.behaviors.slideshow = {
        attach: function (context) {
            $(".views_slideshow_cycle_slide").css("postion", "relative");
        }
    };
})(jQuery);