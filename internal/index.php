<?php
require "includes/defines.inc.php";
session_start();

date_default_timezone_set("America/Vancouver");

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $keep_alive)) {
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

if (isset($_GET['page']) && $_GET['page']=='logout') {
	session_unset();     // unset $_SESSION variable for the run-time 
	session_destroy();
}

if (isset($_POST['user_id']) && isset($_POST['pass'])) {
	$match = false;
	if (isset($consultants[$_POST['user_id']]) && $consultants[$_POST['user_id']]['password'] == $_POST['pass']) {
		$_SESSION['user_ivr'] = $_POST['user_id'];
		$open = fopen("logs/".date("Y_m_d").".log","a+");
		$text = $_SESSION['user_ivr']." has logged in at ".date("F jS, Y @G:i")."\r\n";
		fwrite($open, $text);
		fclose($open);
		$match = true;
		$con = dbc_ivr();
		$r = mysql_query("INSERT INTO log (userid, action) VALUES ('".$_POST['user_id']."', 'Successful Login')");
		mysql_close($con);
	}
}

include "includes/header.php";

if (isset($match) && !$match) {
	echo '<div id="container"><div class="shell"><div class="msg msg-error">
			<p><strong>Login Failed</strong></p>
		</div></div></div>';
	if (!isset($_SESSION['attempt'])) {
		$_SESSION['attempt']=0;
	}
	$_SESSION['attempt']++;
	if ($_SESSION['attempt'] > $allowed_login_attempts) {
		mail('tm.tech@stratcom.ca','Stratcom Simple IVR Login Fail Attempt, '.checkip().' attempted to login at least 3 times and failed','',$email_headers);
	}
}


if (!isset($_SESSION['user_ivr'])) {
	include "login.php";
}
else {
	if (!isset($_REQUEST['page']) || (isset($_GET['page']) && $_GET['page']=='home'))
		include "view_projects.php";
	else if (isset($_GET['page']) && $_GET['page']=='edit_project')
		include "edit_project.php";
	else if (isset($_GET['page']) && $_GET['page']=='add_project')
		include "add_project.php";
	else if (isset($_GET['page']) && $_GET['page']=='project_action')
		include "project_action.php";
	else if (isset($_GET['page']) && $_GET['page']=='edit_config')
		include "edit_config.php";
	else if (isset($_GET['page']) && $_GET['page']=='clone_project')
		include "clone_project.php";
	else if (isset($_POST['page']) && $_POST['page']=='edit_project_post')
		include "edit_project_post.php";
	else if (isset($_POST['page']) && $_POST['page']=='add_project_post')
		include "add_project_post.php";
	else if (isset($_POST['page']) && $_POST['page']=='import_numbers')
		include "import_numbers.php";
	else if (isset($_POST['page']) && $_POST['page']=='edit_config_post')
		include "edit_config_post.php";
	else if (isset($_POST['page']) && $_POST['page']=='search_result')
		include "search_result.php";
	else if (isset($_POST['page']) && $_POST['page']=='upload_wav')
		include "upload_wav.php";
	else if (isset($_POST['page']) && $_POST['page']=='upload_pollings')
		include "upload_pollings.php";
	else if (isset($_GET['page']) && $_GET['page']=='number_analysis')
		include "number_analysis.php";
	else if (isset($_POST['page']) && $_POST['page']=='areacode_remove')
		include "areacode_remove.php";
	else if (isset($_POST['page']) && $_POST['page']=='timezone_remove')
		include "timezone_remove.php";
	else if (isset($_POST['page']) && $_POST['page']=='areacode_addback')
		include "areacode_addback.php";
	else if (isset($_POST['page']) && $_POST['page']=='timezone_addback')
		include "timezone_addback.php";
	else if (isset($_GET['page']) && $_GET['page']=='login')
		include "login.php";
	else
		echo '<div id="container"><div class="shell"><div class="msg msg-error">
			<p><strong>Access Denied</strong></p>
		</div></div></div>';
}


include "includes/footer.php";
?>

<?php
function checkip() {
	if (!empty($_SERVER["HTTP_CLIENT_IP"]))	{
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	return $ip;
}
?>