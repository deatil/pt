<?php

function haya_pt_peers__create($arr) {
	$r = db_create('pt_peers', $arr);
	return $r;
}

function haya_pt_peers__update($infohash, $arr) {
	$r = db_update('pt_peers', array(
		'infohash' => $infohash
	), $arr);
	return $r;
}

function haya_pt_peers__delete($arr = array()) {
	if (empty($arr)) {
		return false;
	}
	
	$r = db_delete('pt_peers', $arr);
	return $r;
}

function haya_pt_peers__read($infohash) {
	$r = db_find_one('pt_peers', array(
		'infohash' => $infohash
	));
	return $r;
}

function haya_pt_peers__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_peers', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_pt_peers__count($cond = array()) {
	$r = db_count('pt_peers', $cond);
	return $r;
}

function haya_pt_peers_read_by_uid_and_infohash($uid, $infohash) {
	$r = db_find_one('pt_peers', array(
		'uid' => $uid,
		'infohash' => $infohash
	));
	return $r;
}

function haya_pt_peers_find_by_rand(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('pt_peers', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_pt_peers_find_by_infohash($infohash, $pagesize = 2000) {
	$infohashs = db_find('pt_peers', array(
		'infohash' => $infohash, 
	), array('infohash' => -1), 1, $pagesize, 'infohash'); 	
	
	return $infohashs;
}

function haya_pt_peers_find_by_uid_and_infohash($uid, $infohash, $pagesize = 2000) {
	$infohashs = db_find('pt_peers', array(
		'uid' => $uid,
		'infohash' => $infohash, 
	), array('infohash' => -1), 1, $pagesize, 'infohash'); 	
	
	return $infohashs;
}

function haya_pt_peers_update_by_uid_and_infohash($uid, $infohash, $arr) {
	$r = db_update('pt_peers', array(
		'uid' => $uid,
		'infohash' => $infohash
	), $arr);
	return $r;
}

function haya_pt_peers_delete_by_uid_and_infohash($uid, $infohash) {
	$r = db_delete('pt_peers', array(
		'uid' => $uid,
		'infohash' => $infohash
	));
	return $r;
}

function haya_pt_peers_delete_by_tid($tid) {
	$r = db_delete('pt_peers', array(
		'tid' => $tid,
	));
	return $r;
}

?>