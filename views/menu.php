<?php
$current_ctrl = $ctrl ?? 'home';
$is_home = in_array($current_ctrl, ['home', 'accueil', '']);
$is_about = ($current_ctrl === 'about');
$is_services_sub = in_array($current_ctrl, ['conseils', 'urbanisme', 'topographie', 'bornage', 'prestation']);
$is_services = ($current_ctrl === 'service' || $current_ctrl === 'services' || $is_services_sub);
$is_portfolio = ($current_ctrl === 'portfolio');
$is_contact = ($current_ctrl === 'contact');
?>
            <!-- wrapper -->   
            <div id="wrapper">
                <!-- content-holder -->    
                <div class="content-holder" data-pagetitle="<?= __('site_title') ?>">
                    <!-- nav-holder-->
                    <div class="nav-holder but-hol">
                        <div class="nav-scroll-bar-wrap fl-wrap">
                            <a href="home" class="ajax logo_menu"><img src="images/logo2.png" alt="logo DjoKhouse SCI"></a>

                            <div class="nav-container fl-wrap">
                                <!-- nav -->
                                <nav class="nav-inner" id="menu">
                                    <ul>
                                        <li>
                                            <a href="home" class="ajax <?= $is_home ? 'act-link' : '' ?>"><?= __('nav_home') ?></a>
                                        </li>
                                        <li>
                                            <a href="about" class="ajax <?= $is_about ? 'act-link' : '' ?>"><?= __('nav_about') ?></a>
                                        </li>
                                        <li class="menu-item-has-children <?= $is_services ? 'active-parent' : '' ?>">
                                            <a href="service" class="ajax <?= $is_services ? 'act-link' : '' ?>">
                                                <span><?= __('nav_services') ?></span>
                                                <i class="fal fa-chevron-down submenu-arrow"></i>
                                            </a>
                                            <ul class="sub-menu-list">
                                                <li><a href="conseils" class="ajax <?= ($current_ctrl === 'conseils') ? 'act-link' : '' ?>"><?= __('nav_conseils') ?></a></li>
                                                <li><a href="urbanisme" class="ajax <?= ($current_ctrl === 'urbanisme') ? 'act-link' : '' ?>"><?= __('nav_urbanisme') ?></a></li>
                                                <li><a href="topographie" class="ajax <?= ($current_ctrl === 'topographie') ? 'act-link' : '' ?>"><?= __('nav_topographie') ?></a></li>
                                                <li><a href="bornage" class="ajax <?= ($current_ctrl === 'bornage') ? 'act-link' : '' ?>"><?= __('nav_bornage') ?></a></li>
                                                <li><a href="prestation" class="ajax <?= ($current_ctrl === 'prestation') ? 'act-link' : '' ?>"><?= __('nav_prestation') ?></a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="portfolio" class="ajax <?= $is_portfolio ? 'act-link' : '' ?>"><?= __('nav_portfolio') ?></a>
                                        </li>
                                        <li>
                                            <a href="contact" class="ajax <?= $is_contact ? 'act-link' : '' ?>"><?= __('nav_contact') ?></a>
                                        </li>
                                    </ul>
                                </nav>
                                <!-- nav end-->
                                <!-- Language Switcher — bas du menu -->
                                <div class="djok-menu-lang-wrap">
                                    <?= i18n::renderSwitcher() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-holder-dec"></div>
                    <div class="nav-overlay"></div>
                    <!-- nav-holder end -->
