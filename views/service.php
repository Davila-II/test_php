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
    <!-- Header Banner -->
    <div class="service-hero-wrap fl-wrap" style="position:relative; height:240px; overflow:hidden; background:#1a1a1a;">
        <div class="bg" data-bg="images/bg/2.jpg" style="position:absolute; top:0; left:0; width:100%; height:100%; background-size:cover; background-position:center; background-image:url('images/bg/2.jpg');"></div>
        <div class="overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(17,17,17,0.65);"></div>
        <div class="container" style="position:relative; z-index:5; height:100%; display:flex; flex-direction:column; justify-content:center;">
            <h1 style="color:#ffffff !important; font-size:34px; font-weight:800; text-transform:uppercase; letter-spacing:2px; margin:0;"><?= $pageTitleDisplay ?></h1>
            <p style="color:rgba(255,255,255,0.85) !important; font-size:14px; margin:10px 0 0 0; max-width:680px;"><?= $pageSubtitle ?></p>
        </div>
    </div>
    
    <!--column-wrap--> 
    <div class="column-wrap service-column-wrap" style="width:100% !important; float:none !important;">
        <!--column-wrap-container -->   
        <div class="column-wrap-container white-bg fl-wrap single_cwc" style="padding:0 !important;">
            <!--section--> 
            <section id="sec1" class="scroll-con-sec" style="padding: 40px 0 30px;">
                <div class="container">
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
