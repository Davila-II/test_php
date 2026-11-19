<?php
$isEn = i18n::isEn();
$pageTitleDisplay = $isEn ? 'Our Services' : 'Nos Services';
$pageSubtitle = $isEn 
    ? 'Specialized technical and legal expertise for all your land and property projects in Cameroon.'
    : 'Une expertise technique et juridique spécialisée pour tous vos projets fonciers et immobiliers au Cameroun.';

$serviceList = [
    [
        'slug' => 'conseils',
        'title' => $isEn ? 'Land & Property Advisory' : 'Conseils Fonciers & Immobiliers',
        'desc' => $isEn 
            ? 'Legal and technical support to secure your land purchases, audit land titles and prevent disputes in Cameroon.' 
            : 'Accompagnement juridique et technique pour sécuriser vos acquisitions de terrains, auditer les titres fonciers et prévenir les litiges.',
        'image' => 'images/services/1.jpg',
        'icon' => 'fal fa-balance-scale'
    ],
    [
        'slug' => 'urbanisme',
        'title' => $isEn ? 'Urban Planning & Subdivision' : 'Urbanisme & Aménagement',
        'desc' => $isEn 
            ? 'Subdivision studies, master development plans, building permit files, and environmental impact assessments.' 
            : 'Études de lotissement, plans d’aménagement, dossiers de permis de construire et études d’impact environnemental.',
        'image' => 'images/services/2.jpg',
        'icon' => 'fal fa-city'
    ],
    [
        'slug' => 'topographie',
        'title' => $isEn ? 'Topography & Civil Engineering' : 'Topographie & Génie Civil',
        'desc' => $isEn 
            ? 'High-precision topographic surveys, building layout staking, road network studies (VRD), and drone photogrammetry.' 
            : 'Levés topographiques de haute précision, implantations de bâtiments, études de voiries et réseaux divers (VRD), photogrammétrie par drone.',
        'image' => 'images/services/3.jpg',
        'icon' => 'fal fa-compass'
    ],
    [
        'slug' => 'bornage',
        'title' => $isEn ? 'Boundary Demarcation & Land Titling' : 'Bornage & Sécurisation Foncière',
        'desc' => $isEn 
            ? 'Official cadastral demarcation, parcel subdivision, land title registration and boundary dispute resolutions.' 
            : 'Bornage officiel contradictoire, morcellement de parcelles, immatriculation au titre foncier et règlement des empiétements.',
        'image' => 'images/services/4.jpg',
        'icon' => 'fal fa-map-marked-alt'
    ],
    [
        'slug' => 'prestation',
        'title' => $isEn ? 'Civil Works & Construction Management' : 'Prestation de Services & BTP',
        'desc' => $isEn 
            ? 'Construction site supervision, architectural design, earthworks, and structural renovation works.' 
            : 'Maîtrise d’œuvre, suivi de chantiers, conception architecturale, terrassement et travaux de gros œuvre.',
        'image' => 'images/services/6.jpg',
        'icon' => 'fal fa-tools'
    ],
];
?>

<!-- content-->
<div class="content service-page-content">
    <!--fixed-column-wrap-->
    <div class="fixed-column-wrap init_fx-column">
        <!--fixed-column-wrap-content-->
        <div class="fixed-column-wrap-content">
            <div class="fixed-column-wrap_bg">
                <div class="bg" data-bg="images/bg/2.jpg"></div>
                <div class="overlay"></div>
                <div class="fixed-column-dec"></div>
            </div>
            <!--fixed-column-wrap_title-->
            <div class="fixed-column-wrap_title">
                <h2 style="color:#ffffff !important;"><span style="color:#ffffff !important;"><?= $pageTitleDisplay ?></span></h2>
            </div>
            <!--fixed-column-wrap_title end-->
            <div class="fwc-dec fwc-dec_top"></div>
            <div class="fwc-dec fwc-dec_right"></div>
            <div class="fixed-column-wrap_dec"></div>
        </div>
        <!--fixed-column-wrap-content end-->
    </div>
    <!--fixed-column-wrap end-->
    
    <!--column-wrap--> 
    <div class="column-wrap">
        <!--column-wrap-container -->   
        <div class="column-wrap-container white-bg fl-wrap single_cwc">
            <div class="col-wc_dec"></div>
            
            <!--section--> 
            <section id="sec1" class="scroll-con-sec" style="padding: 60px 0 30px;">
                <div class="container">
                    <div class="section-title fl-wrap">
                        <h2><?= $pageTitleDisplay ?></h2>
                        <p style="max-width:700px !important"><?= $pageSubtitle ?></p>
                        <div class="sec-title_dec"></div>
                    </div>

                    <div class="row">
                        <?php foreach ($serviceList as $s): ?>
                        <div class="col-md-6" style="margin-bottom: 35px;">
                            <div class="service-box" style="background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.04); transition:transform 0.3s ease, box-shadow 0.3s ease;">
                                <div style="height:200px; overflow:hidden; position:relative;">
                                    <img src="<?= $s['image'] ?>" alt="<?= htmlspecialchars($s['title']) ?>" style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease;">
                                    <div style="position:absolute; top:15px; right:15px; width:44px; height:44px; background:#008751; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 3px 10px rgba(0,135,81,0.3);">
                                        <i class="<?= $s['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div style="padding:25px;">
                                    <h3 style="font-size:18px; font-weight:700; color:#111; margin-bottom:12px;">
                                        <a href="<?= $s['slug'] ?>" class="ajax" style="color:#111; text-decoration:none;"><?= $s['title'] ?></a>
                                    </h3>
                                    <p style="color:#6b7280; font-size:13px; line-height:22px; margin-bottom:20px; min-height:66px;">
                                        <?= $s['desc'] ?>
                                    </p>
                                    <a href="<?= $s['slug'] ?>" class="ajax btn color-bg" style="display:inline-block; padding:10px 22px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                                        <span><?= $isEn ? 'Learn More' : 'En savoir plus' ?> <i class="fal fa-arrow-right" style="margin-left:6px;"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Call to action block -->
                    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:35px 30px; margin-top:20px; text-align:center;">
                        <h3 style="font-size:20px; font-weight:700; color:#111; margin-bottom:10px;">
                            <?= $isEn ? 'Need a custom quote or project study?' : 'Un projet spécifique ou un besoin de devis ?' ?>
                        </h3>
                        <p style="color:#6b7280; font-size:14px; max-width:600px; margin:0 auto 20px;">
                            <?= $isEn 
                                ? 'Our engineers and legal experts are at your disposal to examine your project.' 
                                : 'Nos ingénieurs géomètres et experts fonciers sont à votre disposition pour analyser vos besoins.' ?>
                        </p>
                        <a href="contact" class="ajax btn color-bg" style="display:inline-block; padding:12px 30px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:1.5px; border-radius:4px;">
                            <span><?= $isEn ? 'Contact Us' : 'Contactez notre équipe' ?></span>
                        </a>
                    </div>
                </div>
            </section>
            <!--section end-->
        </div>
        <!--column-wrap-container end -->
    </div>
    <!--column-wrap end-->
    <div class="limit-box fl-wrap"></div>
</div>
<!-- content end-->
