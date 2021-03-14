<?php

/**
 * plugin-pt
 * 
 * @author deatil
 * @create 2018-4-5
 */

/**
 * 获取缓存
 */
function haya_pt_cache_get($k) {
    // 16位md5值
    $key = md5($k, true);
    return cache_get($key);
}

/**
 * 设置缓存
 */
function haya_pt_cache_set($k, $v) {
    $key = md5($k, true);
    return cache_set($key, $v);
}

/**
 * 删除缓存
 */
function haya_pt_cache_delete($k) {
    $key = md5($k, true);
    return cache_delete($key);
}

?>