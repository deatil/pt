<?php

// 插件自定义缓存
$g_haya_pt_cache = FALSE;
function haya_pt_cache_get($k) {
	global $g_haya_pt_cache;
	$g_haya_pt_cache === FALSE AND $g_haya_pt_cache = cache_get('haya_pt');
	empty($g_haya_pt_cache) AND $g_haya_pt_cache = array();
	return array_value($g_haya_pt_cache, $k, NULL);
}

function haya_pt_cache_set($k, $v) {
	global $g_haya_pt_cache;
	$g_haya_pt_cache === FALSE AND $g_haya_pt_cache = cache_get('haya_pt');
	empty($g_haya_pt_cache) AND $g_haya_pt_cache = array();
	$g_haya_pt_cache[$k] = $v;
	return cache_set('haya_pt', $g_haya_pt_cache);
}

function haya_pt_cache_delete($k) {
	global $g_haya_pt_cache;
	$g_haya_pt_cache === FALSE AND $g_haya_pt_cache = cache_get('haya_pt');
	empty($g_haya_pt_cache) AND $g_haya_pt_cache = array();
	if(isset($g_haya_pt_cache[$k])) unset($g_haya_pt_cache[$k]);
	cache_set('haya_pt', $g_haya_pt_cache);
	return TRUE;
}


?>