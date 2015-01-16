<?php
session_start();
if (!isset($_SESSION['user_ivr_fndp'])) {
	require_once "../includes/redirect.php";
	safe_redirect("index.php");
}

$cfile = "DNC.csv";
$file = "Stratcom FNDP DNC list.csv";
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$file");
readfile($cfile);