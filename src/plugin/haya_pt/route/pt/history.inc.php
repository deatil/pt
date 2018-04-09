<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '下载历史 - PT';

$user = user_read($uid);
user_login_check();

$pagesize = 10;
$current_page = param(2, 1);

if ($gid == 1) {
	$uid = param('uid', $uid);
}

$historys = haya_pt_history__find(array(
	'uid' => $uid
), array('date' => -1), $current_page, $pagesize);

$total = haya_pt_history__count(array('uid' => $uid));

$haya_pt_peer_infohashs = array();
$i = 0;
foreach ($historys as $history) {
	if ($i % 2 == 1) {
		$historys[$i]['color'] = "table-secondary";
	} else {
		$historys[$i]['color'] = "table-light";
	}
	
	$thread = thread_read($history['tid']);
	
	$historys[$i]['subject'] = $thread['subject'];
	$historys[$i]['tid'] = $history['tid'];
	$historys[$i]['makedate'] = haya_pt_humandate($history['makedate']);
	$historys[$i]['date'] = haya_pt_humandate($history['date']);
	$historys[$i]['uploaded'] = haya_pt_sizecount($history['uploaded']);
	$historys[$i]['realup'] = haya_pt_sizecount($history['realup']);
	$historys[$i]['downloaded'] = haya_pt_sizecount($history['downloaded']);
	$historys[$i]['realdown'] = haya_pt_sizecount($history['realdown']);
	
	$haya_pt_peer_infohashs[] = $history['infohash'];
	
	$i++;
}

$pagination = pagination(url("pt-history-{page}"), $total, $current_page, $pagesize);

$haya_pt_peer_infohash_list = haya_pt_peers_find_by_uid_and_infohash($uid, $haya_pt_peer_infohashs, count($haya_pt_peer_infohashs));

include _include(haya_pt_plugin_view('htm/user/history.htm'));

?>