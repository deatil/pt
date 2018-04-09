<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '兑换记录 - PT';

$user = user_read($uid);
user_login_check();

$pagesize = 15;
$current_page = param(2, 1);

if ($gid == 1) {
	$uid = param('uid', $uid);
}

$haya_pt_logs = haya_pt_log__find(array(
	'uid' => $uid
), array('create_date' => -1), $current_page, $pagesize);

$total = haya_pt_log__count(array('uid' => $uid));
$pagination = pagination(url("pt-log-{page}"), $total, $current_page, $pagesize);

include _include(haya_pt_plugin_view('htm/user/log.htm'));

?>