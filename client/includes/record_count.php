<?php
//include "functions.inc.php";
date_default_timezone_set('America/Vancouver');
$vancouver_time = date('Y-m-d H:i:s T');
date_default_timezone_set('America/Toronto');
$toronto_time = date('Y-m-d H:i:s T');
date_default_timezone_set('America/Vancouver');
//$channels = get_channel_totals();
?>
<p>Server Time: </p>
<p><?php echo $vancouver_time; ?></p>
<p><?php echo $toronto_time; ?></p>
