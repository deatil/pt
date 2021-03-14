<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-4-5
 */
 
defined('DEBUG') OR exit('Forbidden');

$header['title'] = '统计 - PT';

$user = user_read($uid);
user_login_check();

$user_torrent = haya_pt_users__read($uid);

if (!isset($user_torrent['flux'])) {
	$user_torrent['flux'] = 0;
}

if (!isset($user_torrent['uploaded'])) {
	$user_torrent['uploaded'] = 0;
}

if (!isset($user_torrent['downloaded'])) {
	$user_torrent['downloaded'] = 0;
}

include _include(haya_pt_plugin_view('htm/user/info.htm'));


?>