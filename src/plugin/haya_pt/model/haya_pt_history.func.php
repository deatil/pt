<?php

function haya_pt_history__create($arr) {
	$r = db_create('pt_history', $arr);
	return $r;
}

function haya_pt_history__update($infohash, $arr) {
	$r = db_update('pt_history', array(
		'infohash' => $infohash
	), $arr);
	return $r;
}

function haya_pt_history__read($infohash) {
	$r = db_find_one('pt_history', array(
		'infohash' => $infohash
	));
	return $r;
}

function haya_pt_history__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_history', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_pt_history__count($cond = array()) {
	$r = db_count('pt_history', $cond);
	return $r;
}

function haya_pt_history_read_by_uid_and_infohash($uid, $infohash) {
	$r = db_find_one('pt_history', array(
		'uid' => $uid,
		'infohash' => $infohash
	));
	return $r;
}

function haya_pt_history_update_by_uid_and_infohash($uid, $infohash, $arr) {
	$r = db_update('pt_history', array(
		'uid' => $uid,
		'infohash' => $infohash
	), $arr);
	return $r;
}

function haya_pt_history_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_history', $cond, $orderby, $page, $pagesize);
	
	if (!empty($r)) {
		foreach ($r as & $history) {
			$history['user'] = user_read($history['uid']);
		}
	}
	
	return $r;
}


?>