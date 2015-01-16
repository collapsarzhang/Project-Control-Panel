<!-- Header -->
<div id="header">
	<div class="shell">
		<!-- Logo + Top Nav -->
		<div id="top">
			<h1>Stratcom Simple IVR</h1>
			<div id="top-navigation">
		<?php
		if (isset($_SESSION['user_ivr'])) {
		?>
				Welcome <strong><?php echo $consultants[$_SESSION['user_ivr']]['name'];;?></strong>
				<span>|</span>
				<a href="#">Help</a>
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
			    <li><a href="./?page=home" <?php if (!isset($_REQUEST['page']) || (isset($_GET['page']) && $_GET['page']=='home')) echo 'class="active"'; ?>><span>Home</span></a></li>
			</ul>
		</div>
		<!-- End Main Nav -->
	</div>
</div>
<!-- End Header -->


