var timerlen = 5;
var slideAniLen = 250;
var timerID = new Array();
var startTime = new Array();
var obj = new Array();
var endHeight = new Array();
var moving = new Array();
var dir = new Array();
var mousedover = false;
//// START PREVIEW OPEN ///////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function preview(objname){
	if(moving[objname])
		return;
	previewdown(objname);  /// uncomment this line as well as the next in order for the ad to expand open on page load and then retract after set time. ////
    setTimeout("previewup('"+objname+"')", 20000);  //// uncomment the line to the left and adjust the time that the add is expanded on page load. ////
//// START - SET COOKIE FOR PREVIEW FUNCTION //////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//var cookie = getcookie("sbb");
// alert(cookie);
if(cookie)
 {
  document.getElementById(objname).style.display = 'none';
 }
 else
 {
  document.getElementById(objname).style.display = 'block';
  var today = new Date();
  var expire = new Date();
  expire.setTime(today.getTime() + 600000);  // currently set for 10 minutes. 600000 milliseconds = 10min.
  document.cookie = "sbb=1;expires="+expire.toGMTString();
}
function getcookie(cookiename) {
var cookiestring=""+document.cookie;
var index1=cookiestring.indexOf(cookiename);
if (index1==-1 || cookiename=="") return "";
var index2=cookiestring.indexOf(';',index1);
if (index2==-1) index2=cookiestring.length;
return unescape(cookiestring.substring(index1+cookiename.length+1,index2));
	}
//// END - SET COOKIE FOR PREVIEW FUNCTION //////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}
function previewdown(objname){
        if(moving[objname])
                return;
        if(document.getElementById(objname).style.display != "none")
                return; // cannot slide down something that is already visible
        moving[objname] = true;
        dir[objname] = "down";
        startslide(objname);
}
function previewup(objname){
        if(moving[objname])
                return;
        if(document.getElementById(objname).style.display == "none")
                return; // cannot slide up something that is already hidden
        moving[objname] = true;
        dir[objname] = "up";
        startslide(objname);
}
//// END PREVIEW OPEN ///////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
function startslide(objname){
        obj[objname] = document.getElementById(objname);
        endHeight[objname] = parseInt(obj[objname].style.height);
        startTime[objname] = (new Date()).getTime();
        if(dir[objname] == "down"){
                obj[objname].style.height = "px";
        }
        obj[objname].style.display = "block";
        timerID[objname] = setInterval('slidetick(\'' + objname + '\');',timerlen);
}
function slidetick(objname){
        var elapsed = (new Date()).getTime() - startTime[objname];
        if (elapsed > slideAniLen)
                endSlide(objname)
        else {
                var d =Math.round(elapsed / slideAniLen * endHeight[objname]);
                if(dir[objname] == "up")
                        d = endHeight[objname] - d;
                obj[objname].style.height = d + "px";
        }
        return;
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

(function($) {
    $('#adone').mouseover(function() {
        slidedown('adtwo');
    });
    $('#adone').mouseout(function() {
        slideup('adtwo');
    });
})(jQuery);
