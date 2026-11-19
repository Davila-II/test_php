<?php
global $site_settings;
if (empty($site_settings)) {
    $site_settings = Database::fetchOne("SELECT * FROM site_settings WHERE id = 1");
}
?>
                    <!-- content-->
                    <div class="content full-height cfh_nomob">
                        <!-- map-view-wrap --> 
                        <div class="map-view-wrap white-bg">
                            <div class="map-view-wrap_item fl-wrap">
                                <div class="contact-details">
                                    <h4><?= __('contact_header') ?></h4>
                                    <ul>
                                        <li><span>01. <?= __('contact_email_lbl') ?> :</span><a href="mailto:<?= htmlspecialchars($site_settings['email']) ?>"><?= htmlspecialchars($site_settings['email']) ?></a></li>
                                        <li><span>02. <?= __('contact_addr_lbl') ?> : </span><a href="#"><?= htmlspecialchars($site_settings['address']) ?></a></li>
                                        <li><span>03. <?= __('contact_tel_lbl') ?> :</span><a href="tel:<?= htmlspecialchars($site_settings['phone']) ?>"><?= htmlspecialchars($site_settings['phone']) ?></a></li>
                                        <?php if (!empty($site_settings['phone2'])): ?>
                                            <li><span>04. <?= __('contact_wa_lbl') ?> :</span><a href="tel:<?= htmlspecialchars($site_settings['phone2']) ?>"><?= htmlspecialchars($site_settings['phone2']) ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="btn float-btn act-cf"><?= __('btn_write_us') ?></div>
                            </div>
                        </div>
                        <!--map-view-wrap end -->						
                        <!--fixed-column-wrap-->
                        <div class="fixed-column-wrap fcw_fw">
                            <!--fixed-column-wrap-content-->
                            <div class="fixed-column-wrap-content">
                                <div class="fixed-column-wrap_bg">
                                    <div class="map-container">
                                        <div id="map-single" class="map" data-latlog="[4.0105092 , 9.699614]" data-popuptext="DjoKhouse SCI, Douala, Cameroun."></div>
                                    </div>
                                    <div class="hero-scroll-down-notifer">
                                        <div class="scroll-down-wrap">
                                            <div class="mousey">
                                                <div class="scroller"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fixed-column-dec"></div>
                                </div>
                                <div class="fwc-dec fwc-dec_top"></div>
                                <div class="fwc-dec fwc-dec_right"></div>
                                <div class="fixed-column-wrap_dec"></div>
                            </div>
                            <!--fixed-column-wrap-content end-->
                        </div>
                        <!--fixed-column-wrap end-->
                        <div class="limit-box fl-wrap"></div>
                        <!-- contact form wrap -->
                        <div class="contact-form-wrap">
                            <div class="contact-form-overlay"></div>
                            <div class="contact-form-container">
                                <div class="contact-form-content fl-wrap white-bg">
                                    <div class="contact-form-header fl-wrap">
                                        <h4><?= __('contact_form_title') ?></h4>
                                        <span class="close-cf close-contact-form"><i class="fal fa-times"></i></span>
                                    </div>
                                    <div id="contact-form" class="fl-wrap">
                                        <div id="message" style="margin-bottom:15px; font-weight:600;"></div>
                                        <form class="custom-form" id="contactform" onsubmit="handleContactSubmit(event)">
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <input type="text" name="name" id="name" placeholder="<?= __('contact_name_ph') ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="email" name="email" id="email" placeholder="<?= __('contact_email_ph') ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <input type="tel" name="phone" id="phone" placeholder="<?= __('contact_phone_ph') ?>" required />
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="fl-wrap select-holder">
                                                            <select id="subject" name="subject">
                                                                <option value="Demande de devis" selected><?= i18n::isEn() ? 'Quote Request' : 'Demande de devis' ?></option>
                                                                <option value="Conseils fonciers"><?= i18n::isEn() ? 'Land Advisory' : 'Conseils fonciers' ?></option>
                                                                <option value="Urbanisme & Aménagement"><?= i18n::isEn() ? 'Urban Planning & Subdivision' : 'Urbanisme & Aménagement' ?></option>
                                                                <option value="Topographie & Bornage"><?= i18n::isEn() ? 'Surveying & Demarcation' : 'Topographie & Bornage' ?></option>
                                                                <option value="Achat / Vente de terrain"><?= i18n::isEn() ? 'Land Acquisition / Sale' : 'Achat / Vente de terrain' ?></option>
                                                                <option value="Prestation de services BTP"><?= i18n::isEn() ? 'Civil Works & BTP Services' : 'Prestation de services BTP' ?></option>
                                                                <option value="Autre demande"><?= i18n::isEn() ? 'Other inquiries' : 'Autre demande' ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="question">
                                                    <textarea name="comments" id="comments" cols="40" rows="3" placeholder="<?= __('contact_msg_ph') ?>" required></textarea>
                                                </div>
                                                <button type="submit" class="btn fl-btn" id="submit-btn" style="background:#008751; color:#fff; font-weight:700;"><span><?= __('btn_send_message') ?></span></button>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--contact form wrap end --> 
                    </div>
                    <!-- content end -->

                    <script>
                        function handleContactSubmit(e) {
                            e.preventDefault();
                            var btn = document.getElementById('submit-btn');
                            var msgBox = document.getElementById('message');
                            
                            btn.disabled = true;
                            btn.querySelector('span').innerText = '<?= __('btn_send') ?>...';

                            $.ajax({
                                type: 'POST',
                                url: 'index.php?page=contact',
                                data: {
                                    name: document.getElementById('name').value,
                                    email: document.getElementById('email').value,
                                    phone: document.getElementById('phone').value,
                                    subject: document.getElementById('subject').value,
                                    message: document.getElementById('comments').value
                                },
                                dataType: 'json',
                                success: function(res) {
                                    btn.disabled = false;
                                    btn.querySelector('span').innerText = '<?= __('btn_send_message') ?>';
                                    if (res.status === 'success') {
                                        msgBox.style.color = '#008751';
                                        msgBox.innerHTML = '<div style="background:#dcfce7; padding:12px; border-radius:6px; margin-bottom:12px;">' + res.message + '</div>';
                                        document.getElementById('contactform').reset();
                                    } else {
                                        msgBox.style.color = '#b91c1c';
                                        msgBox.innerHTML = '<div style="background:#fee2e2; padding:12px; border-radius:6px; margin-bottom:12px;">' + res.message + '</div>';
                                    }
                                },
                                error: function() {
                                    btn.disabled = false;
                                    btn.querySelector('span').innerText = '<?= __('btn_send_message') ?>';
                                    msgBox.style.color = '#b91c1c';
                                    msgBox.innerHTML = '<div style="background:#fee2e2; padding:12px; border-radius:6px; margin-bottom:12px;">Error. Please try again or reach us by phone.</div>';
                                }
                            });
                        }
                    </script>
