
// Auto-Fill Plugin
// Written by Joe Sak http://www.joesak.com/2008/11/19/a-jquery-function-to-auto-fill-input-fields-and-clear-them-on-click/
(function($){
  $.fn.autofill = function(options){
    var defaults = {
      value:'',
      defaultTextColor:"#AAAAAA",
      activeTextColor:"#000000",
      password: false
    };
    var options = $.extend(defaults,options);
    return this.each(function(){
      var obj=$(this);
      obj.css({color:options.defaultTextColor})
        .val(options.value)
        .focus(function(){
          if(obj.val()==options.value){
            obj.val("").css({color:options.activeTextColor});
            if (options.password && obj.attr('type') == 'text') {
              obj.attr('type', 'password');
            }
          }
        })
        .blur(function(){
          if(obj.val()==""){
            obj.css({color:options.defaultTextColor}).val(options.value);
            if (options.password && obj.attr('type') == 'password') {
              obj.attr('type', 'text');
            }
          }
        });
    });
  };
})(jQuery);

(function ($) {
Drupal.behaviors.newscenter = {
  attach: function (context) {
    $('#search-block-form .form-text', context).autofill({
      value: "Search Articles ..."
    });
  } 
};
})(jQuery);

(function ($) {
Drupal.behaviors.newsletter = {
  attach: function (context) {
    $('.block-simplenews #edit-mail', context).autofill({
      value: "Your Email Address"
    });
  }
};

})(jQuery);
  
(function ($) {
Drupal.behaviors.newscenterbox = {
  attach: function (context) {
    $('#search-block-form--2 .form-text', context).autofill({
      value: "Search Articles ..."
    });
  } 
};
})(jQuery);


(function ($) {
	Drupal.behaviors.newscenterbox = {
  		attach: function (context) {
  			var obj = $('#block-superfish-1 ul li:first-child a');
    		obj.click(function(e){
    			var text = $(this).text();
    			e.preventDefault();
				$('#block-superfish-1 ul li:not(:first-child)').toggle();
				if (text == 'Show Menu') {
					$(this).text('Hide Menu');
				} else {
					$(this).text('Show Menu');
				}
    		});
        
    		$(window).resize(function(){
    			var w = $(window).width();
				if (w > 979) {
					$('#block-superfish-1 ul li:not(:first-child)').css("display", "inline-block").show();
  				} else {
  					$('#block-superfish-1 ul li:first-child a').text('Show Menu');
  					$('#block-superfish-1 ul li:not(:first-child)').hide();
  				} 
  			});
  		}
	};
})(jQuery);


(function ($) {
    Drupal.behaviors.imageclick = {
        attach: function (context) {
            $(document).on("contextmenu", "img", function (e) {
                alert('Context Menu event has fired!');
                return false;
            });
        }
    };
})(jQuery);

