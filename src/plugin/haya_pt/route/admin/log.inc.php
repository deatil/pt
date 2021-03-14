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

$header['title'] = '兑换记录 - PT';

$pagesize = 15;
$srchtype = param(2);
$keyword  = trim(xn_urldecode(param(3)));
$page     = param(4, 1);

$where = array();
if ($keyword) {
	if (in_array($srchtype, array(
		'uid', 
	))) {
		$where[$srchtype] = array('LIKE' => $keyword); 
	}	
}

$total = haya_pt_log__count($where);
$haya_pt_logs = haya_pt_log_find($where, array('create_date' => -1), $page, $pagesize);
$pagination = pagination(url("pt-log-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

include _include(haya_pt_plugin_view('htm/admin/log.htm'));

?>