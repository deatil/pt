<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-5-19
 */

defined('DEBUG') OR exit('Forbidden');

$header['title'] = "赠送流量 - PT";

if ($method == 'GET') {
	
	$action = 'users';
	
	$_uid = param('2');
	if (empty($_uid)) {
		message(1, jump('用户ID不能为空', url('pt-users')));
	}
	
	$_user = user_read($_uid);
	if (empty($_user)) {
		message(1, jump('用户不存在', url('pt-users')));	
	}
	
	$_pt_user = haya_pt_users__read($_uid);
	
	$haya_pt_view = haya_pt_plugin_view('htm/admin/flux.htm');
	include _include($haya_pt_view);
	
} else {
	$_uid = param('2');
	if (empty($_uid)) {
		message(1, jump('用户ID不能为空', url('pt-users')));
	}
	
	$num = param("num");
	
	$type = 'system';
	
	$money = 0;
	$flux = $num * 1024 * 1024 * 1024;
	
	if ($num >= 0) {
		$action = 'add';
		$content = '收到了系统赠送的流量 <span class="text-info">'.haya_pt_sizecount($flux).'</span>。';
	} else {
		$action = 'delete';
		$content = '系统回收了你的流量 <span class="text-info">'.haya_pt_sizecount(abs($flux)).'</span>。';
	}
	
	$haya_pt_users_status = haya_pt_users__update($_uid, array(
		'flux+' => $flux,
	));
	if ($haya_pt_users_status === false) {
		message(1, jump('赠送流量失败', url('pt-flux')));	
	}
	
	haya_pt_log__create(array(
		'uid' => $_uid,
		'action' => $action,
		'type' => $type,
		'money' => $money,
		'flux' => $flux,
		'content' => $content,
		'create_date' => time(),
		'create_ip' => $longip,
	));
	
	message(0, jump('赠送流量成功', url('pt-users')));
}


?>
