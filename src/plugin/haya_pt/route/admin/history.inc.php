<?php 

defined('DEBUG') OR exit('Forbidden');

$tid = param(2, 0);
if (empty($tid)) {
	message(1, '参数错误');
}

$haya_pt_thread = thread_read($tid);
if (empty($haya_pt_thread['subject'])) {
	$haya_pt_thread['subject'] = '主题已被删除';
}

$pagesize = 10;
$page     = param(3, 1);

$total = haya_pt_history__count(array(
	'tid' => $tid
));
$historys = haya_pt_history_find(array(
	'tid' => $tid
), array(
	'date' => -1
), $page, $pagesize);
$pagination = pagination(url("pt-history-{$tid}-{page}"), $total, $page, $pagesize);

$action = "files";

include haya_pt_plugin_view('htm/admin/history.htm');

?>