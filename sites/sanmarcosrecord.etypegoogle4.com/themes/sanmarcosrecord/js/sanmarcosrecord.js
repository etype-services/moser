(function ($) {
  Drupal.behaviors.sanMarcosRecord = {
    attach: function (context) {
      setTimeout(function () {
        $("#block-block-69").css('display', 'flex');
      }, 10000);
    }
  };
})(jQuery);
