<?php require_once ('header.inc.php'); ?>

<div id="page">
    <div class="page-inner <?php echo $grid_size ?>">

        <!-- Main Content -->
        <div class="main-content-wrapper clearfix">
            <div class="main-content-wrapper-inner">
                <section id="main-content">

                    <!-- Main 1 -->
                    <div class="main clearfix">
                        <div class="main-inner grid_8">

                            <?php if ($page['slideshow']): ?>
                                <div class="slideshow-wrapper clearfix">
                                    <div class="slideshow-wrapper-inner">
                                        <div id="slideshow">
                                            <?php print render($page['slideshow']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($page['preface_1']): ?>
                                <div class="preface-wrapper
                                preface-wrapper-top clearfix">
                                    <div class="preface-wrapper-inner">
                                        <div
                                            class="preface-wrapper-inner-inner">
                                            <section id="preface_1">
                                                <div><?php print render
                                                    ($page['preface_1']); ?></div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                        <?php if ($page['sidebar_first']): ?>
                            <aside
                                class="sidebar first-sidebar grid_4 clearfix">
                                <?php print render($page['sidebar_first']); ?>
                            </aside>
                        <?php endif; ?>
                    </div>

                    <!-- Main 2 Editor's Pick-->
                    <div class="main clearfix">
                        <div class="main-inner grid_12">
                            <?php print render($page['content_middle']); ?>
                        </div>


                        <!-- Main 3 -->
                        <div class="main clearfix">
                            <div class="main-inner grid_8">

                                <?php if ($page['preface_2'] || $page['preface_3']): ?>
                                    <div class="preface-wrapper clearfix">
                                        <div class="preface-wrapper-inner">
                                            <div
                                                class="preface-wrapper-inner-inner">
                                                <section id="preface">
                                                    <div><?php print render($page['preface_2']); ?></div>
                                                    <div><?php print render($page['preface_3']); ?></div>
                                                </section>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>


                            </div>

                            <?php if ($page['sidebar_second']): ?>
                                <aside
                                    class="sidebar second-sidebar grid_4 clearfix">
                                    <?php print render($page['sidebar_second']); ?>
                                </aside>
                            <?php endif; ?>
                        </div>

                </section>
            </div>
        </div>

        <?php print render($page['content_bottom']); ?>

        <?php if ($page['postscript_1'] || $page['postscript_2'] || $page['postscript_3'] || $page['postscript_4']): ?>
            <div class="postscript-wrapper clearfix">
                <div class="postscript-wrapper-inner">
                    <div class="postscript-wrapper-inner-inner">
                        <section id="postscript">
                            <div
                                class="grid_4"><?php print render($page['postscript_1']); ?></div>
                            <div
                                class="grid_4"><?php print render($page['postscript_2']); ?></div>
                            <div
                                class="grid_4"><?php print render($page['postscript_3']); ?></div>
                        </section>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div><!-- page -->
