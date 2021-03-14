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

$header['title'] = '兑换流量 - PT';

$user = user_read($uid);
user_login_check();

// hook plugin_haya_pt_shop_start.php

if ($method == 'GET') {
	$haya_pt_user = haya_pt_users__read($uid);

	$haya_pt_check_user_exchange_flux_group = haya_pt_check_user_exchange_flux_group($gid);
	
	// hook plugin_haya_pt_shop_get_start.php
	
	include _include(haya_pt_plugin_view('htm/user/shop.htm'));
} else {
	
	// hook plugin_haya_pt_shop_post_start.php
	
	$num = param("num", 0);
	if ($num <= 0) {
		message(1, jump('兑换数量不能为空', url('pt-shop')));	
	}
	
	$num = abs($num);
	
	$action = 'add';
	$type = 'credits';
	
	if (!isset($haya_pt_config['exchange_flux_money'])) {
		$haya_pt_config['exchange_flux_money'] = 20;
	}
	$money = $num * $haya_pt_config['exchange_flux_money'];
	$flux = $num * 1024 * 1024 * 1024;
	$content = '兑换了流量：<span class="text-info">'.haya_pt_sizecount($flux).'</span>，共花费积分：<span class="text-success">'.$money.'</span> 枚。';
	
	if ($num > $user['credits']) {
		message(1, jump('你的积分不够，不能够进行兑换', url('pt-shop')));	
	}
	
	$haya_pt_user_status = user__update($uid, array(
		'credits-' => $money,
	));
	if ($haya_pt_user_status === false) {
		message(1, jump('兑换流量失败', url('pt-shop')));	
	}
	
	$haya_pt_users_status = haya_pt_users__update($uid, array(
		'flux+' => $flux,
	));
	if ($haya_pt_users_status === false) {
		message(1, jump('兑换流量失败', url('pt-shop')));	
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
	
	// hook plugin_haya_pt_shop_post_end.php
	
	message(0, jump('兑换流量成功', url('pt-shop')));	
}


?>