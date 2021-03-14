<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-4-5
 */

function haya_pt_banlist__create($arr) {
    $r = db_create('pt_banlist', $arr);
    return $r;
}

function haya_pt_banlist__update($ban_uid, $arr) {
    $r = db_update('pt_banlist', array(
        'ban_uid' => $ban_uid
    ), $arr);
    return $r;
}

function haya_pt_banlist__read($ban_uid) {
    $r = db_find_one('pt_banlist', array(
        'ban_uid' => $ban_uid
    ));
    return $r;
}

function haya_pt_banlist__find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('pt_banlist', $cond, $orderby, $page, $pagesize);
    
    return $r;
}

function haya_pt_banlist__count($cond = array()) {
    $r = db_count('pt_banlist', $cond);
    return $r;
}

function haya_pt_banlist_read_by_action_uid($action_uid) {
    $r = db_find_one('pt_banlist', array(
        'action_uid' => $action_uid
    ));
    return $r;
}

function haya_pt_banlist_read_by_ban_ip($ban_ip) {
    $r = db_find_one('pt_banlist', array(
        'ban_ip' => $ban_ip
    ));
    return $r;
}


function haya_pt_banlist_find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('pt_banlist', $cond, $orderby, $page, $pagesize);
    
    if (!empty($r)) {
        foreach ($r as & $ban) {
            $ban['user'] = user_read_cache($ban['ban_uid']);
            $ban['action_user'] = user_read_cache($ban['action_uid']);
        }
    }
    
    return $r;
}

function haya_pt_banlist_find_ban_uids($ban_uids, $pagesize = 2000) {
    $ban_users = db_find('pt_banlist', array(
        'ban_uid' => $ban_uids, 
    ), array('ban_id' => -1), 1, $pagesize, '', array('ban_uid')); 	
    
    $ban_uids = arrlist_values($ban_users, 'ban_uid');
    
    return $ban_uids;
}

function haya_pt_delete_by_ban_uid($ban_uid) {
    $r = db_delete('pt_banlist', array('ban_uid' => $ban_uid));
    return $r;
}


?>