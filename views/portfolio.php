<?php
$allProperties = Database::fetchAll("SELECT * FROM properties ORDER BY id DESC");
$categories = Database::fetchAll("SELECT DISTINCT category FROM properties ORDER BY category ASC");

$catMap = [
    'Terrain' => __('filter_land'),
    'Urbanisme' => __('filter_urban'),
    'Topographie' => __('filter_topo'),
    'Bornage' => __('filter_bornage'),
    'Maison' => __('filter_house'),
    'Prestation' => __('filter_services')
];

function translatePropType($type) {
    if (!i18n::isEn()) return $type;
    switch ($type) {
        case 'Vente': return 'For Sale';
        case 'En cours': return 'In Progress';
        case 'Projet réalisé': return 'Completed';
        default: return $type;
    }
}
?>
                    <!-- content-->
                    <div class="content init-fw_header hor-content_padd full-height">
                        <!--horizontal-grid -->   
                        <div class="horizontal-grid-wrap fl-wrap">
                            <!-- portfolio start -->
                            <div id="portfolio_horizontal_container" class="two-ver-columns lightgallery">
                                <?php foreach ($allProperties as $prop): 
                                    $filterClass = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $prop['category']));
                                    $catLabel = $catMap[$prop['category']] ?? $prop['category'];
                                    $typeLabel = translatePropType($prop['type']);
                                ?>
                                <!-- portfolio_item-->
                                <div class="portfolio_item <?= $filterClass ?>">
                                    <div class="grid-item-holder hov_zoom">
                                        <img src="<?= htmlspecialchars($prop['image']) ?>" alt="<?= htmlspecialchars($prop['title']) ?>">
                                        <div class="grid-det">
                                            <div class="grid-det_category">
                                                <a href="#"><?= htmlspecialchars($catLabel) ?></a> 
                                                <a href="#"><?= htmlspecialchars($typeLabel) ?></a>
                                                <?php if (!empty($prop['surface'])): ?>
                                                    <span style="color:#eee; font-size:11px;">&bull; <?= htmlspecialchars($prop['surface']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="grid-det-item">
                                                <a href="projetdetail?id=<?= $prop['id'] ?>" class="ajax grid-det_link"><?= htmlspecialchars($prop['title']) ?></a>
                                                <?php if (!empty($prop['price'])): ?>
                                                    <div style="font-size:12px; color:#008751; font-weight:700; margin-top:4px;"><?= htmlspecialchars($prop['price']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?= htmlspecialchars($prop['image']) ?>" class="grid-media-zoom image-popup"><i class="far fa-search"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <!-- portfolio_item end-->
                                <?php endforeach; ?>
                            </div>
                            <!-- portfolio end -->
                        </div>
                        <!--horizontal-grid end -->
                        <div class="fixed-bottom-panel fbp_dec fbp_mob-vis">
                            <div class="show-fixed-filters act-filter"><?= i18n::isEn() ? 'Filters' : 'Filtres' ?></div>
                            <div class="fbp-filters">
                                <div class="gallery-filters init_hidden_filter">
                                    <a href="#" class="gallery-filter transition2 gallery-filter-active" data-filter="*"><?= __('filter_all') ?> (<?= count($allProperties) ?>)</a>
                                    <?php foreach ($categories as $cat): 
                                        $catClass = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cat['category']));
                                        $label = $catMap[$cat['category']] ?? $cat['category'];
                                    ?>
                                        <a href="#" class="gallery-filter transition2" data-filter=".<?= $catClass ?>"><?= htmlspecialchars($label) ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="count-folio fix-count-folio">
                                    <div class="num-album"><span></span></div>
                                    <div class="all-album"></div>
                                </div>
                            </div>
                            <div class="hero-scroll-down-notifer hsn_2">
                                <div class="scroll-down-wrap ">
                                    <div class="mousey">
                                        <div class="scroller"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fs-pg-idicator_wrap hor-scroll-idicator">
                            <div class="progress-indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-1 -1 44 44">
                                    <circle cx="16" cy="16" r="15.9155" class="progress-bar__background" />
                                    <circle cx="16" cy="16" r="15.9155" class="progress-bar__progress js-progress-bar" />
                                </svg>
                            </div>
                        </div>
                        <div class="body-color-bg">
                            <div class="fwc-dec fwc-dec_right"></div>
                            <div class="hor-folio-filter_title filt-text">
                                <span><?= __('cat_catalogue') ?></span>
                            </div>
                            <div class="counter-clone">
                                <div class="count-folio">
                                    <div class="num-album"><span></span></div>
                                    <div class="all-album"></div>
                                </div>
                            </div>
                        </div>
                        <div class="fwc-dec fwc-dec_top fcw_mob-hidden"></div>
                    </div>
                    <!-- content end -->
                </div>
                <!-- content-holder end -->
                <!-- share-wrapper-->
                <div class="share-wrapper">
                    <div class="close-share-btn"><i class="fal fa-times"></i></div>
                    <div class="share-container fl-wrap isShare"></div>
                </div>
                <!-- share-wrapper end -->
            </div>
            <!-- wrapper end -->
            <!-- cursor-->
            <div class="element">
                <div class="element-item"></div>
            </div>
            <!-- cursor end-->
        </div>
        <!-- Main end -->
        <!--=============== scripts ===============-->
        <script src="js/jquery.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
