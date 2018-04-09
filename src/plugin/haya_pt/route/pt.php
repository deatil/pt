<?php

/**
 * PT
 *
 * 用于商业用途请获取授权
 * 
 * @create 2018-3-29
 * @author deatil
 * 
 * @license GPL2 
 */
 
defined('DEBUG') OR exit('Forbidden');

// 动作
$action = param(1);
empty($action) and $action = 'history';

$actions = array(
	'upload',
	
	'announce',
	
	'download',
	'downloaded_users',
	'torr_info',
	'weight',
	
	'history',
	'resettracker',
	'info',

	'shop',
	'gift',
	'log',
);

if (!in_array($action, $actions)) {
	message(1, jump('访问错误'));
}

// 引入核心
$haya_pt_action_file = haya_pt_plugin_action($action.'.inc.php');
if (!file_exists($haya_pt_action_file)) {
	message(1, jump('路由文件不存在'));
}

include haya_pt_plugin_dir().'config/config.php';

$tablepre = $db->tablepre;
$haya_pt_config = kv_get('haya_pt');

include _include($haya_pt_action_file);

