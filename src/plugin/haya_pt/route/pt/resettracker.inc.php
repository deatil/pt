<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '重置Tracker - PT';

$user = user_read($uid);
user_login_check();

if ($method == 'GET') {
	$is_ban = haya_pt_banlist__read($uid);
	
	$tracker = haya_pt_get_user_tracker($uid);

	include _include(haya_pt_plugin_view('htm/user/resettracker.htm'));
} else {
	
	if (isset($_POST['changepid'])) {
		$pid = md5(uniqid(rand(), true));

		haya_pt_users__update($uid, array(
			'pid' => $pid
		));
	}
	
	message(0, jump('重置Tracker成功', url('pt-resettracker')));	
}


?>