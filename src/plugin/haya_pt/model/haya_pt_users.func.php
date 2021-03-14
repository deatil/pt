<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-3-31
 */

function haya_pt_users__create($arr) {
    $r = db_create('pt_users', $arr);
    return $r;
}

function haya_pt_users__update($uid, $arr) {
    $r = db_update('pt_users', array(
        'uid' => $uid
    ), $arr);
    return $r;
}

function haya_pt_users__read($uid) {
    $r = db_find_one('pt_users', array(
        'uid' => $uid
    ));
    return $r;
}

function haya_pt_users__find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('pt_users', $cond, $orderby, $page, $pagesize);
    
    return $r;
}

function haya_pt_users__count($cond = array()) {
    $r = db_count('pt_users', $cond);
    return $r;
}

function haya_pt_users_find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('pt_users', $cond, $orderby, $page, $pagesize);
    
    if (!empty($r)) {
        foreach ($r as & $user) {
            $user['user'] = user_read($user['uid']);
        }
    }
    
    return $r;
}

function haya_pt_users_read_by_uid($uid) {
    $r = db_find_one('pt_users', array(
        'uid' => $uid
    ));
    return $r;
}

function haya_pt_users_read_by_pid($pid) {
    $r = db_find_one('pt_users', array(
        'pid' => $pid
    ));
    return $r;
}

function haya_pt_users_get_pid_by_uid($uid = '') {
    if (empty($uid)) {
        return false;
    }
    
    $pt_user = haya_pt_users_read_by_uid($uid);
    if (empty($pt_user)) {
        $pid = md5(uniqid(rand(), true));	
        haya_pt_users__create(array(
            'uid' => $uid,
            'pid' => $pid,
            'last_active' => time(),
            'last_ip' => GLOBALS('longip'),
            'create_date' => time(),
            'create_ip' => GLOBALS('longip'),
        ));
    } else {
        $pid = $pt_user['pid'];
    }
    
    return $pid;
}


function haya_pt_get_user_tracker($uid) {
    $haya_pt_user_pid = haya_pt_users_get_pid_by_uid($uid);
    if ($haya_pt_user_pid === false) {
        return false;
    }
    
    $haya_pt_config = kv_get('haya_pt');
    $haya_pt_tracker_url = str_replace('__pid__', $haya_pt_user_pid, $haya_pt_config['tracker_url']);
    
    return $haya_pt_tracker_url;
}

?>