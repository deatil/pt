<?php

function haya_pt_files__create($arr) {
	$r = db_create('pt_files', $arr);
	return $r;
}

function haya_pt_files__update($info_hash, $arr) {
	$r = db_update('pt_files', array(
		'info_hash' => $info_hash
	), $arr);
	return $r;
}

function haya_pt_files__delete($arr = array()) {
	if (empty($arr)) {
		return false;
	}
	
	$r = db_delete('pt_files', $arr);
	return $r;
}

function haya_pt_files__read($info_hash) {
	$r = db_find_one('pt_files', array(
		'info_hash' => $info_hash
	));
	return $r;
}

function haya_pt_files__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_files', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_pt_files__count($cond = array()) {
	$r = db_count('pt_files', $cond);
	return $r;
}

function haya_pt_files_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_files', $cond, $orderby, $page, $pagesize);
	
	foreach ($r as & $_r) {
		$_r['thread'] = thread_read($_r['tid']);
	}
	
	return $r;
}


function haya_pt_files_read_by_tid($tid) {
	$r = db_find_one('pt_files', array(
		'tid' => $tid
	));
	return $r;
}

function haya_pt_files_read_by_info_hash($info_hash) {
	$r = db_find_one('pt_files', array(
		'info_hash' => $info_hash
	));
	return $r;
}

function haya_pt_files_read_by_filename($filename) {
	$r = db_find_one('pt_files', array(
		'filename' => $filename
	));
	return $r;
}

function haya_pt_files_find_by_tids($tids, $page = 1, $pagesize = 20, $order = 'tid') {
	$haya_pt_files = haya_pt_files__find(array(
		'tid' => $tids
	), array(
		$order => -1
	), $page, $pagesize);
	
	return $haya_pt_files;
}

function haya_pt_files_update_by_tid($tid, $arr) {
	$r = db_update('pt_files', array(
		'tid' => $tid
	), $arr);
	return $r;
}

function haya_pt_files_delete_by_info_hash($info_hash) {
	$r = db_delete('pt_files', array(
		'info_hash' => $info_hash
	));
	return $r;
}

function haya_pt_files_delete_by_tid($tid) {
	$r = db_delete('pt_files', array(
		'tid' => $tid
	));
	return $r;
}

?>