<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-5-20
 */

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '删除日志 - PT';

if ($method != 'POST') {
	message(1, jump('提交错误'));
}

$lid = param('lid');
if (empty($lid)) {
	message(1, jump('ID不能为空'));
}

// 判断
$haya_pt_log = haya_pt_log__read($lid);
if (empty($haya_pt_log)) {
	message(1, jump('日志不存在'));
}

$haya_pt_log_delete_status = haya_pt_log_delete_by_lid($lid);
if ($haya_pt_log_delete_status === false) {
	message(1, jump('日志删除失败'));
}

message(0, jump('操作成功'));


?>