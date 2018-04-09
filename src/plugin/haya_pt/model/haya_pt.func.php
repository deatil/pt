<?php

function haya_pt_plugin_dir($plugin_name = 'haya_pt') {
	$plugin_dir = APP_PATH . 'plugin/';
	
	if (empty($plugin_name)) {
		return $plugin_dir;
	}
	
	return $plugin_dir . $plugin_name . '/';
}

function haya_pt_get_plugin_dir($plugin_name = 'haya_pt') {
	$plugin_dir = APP_PATH . 'plugin/';
	
	if (empty($plugin_name)) {
		return $plugin_dir;
	}
	
	return $plugin_dir . $plugin_name;
}

function haya_pt_get_plugin_config($file = '') {
	if (empty($file)) {
		return false;
	}
	
	$haya_pt_config_dir = APP_PATH . 'plugin/haya_pt/config/';
	$haya_pt_config_file = $haya_pt_config_dir.$file.".php";
	if (!file_exists($haya_pt_config_file)) {
		return false;
	}
	
	return $haya_pt_config_file;
}

function haya_pt_plugin_action($action = '') {
	$action_dir = haya_pt_plugin_dir('haya_pt') . 'route/pt/';
	
	return $action_dir . $action;
}

function haya_pt_plugin_admin_action($action = '') {
	$action_dir = haya_pt_plugin_dir('haya_pt') . 'route/admin/';
	
	return $action_dir . $action;
}

function haya_pt_plugin_view($htm = '') {
	$view_dir = haya_pt_plugin_dir('haya_pt') . 'view/';
	
	return $view_dir . $htm;
}

function haya_pt_plugin_torrent($torrent = '') {
	$torrent_dir = haya_pt_plugin_dir('haya_pt') . 'torrent/';
	
	return $torrent_dir . $torrent;
}

function haya_pt_plugin_view_url($htm = '') {
	$view_dir = 'plugin/haya_pt/view/';
	
	return $view_dir . $htm;
}

function haya_pt_get_web_root() {
	$php_file = rtrim($_SERVER['SCRIPT_NAME'], '/');
	
	$_root = rtrim(dirname($php_file), '/');
	$web_root = ($_root == '/' || $_root == '\\') ? '' : $_root;

	return $web_root;
} 

function haya_pt_get_web_torrent_url() {
	$haya_pt_admin_url =  "http://".$_SERVER['HTTP_HOST'].haya_pt_get_web_root(); 
	$haya_pt_torrent_url = dirname($haya_pt_admin_url).url('/pt-announce', array('pid' => '__pid__'));
	return $haya_pt_torrent_url;
} 

function haya_pt_delete_torrent($tidarr) {
	
	$haya_pt_torrent_files = haya_pt_files__find(array(
		'tid' => $tidarr
	), array(
		'tid' => -1
	), 1, count($tidarr));
	
	if (!empty($haya_pt_torrent_files)) {
		
		foreach ($haya_pt_torrent_files as $_haya_pt_torrent) {
			$TORRENTSDIR = haya_pt_plugin_torrent();
			$filepath = $TORRENTSDIR . $_haya_pt_torrent['url'];
			
			@unlink($filepath); // 删除种子文件
			
			$_haya_pt_tid = $_haya_pt_torrent['tid'];
		
			haya_pt_files_delete_by_tid($_haya_pt_tid);
			haya_pt_peers_delete_by_tid($_haya_pt_tid);
		}
		
	}	
	
}

