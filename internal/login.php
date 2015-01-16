

<!-- Container -->
<div id="container">
	<div class="shell">

		<!-- Main -->
		<div id="main">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content">


				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2>Login</h2>
					</div>
					<!-- End Box Head -->


					<form name="input" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
					
						<!-- Form -->
						<div class="form">
						
							<p>
								<label>Username <span>(Required Field)</span></label>
								<input type="text" name="user_id" class="field size1" placeholder="firstname.lastname" />
							</p>

							<p>
								<label>Password <span>(Required Field)</span></label>
								<input type="password" autocomplete="off" name="pass" class="field size1" />
							</p>

						</div>
						<!-- End Form -->

						<!-- Form Buttons -->
						<div class="buttons">
							<input type="submit" class="button" value="submit" />
						</div>
						<!-- End Form Buttons -->
					</form>

				</div>
				<!-- End Box -->

			</div>
			<!-- End Content -->

			<div class="cl">&nbsp;</div>			
		</div>
		<!-- Main -->
	</div>
</div>
<!-- End Container -->