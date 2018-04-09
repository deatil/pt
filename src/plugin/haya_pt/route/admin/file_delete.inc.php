<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = 'ban - PT';

if ($method != 'POST') {
	message(1, jump('提交错误'));
}

$tid = param('tid');
if (empty($tid)) {
	message(1, jump('ID不能为空'));
}

// 判断
$haya_pt_file = haya_pt_files_read_by_tid($tid);
if (empty($haya_pt_file)) {
	message(1, jump('种子文件不存在'));
}

$haya_pt_file_delete_status = haya_pt_files_delete_by_tid($tid);
if ($haya_pt_file_delete_status === false) {
	message(1, jump('种子文件删除失败'));
}

$haya_pt_file_path = haya_pt_plugin_torrent($haya_pt_file['url']);
if (file_exists($haya_pt_file_path)) {
	@unlink($haya_pt_file_path);
}

message(0, jump('操作成功'));


?>