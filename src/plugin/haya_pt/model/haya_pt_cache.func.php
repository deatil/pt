<?php

/**
 * plugin-pt
 * 
 * @author deatil
 * @create 2018-4-5
 */

/**
 * 生成16位md5
 */
function haya_pt_cache_md5($str, $type16 = false) {
    if ($type16) {
        return substr(md5($str), 8, 16);
    }
    
    return md5($str);
}

/**
 * 获取缓存
 */
function haya_pt_cache_get($k) {
    // 16位md5值
    $key = haya_pt_cache_md5($k, true);
    return cache_get($key);
}

/**
 * 设置缓存
 */
function haya_pt_cache_set($k, $v) {
    $key = haya_pt_cache_md5($k, true);
    return cache_set($key, $v);
}

/**
 * 删除缓存
 */
function haya_pt_cache_delete($k) {
    $key = haya_pt_cache_md5($k, true);
    return cache_delete($key);
}

?>