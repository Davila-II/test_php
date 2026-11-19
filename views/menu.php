            <!-- wrapper -->   
            <div id="wrapper">
                <!-- content-holder -->    
                <div class="content-holder" data-pagetitle="<?= __('site_title') ?>">
                    <!-- nav-holder-->
                    <div class="nav-holder but-hol">
                        <div class="nav-scroll-bar-wrap fl-wrap">
                            <a href="home" class="ajax logo_menu"><img src="images/logo2.png" style="height:60px !important; width:auto !important; padding:4px !important;" alt="logo DjoKhouse SCI"></a>

                            <div class="nav-container fl-wrap">
                                <!-- nav -->
                                <nav class="nav-inner" id="menu">
                                    <ul>
                                        <li>
                                            <a href="home" class="ajax act-link"><?= __('nav_home') ?></a>
                                        </li>
                                        <li><a href="about" class="ajax"><?= __('nav_about') ?></a></li>
                                        <li class="menu-item-has-children">
                                            <a href="service" class="ajax"><?= __('nav_services') ?></a>
                                            <ul class="sub-menu-list">
                                                <li><a href="conseils" class="ajax"><?= __('nav_conseils') ?></a></li>
                                                <li><a href="urbanisme" class="ajax"><?= __('nav_urbanisme') ?></a></li>
                                                <li><a href="topographie" class="ajax"><?= __('nav_topographie') ?></a></li>
                                                <li><a href="bornage" class="ajax"><?= __('nav_bornage') ?></a></li>
                                                <li><a href="prestation" class="ajax"><?= __('nav_prestation') ?></a></li>
                                            </ul>
                                        </li>
                                        <li><a href="portfolio" class="ajax"><?= __('nav_portfolio') ?></a></li>
                                        <li><a href="contact" class="ajax"><?= __('nav_contact') ?></a></li>
                                    </ul>
                                </nav>
                                <!-- nav end-->
                                <!-- Language Switcher — bas du menu -->
                                <div class="djok-menu-lang-wrap">
                                    <?= i18n::renderSwitcher() ?>
                                </div>
                            </div>
                        </div>
                        <!-- nav-title-->
                        <div class="nav-holder-line"></div>
                        <div class="nav-title"><span><?= __('btn_menu') ?></span></div>
                        <!-- nav-title end-->
                    </div>
                    <div class="nav-holder-dec"></div>
                    <div class="nav-overlay"></div>
                    <!-- nav-holder end -->
