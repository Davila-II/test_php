
    <!-- Content -->
    <div class="page-content bg-white">
		<!-- inner page banner -->
        <div class="dlab-bnr-inr dlab-bnr-inr-sm overlay-black-middle bg-pt" style="background-image:url(images/banner/bnr2.jpg);">
            <div class="container">
                <div class="dlab-bnr-inr-entry">
                    <h1 class="text-white">Demandez un devis</h1>
					<!-- Breadcrumb row -->
					
					<!-- Breadcrumb row END -->
                </div>
            </div>
        </div>
        <!-- inner page banner END -->
        <div class="content-block">
            <!-- About Me -->
			
			
					<div class="section-head text-center">
						<h2 class="m-b5">Formulaire devis</h2>
						
					</div>
					<div class="dzFormMsg"></div>
					<form method="post" class="dzForm" action="https://intera.dexignlab.com/xhtml/script/contact.php">
						<input type="hidden" value="Contact" name="dzToDo">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-12">
								<div class="form-group">
									<input name="dzName" type="text" required class="form-control" placeholder="Enter your name">
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12">
								<div class="form-group">
									<input name="dzEmail" type="email" class="form-control" required  placeholder="Your Email Address" >
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12">
								<div class="form-group">
									<input name="dzOther[Phone]" type="text" required class="form-control" placeholder="Phone">
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<textarea name="dzMessage" rows="4" class="form-control" required placeholder="Your message here"></textarea>
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<div class="g-recaptcha" data-sitekey="6LefsVUUAAAAADBPsLZzsNnETChealv6PYGzv3ZN" data-callback="verifyRecaptchaCallback" data-expired-callback="expiredRecaptchaCallback"></div>
									<input class="form-control d-none" style="display:none;" data-recaptcha="true" required data-error="Please complete the Captcha">
								</div>
							</div>
							<div class="col-md-12 col-sm-12 text-center">
								<button name="submit" type="submit" value="Submit" class="btn radius-xl btn-lg outline outline-2 black btn-aware">Envoyez<span></span></button>
							</div>
						</div>
					</form>
				</div>
			</div>
            <!-- About Me End -->
        </div>
		<!-- contact area END -->
    </div>
    <!-- Content END-->
   
	<!-- Footer -->
   