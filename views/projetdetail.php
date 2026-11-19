<?php
$propId = !empty($_GET['id']) ? (int)$_GET['id'] : 1;
$project = Database::fetchOne("SELECT * FROM properties WHERE id = ?", [$propId]);
if (!$project) {
    $project = Database::fetchOne("SELECT * FROM properties ORDER BY id ASC LIMIT 1");
}

// Next & Prev projects
$prev = Database::fetchOne("SELECT id, title, image FROM properties WHERE id < ? ORDER BY id DESC LIMIT 1", [$project['id']]);
$next = Database::fetchOne("SELECT id, title, image FROM properties WHERE id > ? ORDER BY id ASC LIMIT 1", [$project['id']]);

$isEn = i18n::isEn();
$typeLabel = $project['type'];
if ($isEn) {
    if ($typeLabel === 'Vente') $typeLabel = 'For Sale';
    elseif ($typeLabel === 'En cours') $typeLabel = 'In Progress';
    elseif ($typeLabel === 'Projet réalisé') $typeLabel = 'Completed';
}
?>
                    <!-- content-->
                    <div class="content">
                        <!--fixed-column-wrap-->
                        <div class="fixed-column-wrap init_fx-column">
                            <!--fixed-column-wrap-content-->
                            <div class="fixed-column-wrap-content">
                                <div class="fixed-column-wrap_bg">
                                    <div class="bg" data-bg="<?= htmlspecialchars($project['image']) ?>"></div>
                                    <div class="overlay"></div>
                                    <div class="hero-scroll-down-notifer">
                                        <div class="scroll-down-wrap">
                                            <div class="mousey">
                                                <div class="scroller"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fixed-column-dec"></div>
                                </div>
                                <!--fixed-column-wrap_title-->
                                <div class="fixed-column-wrap_title">
                                    <h2><span><?= htmlspecialchars($project['category']) ?></span></h2>
                                </div>
                                <!--fixed-column-wrap_title end-->
                                <div class="scroll-notifer"><?= __('scroll_down') ?></div>
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
                                <section>
                                    <div class="container">
                                        <div class="section-title fl-wrap">
                                            <h2><?= htmlspecialchars($project['title']) ?></h2>
                                            <p style="color:#008751; font-weight:700; font-size:16px;">
                                                <?php if (!empty($project['price'])): ?>
                                                    <?= __('prop_price') ?> : <?= htmlspecialchars($project['price']) ?> &bull; 
                                                <?php endif; ?>
                                                <?= __('prop_status') ?> : <?= htmlspecialchars($typeLabel) ?>
                                            </p>
                                            <div class="sec-title_dec"></div>
                                        </div>
                                        <div class="column-wrap-media fl-wrap">
                                            <div class="grid-item-holder hov_zoom" style="max-height:480px; overflow:hidden; border-radius:8px;">
                                                <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="respimg">
                                            </div>
                                        </div>
                                        <div class="pr-details-wrap pr-det_vis pr-details-ccp fl-wrap" style="margin-top:30px;">
                                            <div class="parallax-header">
                                                <span><?= __('prop_category') ?> : </span><a href="portfolio"><?= htmlspecialchars($project['category']) ?></a>
                                                <?php if (!empty($project['location'])): ?>
                                                    <span>&bull; <?= __('prop_location') ?> : </span><strong><?= htmlspecialchars($project['location']) ?></strong>
                                                <?php endif; ?>
                                                <?php if (!empty($project['surface'])): ?>
                                                    <span>&bull; <?= __('prop_surface') ?> : </span><strong><?= htmlspecialchars($project['surface']) ?></strong>
                                                <?php endif; ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            
                                            <div style="font-size:15px; line-height:1.8; color:#374151; margin:20px 0;">
                                                <?= nl2br(htmlspecialchars($project['description'] ?? 'Contactez DjoKhouse SCI pour obtenir le dossier complet, les coordonnées géographiques et organiser une visite sur le terrain.')) ?>
                                            </div>

                                            <div class="section-separator fl-wrap" style="margin: 30px 0;"><span></span></div>

                                            <!-- Inquire / Booking Box -->
                                            <div class="order-wrap fl-wrap" style="background:#f9fafb; padding:30px; border-radius:8px; border:1px solid #e5e7eb;">
                                                <h3 style="margin-bottom:12px; color:#111;"><?= __('prop_inquire_title') ?></h3>
                                                <p style="margin-bottom:20px; color:#6b7280; font-size:14px;"><?= __('prop_inquire_desc') ?></p>
                                                
                                                <form id="property-inquiry-form" onsubmit="submitInquiry(event)">
                                                    <input type="hidden" name="property_id" value="<?= $project['id'] ?>">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <input type="text" name="name" id="inq_name" placeholder="<?= __('prop_form_name') ?>" required style="width:100%; padding:12px; margin-bottom:14px; border:1px solid #d1d5db; border-radius:6px;">
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <input type="tel" name="phone" id="inq_phone" placeholder="<?= __('prop_form_phone') ?>" required style="width:100%; padding:12px; margin-bottom:14px; border:1px solid #d1d5db; border-radius:6px;">
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input type="email" name="email" id="inq_email" placeholder="<?= __('prop_form_email') ?>" style="width:100%; padding:12px; margin-bottom:14px; border:1px solid #d1d5db; border-radius:6px;">
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <textarea name="details" id="inq_details" placeholder="<?= __('prop_form_details') ?>" rows="3" style="width:100%; padding:12px; margin-bottom:14px; border:1px solid #d1d5db; border-radius:6px;"></textarea>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <button type="submit" id="btn-inquiry" class="btn float-btn" style="background:#008751; color:#fff; border:none; padding:12px 28px; border-radius:6px; cursor:pointer; font-weight:700;"><?= __('btn_submit_inquiry') ?></button>
                                                            <div id="inquiry-feedback" style="margin-top:12px; font-weight:600;"></div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!--content-nav_holder-->
                                            <div class="content-nav_holder fl-wrap cnh_sin-post" style="margin-top:40px;">
                                                <div class="content-nav">
                                                    <ul>
                                                        <?php if ($prev): ?>
                                                        <li>
                                                            <a href="projetdetail?id=<?= $prev['id'] ?>" class="ln ajax"><i class="fal fa-long-arrow-left"></i><span><?= __('prev') ?> : <?= htmlspecialchars($prev['title']) ?></span></a>
                                                            <div class="content-nav_mediatooltip cnmd_leftside"><img src="<?= htmlspecialchars($prev['image']) ?>" alt=""></div>
                                                        </li>
                                                        <?php endif; ?>
                                                        <?php if ($next): ?>
                                                        <li>
                                                            <a href="projetdetail?id=<?= $next['id'] ?>" class="rn ajax"><span><?= __('next') ?> : <?= htmlspecialchars($next['title']) ?></span> <i class="fal fa-long-arrow-right"></i></a>
                                                            <div class="content-nav_mediatooltip cnmd_rightside"><img src="<?= htmlspecialchars($next['image']) ?>" alt=""></div>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <!-- content-nav_holder end-->									
                                        </div>
                                    </div>
                                    <div class="section-number">
                                        <span>0</span>2<strong>.</strong>
                                    </div>
                                </section>
                                <!--section end--> 
                            </div>
                            <!--column-wrap-container end-->  
                        </div>
                        <!--column-wrap end-->					
                        <div class="limit-box fl-wrap"></div>
                    </div>
                    <!-- content end -->

                    <script>
                        function submitInquiry(e) {
                            e.preventDefault();
                            var btn = document.getElementById('btn-inquiry');
                            var feedback = document.getElementById('inquiry-feedback');
                            
                            btn.disabled = true;
                            btn.innerText = '<?= __('btn_send') ?>...';

                            $.ajax({
                                type: 'POST',
                                url: 'index.php?page=reservation',
                                data: {
                                    property_id: '<?= $project['id'] ?>',
                                    name: document.getElementById('inq_name').value,
                                    phone: document.getElementById('inq_phone').value,
                                    email: document.getElementById('inq_email').value,
                                    details: document.getElementById('inq_details').value
                                },
                                dataType: 'json',
                                success: function(res) {
                                    btn.disabled = false;
                                    btn.innerText = '<?= __('btn_submit_inquiry') ?>';
                                    if (res.status === 'success') {
                                        feedback.style.color = '#008751';
                                        feedback.innerText = res.message;
                                        document.getElementById('property-inquiry-form').reset();
                                    } else {
                                        feedback.style.color = '#b91c1c';
                                        feedback.innerText = res.message;
                                    }
                                },
                                error: function() {
                                    btn.disabled = false;
                                    btn.innerText = '<?= __('btn_submit_inquiry') ?>';
                                    feedback.style.color = '#b91c1c';
                                    feedback.innerText = "Error. Please try again.";
                                }
                            });
                        }
                    </script>
