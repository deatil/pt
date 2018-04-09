<?php

function haya_pt_log__create($arr) {
	$r = db_create('pt_log', $arr);
	return $r;
}

function haya_pt_log__update($lid, $arr) {
	$r = db_update('pt_log', array(
		'lid' => $lid
	), $arr);
	return $r;
}

function haya_pt_log__read($lid) {
	$r = db_find_one('pt_log', array(
		'lid' => $lid
	));
	return $r;
}

function haya_pt_log__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_log', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_pt_log__count($cond = array()) {
	$r = db_count('pt_log', $cond);
	return $r;
}

function haya_pt_log_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_log', $cond, $orderby, $page, $pagesize, 'lid');
	
	if (!empty($r)) {
		foreach ($r as & $log) {
			$log['user'] = user_read_cache($log['uid']);
		}
	}
	
	return $r;
}

?>