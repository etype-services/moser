<!DOCTYPE html>
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>" <?php print $rdf_namespaces; ?>>

<head>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>  
  <?php print $styles; ?>
  <?php print $scripts; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">  
  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
  <!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<style>
#capption{
   display: none;
}
.views-slideshow-pager-field-item{
float:left !important;
}
.views-slideshow-pager-field-item .views_slideshow_pager_field_item .views-row-odd{
float:left;
}
</style>
<body class="<?php print $classes; ?>"<?php print $attributes;?>>
<!--<script type="text/javascript" src="http://www.sanmarcosrecord.com/misc/modernizr.custom.05929.js"></script>-->
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  
  <?php print $page; ?>
  <?php print $page_bottom; ?>


  <script language='javascript' type='text/javascript'>
    (function($) {
      $("#ad1, #ad2").mouseenter(function () {// show pohelp
        $("#ad1").css({
          "z-index": "1110"
        });
        $("#ad2").fadeIn();
      });
      $("#ad2").mouseleave(function(){ // hide pohelp on mouse out from pohelp
        $("#ad2").fadeOut();
        $("#ad1").css({"z-index":"1095"});
      });
    })(jQuery);

 </script>
  <script language='javascript' type='text/javascript'>
  jQuery('a[href*="http://"]:not([href*="http://www.sanmarcosrecord.com"])').attr('rel', 'nofollow');

jQuery('a[href*="https://"]:not([href*="http://sanmarcosrecord.com"])').attr('rel', 'nofollow');
   </script>
<!----------Pladoogle Code--Patent 7,904,335 Patents Pending 12/099,782 and 13/115,959---------->
<script SRC="http://www.siteencore.com/etypeservices/sanmarcosrecord/stcz2.zbma" type="text/javascript" defer="defer"></script>

<!----------------------------------------  Pladoogle ------------------------------------->

</body>

</html>