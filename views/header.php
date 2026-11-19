<!DOCTYPE HTML>
<html lang="<?= i18n::lang() ?>">
<head>
        <!--=============== basic  ===============-->
        <meta charset="UTF-8">
        <title><?= !empty($page_title) ? htmlspecialchars($page_title) : __('site_title') ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="robots" content="index, follow"/>
        <meta name="keywords" content="DjoKhouse, SCI, immobilier, terrain, Cameroun, topographie, urbanisme, land, real estate, Douala"/>
        <meta name="description" content="<?= __('meta_description') ?>"/>
        <!--=============== css  ===============-->
        <link type="text/css" rel="stylesheet" href="css/plugins.css">
        <link type="text/css" rel="stylesheet" href="css/style.css">
        <link type="text/css" rel="stylesheet" href="css/color.css">
        <link type="text/css" rel="stylesheet" href="css/light.css">
        <!--=============== favicons ===============-->
        <link rel="shortcut icon" href="images/favicon.ico">
    </head>
    <body>
        <!--loader-->
        <div class="loader-wrap">
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
        </div>
        <!--loader end--> 
        <!-- main start  -->
        <div id="main">
            <!-- main-header-->
            <header class="main-header">
                <a href="home" class="logo-holder ajax"><img src="images/logo.png" style="height:75px !important; width:auto !important; padding:5px !important;" alt="logo DjoKhouse SCI"></a>
                <!-- nav-button-wrap-->
                <div class="nav-button but-hol">
                    <span class="nos"></span>
                    <span class="ncs"></span>
                    <span class="nbs"></span>
                    <div class="menu-button-text"><?= __('btn_menu') ?></div>
                </div>
                <!-- nav-button-wrap end-->

                <div class="progress-bar-wrap">
                    <div class="progress-bar color-bg"></div>
                </div>
                <div class="header_social">
                    <ul>
                        <li><a href="#" target="_blank">Fb</a></li>
                        <li><a href="#" target="_blank">In</a></li>
                    </ul>
                </div>
                <!-- share-button -->
                <div class="folio-btn showshare">
                    <div class="folio-btn-item"></div>
                    <span class="folio-btn-tooltip"><?= __('btn_share') ?></span>
                </div>
                <!-- share-button end -->
            </header>
            <!-- main-header end-->
            <!-- top-header-->
            <div class="top-header top-header-half">
                <div class="page-subtitle">
                    <span></span>
                </div>
                <a href="contact" class="single-page-fixed-row-link ajax"><i class="fal fa-envelope"></i> <span><?= __('btn_contact_us') ?></span></a>			
            </div>
            <!-- top-header end-->
