<?php
//include "functions.inc.php";
date_default_timezone_set('America/Vancouver');
$vancouver_time = date('Y-m-d H:i:s T');
date_default_timezone_set('America/Toronto');
$toronto_time = date('Y-m-d H:i:s T');
date_default_timezone_set('America/Vancouver');
//$channels = get_channel_totals();
$channels['total_channels'] = 200;
?>
<p>Server Time: <?php echo $vancouver_time; ?></p>
<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <?php echo $toronto_time; ?></p>
<p>Channel Status: <?php echo $channels['total_channels']."/200"; ?></p>