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

if (empty($user)) {
	message(1, '请登录');
}

$tid = param('tid', 0);
if (empty($tid)) {
	message(1, '参数错误');
}

$haya_pt_history_users = haya_pt_history_find(array(
	'tid' => $tid
), array(
	'date' => -1
), 1, 12);

include haya_pt_plugin_view('htm/torrent/downloaded_users.htm');

?>