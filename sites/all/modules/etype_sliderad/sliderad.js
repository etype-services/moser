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

            var timerlen = 5;
            var slideAniLen = 250;
            var timerID = new Array();
            var startTime = new Array();
            var obj = new Array();
            var endHeight = new Array();
            var moving = new Array();
            var dir = new Array();
            var mousedover = false;

            function slidetick(objname) {
                var elapsed = (new Date()).getTime() - startTime[objname];
                if (elapsed > slideAniLen) {
                    endSlide(objname);
                } else {
                    var d =Math.round(elapsed / slideAniLen * endHeight[objname]);
                    if(dir[objname] == "up")
                        d = endHeight[objname] - d;
                    obj[objname].style.height = d + "px";
                }
                return;
            }

            function slidedown(objname){
                if(moving[objname])
                    return;
                if(document.getElementById(objname).style.display != "none")
                    return; // cannot slide down something that is already visible
                moving[objname] = true;
                dir[objname] = "down";
                startslide(objname);

                if (!mousedover) {
                    mousedover = true;
                    var img = new Image();
//img.src = "http://localhost/add_mouseover_counter.php?tileID=" + TILE_ID;
                }
            }

            function slideup(objname){
                if(moving[objname])
                    return;
                if(document.getElementById(objname).style.display == "none")
                    return; // cannot slide up something that is already hidden
                moving[objname] = true;
                dir[objname] = "up";
                startslide(objname);
            }

            function startslide(objname) {
                obj[objname] = document.getElementById(objname);
                endHeight[objname] = parseInt(obj[objname].style.height);
                startTime[objname] = (new Date()).getTime();
                if(dir[objname] == "down"){
                    obj[objname].style.height = "px";
                }
                obj[objname].style.display = "block";
                timerID[objname] = setInterval('slidetick(\'' + objname + '\');',timerlen);
            }

            function endSlide(objname){
                clearInterval(timerID[objname]);
                if(dir[objname] == "up")
                    obj[objname].style.display = "none";
                obj[objname].style.height = endHeight[objname] + "px";
                delete(moving[objname]);
                delete(timerID[objname]);
                delete(startTime[objname]);
                delete(endHeight[objname]);
                delete(obj[objname]);
                delete(dir[objname]);
                return;
            }

            $('#adone').mouseover(function() {
                slidedown('adtwo');
            });
            $('#adone').mouseout(function() {
                slideup('adtwo');
            });

        }
    };
}(jQuery));
