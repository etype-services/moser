(function ($) {
  Drupal.behaviors.sanMarcosRecord = {
    attach: function (context) {
      let barkblock = $("#block-block-69");
      setTimeout(function () {
        barkblock.css('display', 'flex');
      }, 10000);
      $("#block-block-69 form").submit(function() {
        barkblock.css('display', 'none');
      });
    }
  };
})(jQuery);
