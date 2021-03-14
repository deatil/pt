<?php

/**
 * PT
 *
 * 用于商业用途请获取授权
 * 
 * @create 2018-4-2
 * @author deatil
 * 
 * @license GPL2 
 */
 
defined('DEBUG') OR exit('Forbidden');

// 动作
$action = param(1);
empty($action) and $action = 'setting';

$actions = array(
	'setting',
	
	'files',
	'file',
	'file_delete',
	'history',
	'torrent',
	
	'users',
	
	'ban',
	'banlist',
	
	'log',
	'log_delete',
	
	'flux',
);

if (!in_array($action, $actions)) {
	message(1, jump('访问错误'));
}

// 引入核心
$haya_pt_action_file = haya_pt_plugin_admin_action($action.'.inc.php');
if (!file_exists($haya_pt_action_file)) {
	message(1, jump('路由文件不存在'));
}

// 定义导航
$haya_pt_admin_config = haya_pt_get_plugin_config('admin_menu');
if ($haya_pt_admin_config === false) {
	message(1, jump('插件配置文件不存在', url('plugin')));
}
$haya_pt_admin_menu = include _include($haya_pt_admin_config);

$tablepre = $db->tablepre;
$haya_pt_admin_view = haya_pt_plugin_view('htm/admin/');

include _include($haya_pt_action_file);

