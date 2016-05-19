<div id="page-top">
    <div class="page-inner">

        <?php if ($page['user_menu']): ?>
            <nav id="user-menu" class="clearfix">
                <?php print render($page['user_menu']); ?>
                <?php print render($page['search_box']); ?>
            </nav>
        <?php endif; ?>
    </div>

    <div class="page-inner <?php echo $grid_size ?>">

        <div class="header-wrapper clearfix">
            <div class="header-wrapper-inner <?php echo $grid_full_width ?>">
                <header>

                    <?php if ($logo): ?>
                        <div class="site-logo">
                        <a href="<?php print check_url($front_page); ?>"><img
                                src="<?php print $logo ?>"
                                alt="<?php print $site_name; ?>"/></a>
                        </div><?php print render($page['header']) ?>
                    <?php endif; ?>

                </header>
            </div>
        </div>
    </div>
</div>

<?php if ($page['main_menu']): ?>
    <div class="main-menu-wrapper clearfix">
        <div class="main-menu-wrapper-inner">
            <nav id="main-menu">
                <?php print render($page['main_menu']); ?>
            </nav>
        </div>
    </div>
<?php endif; ?>