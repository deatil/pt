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

$header['title'] = '赠送流量 - PT';

$user = user_read($uid);
user_login_check();

$haya_pt_user = haya_pt_users__read($uid);

// hook plugin_haya_pt_gift_start.php

if ($method == 'GET') {	
	// hook plugin_haya_pt_gift_get_start.php
	
	include _include(haya_pt_plugin_view('htm/user/gift.htm'));
} else {

	if (!(isset($haya_pt_config['open_user_gift_flux'])
		&& $haya_pt_config['open_user_gift_flux'] == 1
	)) {
		message(1, jump('没有启用赠送流量', url('pt-gift')));
	}
	
	// hook plugin_haya_pt_gift_post_start.php
	
	$_uid = param("uid", 0);
	if (empty($_uid)) {
		message(1, jump('赠送用户ID不能为空', url('pt-gift')));	
	}
	
	$_user = user_read($_uid);
	if (empty($_user)) {
		message(1, jump('赠送用户不存在', url('pt-gift')));	
	}
	
	$num = param("num", 0);
	if ($num <= 0) {
		message(1, jump('赠送数量不能为空', url('pt-gift')));	
	}
	
	$num = abs($num);
	
	$action = 'delete';
	$type = 'gift';
	
	$money = 0;
	$flux = $num * 1024 * 1024 * 1024;
	$content = '赠送了流量 <span class="text-info">'.haya_pt_sizecount($flux).'</span> 给 <a href="'.url('user-'.$_user['uid']).'" target="_blank"><span class="text-info">'.$_user['username'].'</span></a> 。';
	
	if ($flux > $haya_pt_user['flux']) {
		message(1, jump('你的流量不够，不能够进行赠送', url('pt-gift')));	
	}
	
	$haya_pt_users_status = haya_pt_users__update($uid, array(
		'flux-' => $flux,
	));
	if ($haya_pt_users_status === false) {
		message(1, jump('赠送流量失败', url('pt-gift')));	
	}
	
	$haya_pt_users_gift_status = haya_pt_users__update($_uid, array(
		'flux+' => $flux,
	));
	if ($haya_pt_users_gift_status === false) {
		message(1, jump('赠送流量失败', url('pt-gift')));	
	}
	
	haya_pt_log__create(array(
		'uid' => $uid,
		'action' => $action,
		'type' => $type,
		'money' => $money,
		'flux' => $flux,
		'content' => $content,
		'create_date' => time(),
		'create_ip' => $longip,
	));
	
	// 收流量方信息
	$_content = '收到了来自<a href="'.url('user-'.$user['uid']).'" target="_blank"><span class="text-info">'.$user['username'].'</span></a> 的流量 <span class="text-info">'.haya_pt_sizecount($flux).'</span>。';
	haya_pt_log__create(array(
		'uid' => $_uid,
		'action' => 'add',
		'type' => 'gift',
		'money' => $money,
		'flux' => $flux,
		'content' => $_content,
		'create_date' => time(),
		'create_ip' => $longip,
	));
	
	// hook plugin_haya_pt_gift_post_end.php
	
	message(0, jump('赠送流量成功', url('pt-gift')));	
}


?>