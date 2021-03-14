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

$header['title'] = 'ban - PT';

if ($method != 'POST') {
	message(1, jump('提交错误'));
}

$_uid = param('uid');
if (empty($_uid)) {
	message(1, jump('ID不能为空'));
}

$haya_pt_action = param(2, 'create');
if ($haya_pt_action == 'create') {

	$_user = user_read($_uid);
	if (empty($user)) {
		message(1, jump('用户不存在'));
	}

	// 判断
	$haya_pt_ban_user = haya_pt_banlist__read($_uid);
	if (!empty($haya_pt_ban_user)) {
		message(1, jump('你已经BAN过该用户了'));
	}
	
	if (empty($_user['create_ip'])) {
		$_user['create_ip'] = '';
	}
	
	$haya_pt_ban_status = haya_pt_banlist__create(array(
		'ban_uid' => $_uid,
		'ban_ip' => $_user['create_ip'],
		'ban_email' => $_user['email'],
		'action_uid' => $uid,
		'action_date' => time(),
		'action_ip' => $longip,
		'create_date' => time(),
		'create_ip' => $longip,
	));
	if ($haya_pt_ban_status === false) {
		message(1, jump('BAN该用户失败'));
	}
	
	message(0, jump('BAN该用户成功'));
} elseif ($haya_pt_action == 'delete') {
	// 判断
	$haya_pt_ban_user = haya_pt_banlist__read($_uid);
	if (empty($haya_pt_ban_user)) {
		message(1, jump('你还没有BAN过该用户'));
	}
	
	$haya_pt_ban_status = haya_pt_delete_by_ban_uid($_uid);
	if ($haya_pt_ban_status === false) {
		message(1, jump('取消BAN该用户失败'));
	}
	
	message(0, jump('取消BAN该用户成功'));
}


?>