function haya_pt_delete_torrent_by_tids($tids) {
	
	$haya_pt_torrent_files_count = haya_pt_files__count(array('tid' => $tids));
	$haya_pt_torrent_files = haya_pt_files__find(array(
		'tid' => $tids
	), array(
		'tid' => -1
	), 1, $haya_pt_torrent_files_count);
	
	if (!empty($haya_pt_torrent_files)) {
		
		foreach ($haya_pt_torrent_files as $_haya_pt_torrent) {
			$TORRENTSDIR = haya_pt_plugin_torrent();
			$filepath = $TORRENTSDIR . $_haya_pt_torrent['url'];
			
			@unlink($filepath); // 删除种子文件
			
			$_haya_pt_tid = $_haya_pt_torrent['tid'];
		
			haya_pt_files_delete_by_tid($_haya_pt_tid);
			haya_pt_peers_delete_by_tid($_haya_pt_tid);
		}
		
	}	
	
}

// 增强函数

function haya_pt_sizecount($filesize) {
	$type = false;
	if ($filesize < 0) {
		$type = true;
		$filesize = abs($filesize);
	}
	
	if ($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} elseif ($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' Bytes';
	}
	
	if ($type) {
		$filesize = '-'.$filesize;
	}
	
	return $filesize;
}

function haya_pt_substr($s, $len = 20, $htmlspe = TRUE, $extra = '...') {

   	if ($htmlspe == FALSE) {
   		$s = strip_tags($s);
		$s = htmlspecialchars($s);
   	}
	
   	$more = xn_strlen($s) > $len ? $extra : '';
	$s = xn_substr($s, 0, $len).$more;

	return $s;
} 

function haya_pt_humandate($timestamp, $lan = array()) {
	$time = $_SERVER['time'];
	$lang = $_SERVER['lang'];
	
	static $custom_humandate = NULL;
	if ($custom_humandate === NULL) {
		$custom_humandate = function_exists('custom_humandate');
	}
	if ($custom_humandate) {
		return custom_humandate($timestamp, $lan);
	}
	
	$seconds = $time - $timestamp;
	
	if (empty($lan)) {
		$lan = $lang;
	}
	$haya_lan = array(
		'yesterday' => '昨天',
		'today' => '今天',
		'hour_ago' => '小时前',
		'minute_ago' => '分钟前',
		'second_ago' => '秒前',
	);
	$lan = array_merge($haya_lan, $lan);
	
	if ($seconds > 43200) {
		if (date('Y-m-d', $timestamp) == date('Y-m-d')) {
			return $lan['today'].date('H:i:s', $timestamp);
		} elseif (date('Y-m-d', $timestamp) == date('Y-m-d', strtotime("-1 day"))) {
			return $lan['yesterday'].date('H:i:s', $timestamp);
		} elseif (date('Y', $timestamp) == date('Y')) {
			return date('m-d H:i:s', $timestamp);
		} else {
			return date('Y-m-d H:i:s', $timestamp);
		}
	} elseif($seconds > 3600) {
		return floor($seconds / 3600).$lan['hour_ago'];
	} elseif($seconds > 60) {
		return floor($seconds / 60).$lan['minute_ago'];
	} else {
		return $seconds.$lan['second_ago'];
	}
}

function haya_pt_format_date($date) {
	$days = round($date / 43200);
	$daya_hours = $date % 43200;
	
	$hours = round($daya_hours / 3600);
	$hours_minutes = $daya_hours % 3600;
	
	$minutes = round($hours_minutes / 60);
	$minutes_seconds = $hours_minutes % 60;
	
	$seconds = round($minutes_seconds / 1);
	
	$time = '';
	if ($days > 1000) {
		$days = ($days / 1000) . 'k';
		$time .= $days . '天';
	} elseif ($days > 0) {
		$time .= $days . '天';
	}
	
	if ($hours > 0) {
		$time .= $hours . '小时';
	}
	
	if ($minutes > 0) {
		$time .= $minutes . '分';
	}
	
	if ($seconds > 0) {
		$time .= $seconds . '秒';
	}
	
	return $time;
}

