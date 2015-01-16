<?php
	define ("DB_HOST", "localhost");
	define ("DB_USER", "ivr_gui");
	define ("DB_PASSWORD", "snow1in1the1summer");
	define ("DB_ASTERISK", "asterisk");


	function dbc_asterisk() {
		$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if (!$con)
			die('Could not connect: ' . mysql_error());
		if( !mysql_select_db(DB_ASTERISK, $con) )
			showerror();
		return $con;
	}

	require_once "/var/lib/asterisk/agi-bin/phpagi-asmanager.php";
	require_once "/var/lib/asterisk/agi-bin/phpagi.php";
	$astman = new AGI_AsteriskManager();
	if (!$astman->connect("127.0.0.1", "admin" , "ypUho5GEF8u6AZa")) {
		exit (1);
	}

	$amp_conf = parse_amportal_conf_bootstrap("/etc/amportal.conf");
	$arr = engine_getinfo();
    $version = $arr['version'];
	
	function engine_getinfo() {
		global $amp_conf;
		global $astman;

		switch ($amp_conf['AMPENGINE']) {
			case 'asterisk':
				if (isset($astman)) {
					//get version (1.4)
					$response = $astman->send_request('Command', array('Command'=>'core show version'));
					if (preg_match('/No such command/',$response['data'])) {
						// get version (1.2)
						$response = $astman->send_request('Command', array('Command'=>'show version'));
					}
					$verinfo = $response['data'];
				} else {
					// could not connect to asterisk manager, try console
					$verinfo = exec('asterisk -V');
				}
			
				if (preg_match('/Asterisk (\d+(\.\d+)*)(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => $matches[1], 'additional' => $matches[4], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk SVN-(\d+(\.\d+)*)(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => $matches[1], 'additional' => $matches[4], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk SVN-branch-(\d+(\.\d+)*)-r(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => $matches[1].'.'.$matches[4], 'additional' => $matches[4], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk SVN-trunk-r(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => '1.6', 'additional' => $matches[1], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk SVN-.+-(\d+(\.\d+)*)-r(-?(\S*))-(.+)/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => $matches[1], 'additional' => $matches[3], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk [B].(\d+(\.\d+)*)(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => '1.2', 'additional' => $matches[3], 'raw' => $verinfo);
				} elseif (preg_match('/Asterisk [C].(\d+(\.\d+)*)(-?(\S*))/', $verinfo, $matches)) {
					return array('engine'=>'asterisk', 'version' => '1.4', 'additional' => $matches[3], 'raw' => $verinfo);
				}

				return array('engine'=>'ERROR-UNABLE-TO-PARSE', 'version'=>'0', 'additional' => '0', 'raw' => $verinfo);
			break;
		}
		return array('engine'=>'ERROR-UNSUPPORTED-ENGINE-'.$amp_conf['AMPENGINE'], 'version'=>'0', 'additional' => '0', 'raw' => $verinfo);
	}

	function parse_amportal_conf_bootstrap($filename) {
		$file = file($filename);
		foreach ($file as $line) {
			if (preg_match("/^\s*([\w]+)\s*=\s*\"?([\w\/\:\.\*\%-]*)\"?\s*([;#].*)?/",$line,$matches)) {
				$conf[ $matches[1] ] = $matches[2];
			}
		}
		if ( !isset($conf["AMPWEBROOT"]) || ($conf["AMPWEBROOT"] == "")) {
			$conf["AMPWEBROOT"] = "/var/www/html";
		} else {
			$conf["AMPWEBROOT"] = rtrim($conf["AMPWEBROOT"],'/');
		}
		if (!isset($conf['ASTAGIDIR']) || $conf['ASTAGIDIR'] == '') {
			$conf['ASTAGIDIR'] = '/var/lib/asterisk/agi-bin';
		}
		if (!isset($conf['ZAP2DAHDICOMPAT'])) {
			$conf['ZAP2DAHDICOMPAT'] = false;
		} else {
			switch (strtoupper(trim($conf['ZAP2DAHDICOMPAT']))) {
				case '1':
				case 'TRUE':
				case 'ON':
					$conf['ZAP2DAHDICOMPAT'] = true;
					break;
				default:
					$conf['ZAP2DAHDICOMPAT'] = false;
			}
		}

		return $conf;
	}

	// START
	// functions getting freepbx info
	function get_channel_totals() {
		global $amp_conf;
		global $astman;
		if (!$astman) {
			return array(
				'external_calls'=>0,
				'internal_calls'=>0,
				'total_calls'=>0,
				'total_channels'=>0,
			);
		}
		if (version_compare($version, "1.6", "ge")) {
			$response = $astman->send_request('Command',array('Command'=>"core show channels"));
		} else {
			$response = $astman->send_request('Command',array('Command'=>"show channels"));
		}
		$astout = explode("\n",$response['data']);
		
		$external_calls = 0;
		$internal_calls = 0;
		$total_calls = 0;
		$total_channels = 0;
		
		foreach ($astout as $line) {
			if (preg_match('/s@macro-dialout/', $line)) {
				$external_calls++;
			} else if (preg_match('/s@macro-dial:/', $line)) {
				$internal_calls++;
			} else if (preg_match('/^(\d+) active channel/i', $line, $matches)) {
				$total_channels = $matches[1];
			} else if (preg_match('/^(\d+) active call/i', $line, $matches)) {
				$total_calls = $matches[1];
			}
		}
		return array(
			'external_calls'=>$external_calls,
			'internal_calls'=>$internal_calls,
			'total_calls'=>$total_calls,
			'total_channels'=>$total_channels,
		);
	}

	function get_max_calls() {
		if (!isset($_SESSION["calculated_max_calls"])) {
		// set max calls to either MAXCALLS in amportal.conf, or the number of users in the system
			if (isset($amp_conf['MAXCALLS'])) {
				$_SESSION["calculated_max_calls"] = $amp_conf["MAXCALLS"];
			} else if (function_exists('core_users_list')) {
				$_SESSION["calculated_max_calls"] = core_users_list();
			} else {
				$_SESSION["calculated_max_calls"] = 1;
			}
		}

		$channel = get_channel_totals();
		// we currently see more calls than we guessed, increase it
		if ($channel['total_calls'] > $_SESSION["calculated_max_calls"]) {
			$_SESSION["calculated_max_calls"] = $channel['total_calls'];
		}
		return $_SESSION["calculated_max_calls"];
	}


	function core_users_list() {
		$con = dbc_asterisk();
		$result = mysql_query("SELECT COUNT(*) FROM users");
		$row = mysql_fetch_assoc($result);
		mysql_close($con);
		return $row['COUNT(*)'];
	}

	// END
	// functions getting freepbx info

	// START
	// functions getting server info
	function show_procinfo() {
		global $amp_conf;

		//$asterisk = check_asterisk();
		
		if ($astver = check_asterisk()) {
			$asterisk = 'OK';
		} else {
			$asterisk = 'Error';
		}
		
		

		// fop
		if(!$amp_conf['FOPDISABLE'])  {
			if (check_fop_server()) {
				$op_panel = 'OK';
			} else {
				if ($amp_conf['FOPRUN']) {
					// it should be running
					$op_panel = 'Warning';
				} else {
					$op_panel = 'Disabled';
				}
			}
		}
		
		// mysql
		if ($amp_conf['AMPDBENGINE'] == "mysql") {
			$mysql = 'OK';
		}
		
		// web always runs .. HOWEVER, we can turn it off with dhtml
		$web_server = 'OK';
		
		// ssh	
		$ssh_port = (isset($amp_conf['SSHPORT']) && ctype_digit($amp_conf['SSHPORT']) && ($amp_conf['SSHPORT'] > 0) && ($amp_conf['SSHPORT'] < 65536))?$amp_conf['SSHPORT']:22;
		if (check_port($ssh_port)) {
			$ssh_server = 'OK';
		} else {
			$ssh_server = 'Disabled';
		}
		return array(
			'asterisk'=>$asterisk,
			'op_panel'=>$op_panel,
			'mysql'=>$mysql,
			'web_server'=>$web_server,
			'ssh_server'=>$ssh_server,
		);
	}

	function check_port($port, $server = "localhost") {
		$timeout = 5;
		if ($sock = @fsockopen($server, $port, $errno, $errstr, $timeout)) {
			fclose($sock);
			return true;
		}
		return false;
	}
		
	function check_fop_server() {
		global $amp_conf;
		$fop_settings = parse_ini_file($amp_conf['FOPWEBROOT'].'/op_server.cfg');
		if (is_array($fop_settings)) {
		  $listen_port = isset($fop_settings['listen_port']) && trim($fop_settings['listen_port']) != ''?$fop_settings['listen_port']:4445;
		} else {
		  $listen_port = 4445;
		}
		return check_port($listen_port);
	}
		
	function check_mysql($hoststr) {
		$host = 'localhost';
		$port = '3306';
		if (preg_match('/^([^:]+)(:(\d+))?$/',$hoststr,$matches)) {
			// matches[1] = host, [3] = port
			$host = $matches[1];
			if (!empty($matches[3])) {
				$port = $matches[3];
			}
		}
		return check_port($port, $host);
	}

	function check_asterisk() {
		global $astman;
		if (!isset($astman)) {
			return false;
		}
		if (version_compare($version, "1.6", "ge")) {
			$response = $astman->send_request('Command',array('Command'=>"core show version"));
		} else {
			$response = $astman->send_request('Command',array('Command'=>"show version"));
		}
		$astout = explode("\n",$response['data']);
		
		if (!preg_match('/^Asterisk /i', $astout[1])) {
			return false;
		} else {
			return $astout[1];
		}
	}
	// END
	// functions getting server info
?>