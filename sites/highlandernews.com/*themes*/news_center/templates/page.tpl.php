<div id="page-wrapper" class="container" ><div id="page">
		
		<?php print render($page['top_advertisement']); ?>  
	
  <div id="header"><div class="section clearfix">

    <?php if ($logo): ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a>
	  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="mobile-logo"><img src="<?php print $base_path; ?><?php print $directory; ?>/logo_mobile.png" alt="<?php print t('Home'); ?>" /></a>
    <?php endif; ?>

    <?php if ($site_name || $site_slogan): ?>
      <div id="name-and-slogan">
        <?php if ($site_name): ?>
          <?php if ($title): ?>
            <div id="site-name"><strong>
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </strong></div>
          <?php else: /* Use h1 when the content title is empty */ ?>
            <h1 id="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </h1>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($site_slogan): ?>
          <div id="site-slogan"><?php print $site_slogan; ?></div>
        <?php endif; ?>
      </div><!-- /#name-and-slogan -->
    <?php endif; ?>
	
	<?php print render($page['top_area']); ?>    

    <?php print render($page['header']); ?>

  </div></div><!-- /.section, /#header -->

  <div id="main-wrapper"><div id="main" class="clearfix<?php if ($main_menu || $page['navigation']) { print ' with-navigation'; } ?>">

    <div id="content" class="column"><div class="section">
      <?php print render($page['highlighted']); ?>
      <?php print $breadcrumb; ?>
	  
	  <?php print render($page['slideshow']); ?>
	  
      <a id="main-content"></a>
      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
        <h1 class="title" id="page-title"><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
      <?php print $messages; ?>
      <?php if ($tabs = render($tabs)): ?>
        <div class="tabs"><?php print $tabs; ?></div>
      <?php endif; ?>
      <?php print render($page['help']); ?>
      <?php if ($action_links): ?>
        <ul class="action-links"><?php print render($action_links); ?></ul>
      <?php endif; ?>
	  <?php print render($page['content0']); ?>
	  <?php print render($page['content1']); ?>
	  <?php print render($page['content2']); ?>
      <?php print render($page['content']); ?>
      <?php print $feed_icons; ?>
    </div></div><!-- /.section, /#content -->   

    <?php print render($page['sidebar_first']); ?>

    <?php print render($page['sidebar_second']); ?>

  </div></div><!-- /#main, /#main-wrapper -->
  
  <div id="footer">
  
    <?php print render($page['footer']); ?>
  
    <?php print render($page['footer_first_column']); ?>
	
	<?php print render($page['footer_second_column']); ?>
	
	<?php print render($page['footer_third_column']); ?>
	
	<?php print render($page['footer_fourth_column']); ?>
	
	<div class="clear"></div>
	
	<?php print render($page['footer_closure']); ?>
	
  </div>

</div></div><!-- /#page, /#page-wrapper -->

<?php print render($page['bottom']); ?>