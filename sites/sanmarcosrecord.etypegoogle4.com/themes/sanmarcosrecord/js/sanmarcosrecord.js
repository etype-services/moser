(function($){

    var opts = {
        fx: 'scrollHorz',
        pause: 1,
        timeout: 5000,
    }

    $("#slideshow").cycle(opts);

    $(window).resize(function(){
        opts.width = $("#slideshow").width();
        opts.height = $("#slideshow img:first-child").height();
        $("#slideshow").cycle('destroy');
        $("#slideshow").cycle(opts);
    });

})(jQuery);