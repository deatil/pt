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

$header['title'] = "PT".lang('setting');

if ($method == 'GET') {
	
	$config = kv_get('haya_pt');
	$upload_forum = is_array($config['upload_forum']) ? $config['upload_forum'] : array();
	$user_upload_group = is_array($config['user_upload_group']) ? $config['user_upload_group'] : array();
	$user_download_group = is_array($config['user_download_group']) ? $config['user_download_group'] : array();
	$user_exchange_flux_group = is_array($config['user_exchange_flux_group']) ? $config['user_exchange_flux_group'] : array();
	
	$haya_pt_view = haya_pt_plugin_view('htm/admin/setting.htm');
	include _include($haya_pt_view);
	
} else {
	
	$config = array();
	
	$config['tracker_url'] = param('tracker_url', '');
	$config['help_url'] = param('help_url', '');
	$config['website'] = param('website', '');
	$config['connect_ip_type'] = param('connect_ip_type', 'all');
	
	$config['open_upload_forum'] = param('open_upload_forum', 0);
	$config['upload_forum'] = param('upload_forum', array());
	$config['upload_forum_tip'] = param('upload_forum_tip', '');
	
	$config['open_user_upload_group'] = param('open_user_upload_group', 0);
	$config['user_upload_group'] = param('user_upload_group', array());
	$config['user_upload_group_tip'] = param('user_upload_group_tip', '');
	
	$config['open_user_download_group'] = param('open_user_download_group', 0);
	$config['user_download_group'] = param('user_download_group', array());
	$config['user_download_group_tip'] = param('user_download_group_tip', '');
	
	$config['open_user_exchange_flux_group'] = param('open_user_exchange_flux_group', 0);
	$config['user_exchange_flux_group'] = param('user_exchange_flux_group', array());
	$config['user_exchange_flux_group_tip'] = param('user_exchange_flux_group_tip', '');
	$config['exchange_flux_money'] = param('exchange_flux_money', 20);
	
	$config['open_user_gift_flux'] = param('open_user_gift_flux', 0);
	
	kv_set('haya_pt', $config); 
	
	message(0, jump('设置保存成功', url('pt-setting')));
}


?>
