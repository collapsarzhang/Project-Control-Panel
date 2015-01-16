<?php
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "includes/redirect.php";
	safe_redirect("index.php");
}
?>

<!-- Container -->
<div id="container">
	<div class="shell">
		
		<!-- Main -->
		<div id="main">
			<div class="cl">&nbsp;</div>
			
			<!-- Content -->
			<div id="content">
				<?php
				if (isset($_GET['message'])) {
					echo $_GET['message'];
				}
				?>
				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Import DNC List</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="page" value="upload_dnc_post">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Do Not Call List</td>
								<td align="left" style="width:40%"><input type="file" name="dncfile" class="field size4"></td>
								<td align="left" style="width:40%">Make sure the file is in CSV format, phone number in first column.</td>
							</tr>
							<tr>
								<td align="left" style="width:20%">Incremental Mode?</td>
								<td align="left" style="width:40%">
									<div class="onoffswitch">
										<input type="checkbox" name="incremental" class="onoffswitch-checkbox" id="incremental">
										<label class="onoffswitch-label" for="incremental">
											<div class="onoffswitch-inner"></div>
											<div class="onoffswitch-switch"></div>
										</label>
									</div>
								</td>
								<td align="left" style="width:40%">Incremental Mode will add the list to existing DNC list.</td>
							<tr>
							<?php
							if (file_exists('dnc/DNC.csv')) {
								$fp = file('dnc/DNC.csv', FILE_SKIP_EMPTY_LINES);
							?>
							<tr>
								<td align="left" colspan="2">Global DNC list contains <?php echo count($fp); ?> records, and was last modified on <?php echo date ("F d, Y H:i", filemtime('dnc/DNC.csv')); ?></td>
								<td align="left"><a href="dnc/dnc_download.php">Click Here to Download DNC list</a></td>
							</tr>
							<?php
							}
							?>
						</table>


						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Upload DNC" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->


				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Add a DNC Number</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="add_dnc_post">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Do Not Call Number</td>
								<td align="left" style="width:40%"><input type="text" name="dnc_number" class="field size4"></td>
							</tr>
						</table>
						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Add DNC Number" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
					</form>
					
				</div>
				<!-- End Box -->

				<!-- Box -->
				<div class="box">
					<!-- Box Head -->
					<div class="box-head">
						<h2 class="left">Remove a DNC Number</h2>
					</div>
					<!-- End Box Head -->	
					
					<form name="input" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="hidden" name="page" value="remove_dnc_post">
					<!-- Table -->
					<div class="table">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" style="width:20%">Do Not Call Number</td>
								<td align="left" style="width:40%"><input type="text" name="dnc_number" class="field size4"></td>
							</tr>
						</table>
						<!-- Pagging -->
						<div class="pagging">
							<div class="left"><input type="submit" class="button" value="Remove DNC Number" /></div>
						</div>
						<!-- End Pagging -->
						
					</div>
					<!-- Table -->
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
