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

$header['title'] = '种子详情 - PT';

$tid = param(2);
if (empty($tid)) {
	message(1, jump('ID不能为空'));
}

$haya_pt_torrent = haya_pt_files_read_by_tid($tid);
if (empty($haya_pt_torrent)) {
	message(1, jump('种子文件不存在', url('pt-files')));
}

// 权值
$haya_pt_config_file = haya_pt_plugin_dir().'config/config.php';
include($haya_pt_config_file);

if (!isset($haya_pt_torrent['type']) 
	|| !in_array($haya_pt_torrent['type'], array(1, 2, 3, 4, 5))
) {
	$haya_pt_type = 5;
} else {
	$haya_pt_type = $haya_pt_torrent['type'];
}
$haya_pt_torrent_type = 'type'.$haya_pt_type;

$haya_pt_up_image = "up".$upload_weight[$haya_pt_torrent_type].".gif";
$haya_pt_down_image = "down".$down_weight[$haya_pt_torrent_type].".gif";
	
$haya_pt_torrent['filename'] = htmlspecialchars_decode($haya_pt_torrent['filename']);
$haya_pt_filename = haya_pt_substr($haya_pt_torrent['filename'], 40);
$haya_pt_size = haya_pt_sizecount($haya_pt_torrent['size']);
$haya_pt_filesize = haya_pt_sizecount($haya_pt_torrent['filesize']);
$haya_pt_lastactive = haya_pt_humandate($haya_pt_torrent['lastactive']);

$haya_pt_thread = thread_read($tid);

$action = "files";

include _include(haya_pt_plugin_view('htm/admin/file.htm'));

?>