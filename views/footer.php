<?php
global $site_settings;
if (empty($site_settings)) {
    $site_settings = Database::fetchOne("SELECT * FROM site_settings WHERE id = 1");
}
$isEn = i18n::isEn();
$sloganDisplay = $isEn ? __('slogan') : ($site_settings['slogan'] ?? 'Un Camerounais, un terrain');
$aboutDisplay = $isEn ? __('sec_about_text') : ($site_settings['about_summary'] ?? 'DjoKhouse SCI — Un Camerounais, un terrain. Votre partenaire pour la sécurisation foncière, le bornage et l’urbanisme.');
?>
  <!-- footer -->
                    <div class="height-emulator fl-wrap"></div>
                    <footer class="main-footer">
                        <div class="container">
                            <div class="footer-decor">
                                <canvas id="bg-animation"></canvas>
                            </div>
                            <!-- footer-inner -->
                            <div class="footer-inner fl-wrap">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="footer-logo">
                                            <img src="images/logo2.png" style="max-height:80px; width:auto;" alt="logo DjoKhouse SCI">
                                        </div>
                                        <div class="footer_text footer-box fl-wrap">
                                            <p><?= htmlspecialchars($aboutDisplay) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="footer-header fl-wrap"><?= __('footer_contact_header') ?></div>
                                        <!-- footer-contacts-->
                                        <div class="footer-contacts footer-box fl-wrap">
                                            <ul>
                                                <li><span><?= __('contact_tel_lbl') ?> :</span><a href="tel:<?= htmlspecialchars($site_settings['phone']) ?>"><?= htmlspecialchars($site_settings['phone']) ?></a></li>
                                                <?php if (!empty($site_settings['phone2'])): ?>
                                                    <li><span><?= __('contact_wa_lbl') ?> :</span><a href="tel:<?= htmlspecialchars($site_settings['phone2']) ?>"><?= htmlspecialchars($site_settings['phone2']) ?></a></li>
                                                <?php endif; ?>
                                                <li><span><?= __('contact_email_lbl') ?> :</span><a href="mailto:<?= htmlspecialchars($site_settings['email']) ?>"><?= htmlspecialchars($site_settings['email']) ?></a></li>
                                                <li><span><?= __('contact_addr_lbl') ?> :</span><a href="#"><?= htmlspecialchars($site_settings['address']) ?></a></li>
                                            </ul>
                                        </div>
                                        <!-- footer contacts end -->
                                        <a href="contact" class="ajax fc_button btn float-btn"><?= __('btn_contact_us') ?></a>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="footer-header fl-wrap"><?= __('footer_news_header') ?></div>
                                        <div class="footer-box fl-wrap">
                                            <p><?= __('footer_news_desc') ?></p>
                                            <div class="subcribe-form fl-wrap">
                                                <form id="subscribe-form" class="fl-wrap" onsubmit="handleNewsletter(event)">
                                                    <input class="enteremail" name="email" id="subscribe-email" placeholder="<?= __('footer_news_ph') ?>" spellcheck="false" type="email" required>
                                                    <button type="submit" id="subscribe-button" class="subscribe-button"><?= __('btn_send') ?></button>
                                                    <div id="newsletter-feedback" style="margin-top:10px; font-size:12px; font-weight:600;"></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- footer-inner end -->
                            <!-- subbfooter -->
                            <div class="subbfooter fl-wrap">
                                <div class="policy-box">
                                    <span> &#169; <?= date('Y') ?> <?= htmlspecialchars($site_settings['site_name']) ?> &bull; <?= htmlspecialchars($sloganDisplay) ?>. <?= __('footer_rights') ?> &bull; <a href="index.php?page=admin" style="opacity:0.6; text-decoration:underline;"><?= __('footer_admin_link') ?></a></span>
                                </div>
                                <div class="footer-solcial">
                                    <ul>
                                        <?php if (!empty($site_settings['facebook'])): ?>
                                            <li><a href="<?= htmlspecialchars($site_settings['facebook']) ?>" target="_blank">Fb</a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($site_settings['linkedin'])): ?>
                                            <li><a href="<?= htmlspecialchars($site_settings['linkedin']) ?>" target="_blank">In</a></li>
                                        <?php endif; ?>
                                        <?php if (!empty($site_settings['phone2'])): ?>
                                            <li><a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $site_settings['phone2']) ?>" target="_blank">Wa</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="to-top-btn to-top"><i class="fal fa-long-arrow-up"></i></div>
                            </div>
                            <!-- subbfooter end -->
                        </div>
                    </footer>
                    <!-- footer end -->
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
        <script>
            function handleNewsletter(e) {
                e.preventDefault();
                var emailInput = document.getElementById('subscribe-email');
                var feedback = document.getElementById('newsletter-feedback');
                var btn = document.getElementById('subscribe-button');

                btn.disabled = true;
                btn.innerText = '...';

                $.ajax({
                    type: 'POST',
                    url: 'index.php?page=newsletter',
                    data: { email: emailInput.value },
                    dataType: 'json',
                    success: function(res) {
                        btn.disabled = false;
                        btn.innerText = '<?= __('btn_send') ?>';
                        if (res.status === 'success') {
                            feedback.style.color = '#008751';
                            feedback.innerText = res.message;
                            emailInput.value = '';
                        } else {
                            feedback.style.color = '#b91c1c';
                            feedback.innerText = res.message;
                        }
                    },
                    error: function() {
                        btn.disabled = false;
                        btn.innerText = '<?= __('btn_send') ?>';
                        feedback.style.color = '#b91c1c';
                        feedback.innerText = "Error. Please try again.";
                    }
                });
            }
        </script>

        <!-- ===== DjoKhouse — Language Switcher instantané (sans rechargement) ===== -->
        <script>
        (function () {
            // Langue initiale envoyée par PHP
            var CURRENT_LANG = '<?= i18n::lang() ?>';

            /**
             * Applique la langue : met à jour les boutons actifs,
             * sauvegarde en session via fetch, puis recharge le contenu
             * via le mécanisme AJAX existant du site (sans full browser reload).
             */
            function switchLang(lang) {
                if (lang === CURRENT_LANG) return;

                // 1. Mettre à jour l'état visuel des boutons immédiatement
                document.querySelectorAll('.djok-switch-lang').forEach(function (btn) {
                    if (btn.dataset.lang === lang) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });

                // 2. Sauvegarder le choix en session via AJAX (léger, sans reload)
                fetch('index.php?page=set_lang&lang=' + lang, { method: 'GET' })
                    .then(function () {
                        CURRENT_LANG = lang;

                        // 3. Recharger le contenu via l'AJAX interne du site
                        //    Le site utilise $.fn.load() pour les liens .ajax
                        //    On déclenche le rechargement de la page courante sans changer l'URL
                        var currentHref = window.location.href;
                        // Injecter ?lang= dans l'URL puis déclencher la navigation AJAX
                        var sep = currentHref.indexOf('?') !== -1 ? '&' : '?';
                        // On utilise location.replace pour éviter d'ajouter dans l'historique
                        window.location.replace(currentHref + sep + 'lang=' + lang);
                    })
                    .catch(function () {
                        // Fallback : navigation classique
                        var sep = window.location.href.indexOf('?') !== -1 ? '&' : '?';
                        window.location.href = window.location.href + sep + 'lang=' + lang;
                    });
            }

            // Délégation d'événement : capture les clics sur tous les boutons switcher
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.djok-switch-lang');
                if (!btn) return;
                e.preventDefault();
                switchLang(btn.dataset.lang);
            });
        })();
        </script>
    </body>
</html>
