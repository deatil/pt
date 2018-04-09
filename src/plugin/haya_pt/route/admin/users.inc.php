<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '用户 - PT';

$pagesize = 10;
$srchtype = param(2);
$keyword  = trim(xn_urldecode(param(3)));
$page     = param(4, 1);

$where = array();
if ($keyword) {
	if (in_array($srchtype, array(
		'uid', 
		'pid', 
	))) {
		$where[$srchtype] = array('LIKE' => $keyword); 
	}	
}

$total = haya_pt_users__count($where);
$haya_pt_users = haya_pt_users_find($where, array('last_active' => -1), $page, $pagesize);

$pagination = pagination(url("pt-users-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

$haya_pt_user_uids = array();
if (!empty($haya_pt_users)) {
	foreach ($haya_pt_users as $haya_pt_user) {
		$haya_pt_user_uids[] = $haya_pt_user['uid'];
	}
}

$haya_pt_ban_uids = haya_pt_banlist_find_ban_uids($haya_pt_user_uids, count($haya_pt_user_uids));

include _include(haya_pt_plugin_view('htm/admin/users.htm'));

?>