// literally from the ABNF in rfc3986 (thanks to 'WCP')
function haya_pt_validate_ipv6($ip) {
	return preg_match('/\A
	(?:
		(?:
			(?:[a-f0-9]{1,4}:){6}
			|
			::(?:[a-f0-9]{1,4}:){5}
			|
			(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}
			|
			(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}
			|
			(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}
			|
			(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:
			|
			(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
		)
		
		(?:
			[a-f0-9]{1,4}:[a-f0-9]{1,4}
			|
			(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
			(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
		)
		
		|
		(?:
			(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}
			|
			(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
		)
	)\Z/ix', $ip);

}

// 权限检测

function haya_pt_check_upload_forum($fid) {
	$haya_pt_config = kv_get('haya_pt');
	
	if (isset($haya_pt_config['open_upload_forum'])
		&& $haya_pt_config['open_upload_forum'] == 1
	) {
		if (isset($haya_pt_config['upload_forum'])
			&& in_array($fid, $haya_pt_config['upload_forum'])
		) {
			return true;
		}
	}
	
	return false;
}

function haya_pt_check_user_upload_group($gid) {
	$haya_pt_config = kv_get('haya_pt');
	
	if (isset($haya_pt_config['open_user_upload_group'])
		&& $haya_pt_config['open_user_upload_group'] == 1
	) {
		if (!(isset($haya_pt_config['user_upload_group'])
			&& in_array($gid, $haya_pt_config['user_upload_group'])
		)) {
			return false;
		}
	}
	
	return true;
}

function haya_pt_check_user_download_group($gid) {
	$haya_pt_config = kv_get('haya_pt');
	
	if (isset($haya_pt_config['open_user_download_group'])
		&& $haya_pt_config['open_user_download_group'] == 1
	) {
		if (!(isset($haya_pt_config['user_download_group'])
			&& in_array($gid, $haya_pt_config['user_download_group'])
		)) {
			return false;
		}
	}
	
	return true;
}

function haya_pt_check_user_exchange_flux_group($gid) {
	$haya_pt_config = kv_get('haya_pt');
	
	if (isset($haya_pt_config['open_user_exchange_flux_group'])
		&& $haya_pt_config['open_user_exchange_flux_group'] == 1
	) {
		if (!(isset($haya_pt_config['user_exchange_flux_group'])
			&& in_array($gid, $haya_pt_config['user_exchange_flux_group'])
		)) {
			return false;
		}
	}
	
	return true;
}

function haya_pt_announce_check_user_auth($uid, $tid) {
	$haya_pt_config = kv_get('haya_pt');
	
	$haya_pt_allow = false;
	if (isset($haya_pt_config['open_upload_forum'])
		&& $haya_pt_config['open_upload_forum'] == 1
	) {
		$thread = thread__read($tid);
		
		if (isset($haya_pt_config['upload_forum'])
			&& in_array($thread['fid'], $haya_pt_config['upload_forum'])
		) {
			$haya_pt_allow = true;
		}
	}
	
	if ($haya_pt_allow) {
		if (isset($haya_pt_config['open_user_download_group'])
			&& $haya_pt_config['open_user_download_group'] == 1
		) {
			$user = user__read($uid);
			
			if (!(isset($haya_pt_config['user_download_group'])
				&& in_array($user['gid'], $haya_pt_config['user_download_group'])
			)) {
				return false;
			}
		}
	
		$user_ban = haya_pt_banlist__read($uid);
		if (!empty($user_ban)) {
			return false;
		}
		
		return true;
	}
	
	return false;
}

function haya_pt_announce_check_user_ip($ip) {
	$haya_pt_config = kv_get('haya_pt');
	
	$is_ipv6 = haya_pt_validate_ipv6($ip);
	
	if (isset($haya_pt_config['connect_ip_type'])) {
		if ($haya_pt_config['connect_ip_type'] == 'ipv6') {
			if (!$is_ipv6) {
				return false;
			}
		} elseif ($haya_pt_config['connect_ip_type'] == 'ipv4') {
			if ($is_ipv6) {
				return false;
			}
		}
	}

	return true;
}

?>