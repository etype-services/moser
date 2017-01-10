<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>

<head profile="<?php print $grddl_profile; ?>">
<?php print $head; ?>
<title><?php print $head_title; ?></title>
<?php 
	print $styles;
	print $scripts; 
?>
<style type="text/css">
	<?php if (isset($googlewebfonts)): print $googlewebfonts; endif; ?>
	<?php if (isset($theme_setting_css)): print $theme_setting_css; endif; ?>
	<?php 
	// custom typography
	if (isset($typography)): print $typography; endif; 
	
	?>

	<?php if (isset($custom_css)): print $custom_css; endif; ?>
</style>
<?php if (isset($header_code)): print $header_code; endif;?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php 
		print $page_bottom; 
		if ($footer_code): print $footer_code; endif;
	?>
  <!-- AddToAny BEGIN -->
  <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
      <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
      <a class="a2a_button_facebook"></a>
      <a class="a2a_button_twitter"></a>
      <a class="a2a_button_google_plus"></a>
  </div>
  <script async src="https://static.addtoany.com/menu/page.js"></script>
  <!-- AddToAny END -->
</body>
</html>
