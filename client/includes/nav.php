<!-- Header -->
<div id="header">
	<div class="shell">
		<!-- Logo + Top Nav -->
		<div id="top">
			<h1>Stratcom BVM client interface -- NDP</h1>
			<div id="top-navigation">
		<?php
		if (isset($_SESSION['user_ivr_fndp'])) {
		?>
				Welcome <strong><?php echo $consultants[$_SESSION['user_ivr_fndp']]['name'];;?></strong>
				<span>|</span>
				<a href="doc/term_of_use.pdf" target="_blank">Terms of Use</a>
				<span>|</span>
				<a href="./?page=logout">Logout</a>
		<?php
		} else {
		?>
				<a href="./?page=login">login</a>
		<?php
		}
		?>
			</div>
		</div>
		<!-- End Logo + Top Nav -->
		
		<!-- Main Nav -->
		<div id="navigation">
			<ul>
			    <li><a href="./?page=home" <?php if (!isset($_REQUEST['page']) || (isset($_GET['page']) && $_GET['page']=='home')) echo 'class="active"'; ?>><span>View All Projects</span></a></li>
				<li><a href="./?page=upload_dnc" <?php if ((isset($_GET['page']) && $_GET['page']=='upload_dnc')) echo 'class="active"'; ?>><span>Manage Global DNC List</span></a></li>
				<?php if ((isset($_POST['page']) && $_POST['page']=='edit_project')) { ?><li><a class="active"><span>Project Panel (from Home)</span></a></li><?php } ?>
				<?php if ((isset($_POST['page']) && $_POST['page']=='number_analysis')) { ?><li><a class="active"><span>Number Analysis (from Panel)</span></a></li><?php } ?>
			</ul>
		</div>
		<!-- End Main Nav -->
	</div>
</div>
<!-- End Header -->


