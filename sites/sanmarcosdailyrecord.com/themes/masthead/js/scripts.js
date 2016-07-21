(function ($) {

    $(document).ready(function(){															 

	  if ($.browser.msie) { } else {
		$('ul.menu').mobileMenu({
		combine: true,
		switchWidth: 760,
		prependTo: ".header-wrapper-inner",
		nested: false,
		groupPageText: 'More',
		topOptionText: 'Select a page'
		});
	  }

	  /* "Slider" ads */
        $('#ad1, #ad2').mouseenter(function () {// show pohelp
            $("#ad1").css({
                "z-index": "1110"
            });
            $("#ad2").fadeIn();
        });
        $("#ad2").mouseleave(function(){ // hide pohelp on mouse out from pohelp
            $("#ad2").fadeOut();
            $("#ad1").css({"z-index":"1095"});
        });
	
    }); 

 	Drupal.behaviors.bonesSuperfish = {
	
	  attach: function(context, settings) {
			  
	  $('#user-menu ul.menu', context).superfish({
		  delay: 400,											    
		  animation: {height:'show'},
		  speed: 500,
		  easing: 'easeOutBounce', 
		  autoArrows: false,
		  dropShadows: false /* Needed for IE */
	  });
		  
	  }
    }	
				
	$(function() {
		
		$('.postscript-wrapper img').hover(function() {
		  $(this).animate({
			  backgroundColor: "#ff7800", opacity: "1.0"
		  }, 'fast'); }, function() {
		  $(this).animate({
			  backgroundColor: "#555", opacity: "0.9"
		  }, 'normal');
		});
	
	});


Drupal.behaviors.newscenterbox = {
  attach: function (context) {
  	var obj = $('#menu-1389-1 a');
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
    
    $(window).resize( function(){
    	var w = $(window).width();
		if (w > 767){
			$('#block-superfish-1 ul li:not(:first-child)').show();
  		} else {
  			$('#menu-1389-1 a').text('Show Menu');
  			$('#block-superfish-1 ul li:not(:first-child)').hide();
  		}
	});
    
  } 
};

})(jQuery);