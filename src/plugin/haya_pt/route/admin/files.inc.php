<?php

defined('DEBUG') OR exit('Forbidden');

$header['title'] = '种子 - PT';

$pagesize = 10;
$srchtype = param(2);
$keyword  = trim(xn_urldecode(param(3)));
$page     = param(4, 1);

$where = array();
if ($keyword) {
	if (!in_array($srchtype, array(
		'fid', 
		'info_hash', 
		'tid',
		'filename',
		'type',
	))) {
		$srchtype = 'filename';
	}
	
	$where[$srchtype] = array('LIKE' => $keyword); 
}

$total = haya_pt_files__count($where);
$haya_pt_files = haya_pt_files_find($where, array('date' => -1), $page, $pagesize);

$pagination = pagination(url("pt-files-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

include _include(haya_pt_plugin_view('htm/admin/files.htm'));

?>