<?php

/**
 * 加注释版本
 *
 * 1、检测传来的各种参数是否合法
 * 2、记录到历史
 * 3、对各种动作进行处理
 * 
 * @author deatil
 * @create 2018-3-31
 *
 */

if (!preg_match("/^uTorrent|^μTorrent|^qBittorrent|^transmission/i", $_SERVER["HTTP_USER_AGENT"])){
    header("HTTP/1.0 500 Bad Request");
    die("This a a bittorrent application and can't be loaded into a browser");
}

ignore_user_abort(1); // 忽略与用户的断开
error_reporting(E_ALL ^ E_NOTICE);

$passkey_key = 'pid';
$max_left_val = 536870912000;   // 500 GB
$max_up_down_val = 5497558138880;  // 5 TB
$max_up_add_val = 85899345920;    // 80 GB
$max_down_add_val = 85899345920;    // 80 GB

if (!isset($_GET[$passkey_key]) 
    || !is_string($_GET[$passkey_key]) 
    || strlen($_GET[$passkey_key]) != 32
) {
    haya_pt_show_error('Please LOG IN and REDOWNLOAD this torrent (pid not found)');
}

// 字符串 key 值
$input_vars_str = array(
    'info_hash',
    'peer_id',
    'event',
    $passkey_key,
    
    'key',
);
// 数字 key 值
$input_vars_num = array(
    'port',
    'uploaded',
    'downloaded',
    'left',
    'numwant',
    'compact',
    
    // 额外参数
    'corrupt',
    'no_peer_id',
    'ipv6',
    'supportcrypto',
    'redundant',
);

// 字符串
foreach ($input_vars_str as $var_name) {
    $$var_name = isset($_GET[$var_name]) ? (string)$_GET[$var_name] : null;
}
// 数字
foreach ($input_vars_num as $var_name) {
    $$var_name = isset($_GET[$var_name]) ? (float)$_GET[$var_name] : null;
}

// 检测是否所有数据客户端都发送了（下载客户端发送了错误的参数！）
if (!isset($info_hash) || strlen($info_hash) != 20) {
    haya_pt_show_error('Invalid info_hash');
}
if (!isset($peer_id) || strlen($peer_id) != 20) {
    haya_pt_show_error('Invalid peer_id');
}
if (!isset($port) || $port < 0 || $port > 0xFFFF) {
    haya_pt_show_error('Invalid port');
}
if (!isset($uploaded) || $uploaded < 0 || $uploaded > $max_up_down_val || $uploaded == 1844674407370) {
    haya_pt_show_error('Invalid uploaded value');
}
if (!isset($downloaded) || $downloaded < 0 || $downloaded > $max_up_down_val || $downloaded == 1844674407370) {
    haya_pt_show_error('Invalid downloaded value');
}
if (!isset($left) || $left < 0 || $left > $max_left_val) {
    haya_pt_show_error('Invalid left value');
}

$numwants = array("numwant", "num want", "num_want");
foreach ($numwants as $k) {
    if (isset($_GET[$k])) {
        $numwant = 0 + $_GET[$k];
        break;
    }
}
if (!isset($numwant) || $numwant < 0) {
    $numwant = 50;
}

$ip = haya_pt_getip();

if (get_magic_quotes_gpc()) {
    $info_hash = bin2hex(stripslashes($info_hash));
} else {
    $info_hash = bin2hex($info_hash);
}

if (get_magic_quotes_gpc()) {
    $peer_id = bin2hex(stripslashes($peer_id));
} else {
    $peer_id = bin2hex($peer_id);
}

$pid = AddSlashes(StripSlashes($pid));
if ($pid == "" || !$pid) {
   haya_pt_show_error("请重新下载种子，种子的tracker是不合法的。");
}

$respid = haya_pt_users_read_by_pid($pid);
if (!$respid || empty($respid)) {
    haya_pt_show_error("错误的pid值，请重新下载。");
}

$uid = $respid["uid"];

$agent = trim($_SERVER["HTTP_USER_AGENT"]);

// 服务器种子检测
$torrent = haya_pt_files__read($info_hash);
if (empty($torrent)) {
   haya_pt_show_error("种子可能已在服务器删除。");//种子不在服务器上面
}

$tid = $torrent['tid'];

// 权限检测
$haya_pt_announce_user_check = haya_pt_announce_check_user_auth($uid, $tid);
if ($haya_pt_announce_user_check === false) {
    haya_pt_show_error("你的权限不足，不能够下载该资源。");
}

$haya_pt_announce_ip_check = haya_pt_announce_check_user_ip($ip);
if ($haya_pt_announce_ip_check === false) {
    haya_pt_show_error("IP隧道错误，当前限制了特定的IP隧道可以访问。");
}

$peer = haya_pt_get_peer_info($uid, $info_hash);

// 获取种子类型，用于统计流量
if (!isset($torrent['type']) 
    || !in_array($torrent['type'], array(1, 2, 3, 4, 5))
) {
    $torrent['type'] = 5;
}
$type = 'type'.$torrent['type'];

if (!isset($peer)) {
    $peer["uploaded"] = 0;
    $peer["downloaded"] = 0;
}

$new_download_true = max(0, $downloaded - $peer["downloaded"]);
$new_upload_true = max(0, $uploaded - $peer["uploaded"]);
$new_download = $new_download_true * $down_weight[$type];
$new_upload = $new_upload_true * $upload_weight[$type];

// 用户流量小于0将不能下载，第一次出现负数将是可能的
if ($left != 0 && $new_download > 0) {
    $haya_pt_user = haya_pt_users__read($uid);
    if (!(is_numeric($haya_pt_user['flux']) 
        && ($haya_pt_user['flux'] >= 0)
    )) {
        haya_pt_show_error("您的流量过低，不能下载该资源！");
    }
}

// 上传/下载 速度
$speed_up = $speed_down = 0;

if (!empty($peer) && $peer['last_action'] < time()) {
    if (isset($peer['uploaded']) && $uploaded > $peer['uploaded']) {
        $speed_up = ceil(($uploaded - $peer['uploaded']) / (time() - $peer['last_action']));
    }
    if (isset($peer['downloaded']) && $downloaded > $peer['downloaded']) {
        $speed_down = ceil(($downloaded - $peer['downloaded']) / (time() - $peer['last_action']));
    }
}

// 下载 / 做种 时间
if (!empty($peer)) {
    $haya_pt_announcetime = time() - $peer['last_action'];
    if ($peer["status"] == "seeder") {
        $haya_pt_peer_announcetime = array(
            'seedtime+' => $haya_pt_announcetime 
        );
    } else {
        $haya_pt_peer_announcetime = array(
            'leechtime+' => $haya_pt_announcetime 
        );
    }
}

// 获取事件
$event = strtolower($event);

// 事件处理
if (!empty($peer) && $event == 'stopped') {
    haya_pt_cache_delete('peer_'.$tid);
    haya_pt_kill_peer($uid, $info_hash);
} elseif (!empty($peer)) {
    haya_pt_cache_delete('peer_'.$tid);
    
    $haya_pt_peer_update_data = array(
        'ip' => $ip,
        'port' => $port,
        'uploaded' => $uploaded,
        'downloaded' => $downloaded,
        'to_go' => $left,
        'prev_action' => $peer['last_action'],
        'last_action' => time(),
        'client' => $agent,
        'speed_up' => $speed_up,
        'speed_down' => $speed_down,
    );
    
    if ($event == 'completed') {		
        $haya_pt_completed_file = array(
            "finished+" => 1
        );
        
        $haya_pt_completed_history = array(
            'completedat' => time(),
            'finished' => 'yes',
        );

        $haya_pt_peer_update_data = $haya_pt_peer_update_data + array(
            'status' => 'seeder',
            'finishedat' => time(),
        );
    }
    
    haya_pt_peers_update_by_uid_and_infohash($uid, $info_hash, $haya_pt_peer_update_data);
} else {
    if ($left == 0) {
        $status = "seeder";
    } else {
        $status = "leecher";
    }
    
    $sockres = @pfsockopen($ip, $port, $errno, $errstr, 5);
    if (!$sockres) {
        $connectable = "no";
    } else {
        $connectable = "yes";
        @fclose($sockres);
    }
    
    haya_pt_peers__create(array(
        'uid' => $uid,
        'tid' => $tid,
        'infohash' => $info_hash,
        'peer_id' => $peer_id,
        'ip' => $ip,
        'port' => $port,
        'uploaded' => $uploaded,
        'downloaded' => $downloaded,
        'to_go' => $left,
        'status' => $status,
        'started' => time(),
        'last_action' => time(),
        'prev_action' => 0,
        'connectable' => $connectable,
        'client' => $agent,
        'finishedat' => 0,
        'downloadoffset' => $downloaded,
        'uploadoffset' => $uploaded,
        'speed_up' => 0,
        'speed_down' => 0,
        'create_date' => time(),
        'create_ip' => $longip,
    ));
}

// 记录到历史
$haya_pt_history = haya_pt_history_read_by_uid_and_infohash($uid, $info_hash);
if ($event == 'stopped') {
    $haya_pt_history_active = 'no';
} else {
    $haya_pt_history_active = 'yes';
}
if (empty($haya_pt_history)) {
    $haya_pt_history_data = array(
        'uid' => $uid,
        'infohash' => $info_hash,
        'realdown' => $new_download_true,
        'realup' => $new_upload_true,
        'uploaded' => $new_upload,
        'downloaded' => $new_download,
        'active' => $haya_pt_history_active,
        'client' => $agent,
        'makedate' => time(),
        'tid' => $tid,
        'date' => time(),
        'key' => $key,
        'key_up' => $new_upload_true,
        'key_down' => $new_download_true,
        'create_date' => time(),
        'create_ip' => $longip,
    );
    
    if (isset($haya_pt_completed_history)) {
        $haya_pt_history_data = $haya_pt_history_data + $haya_pt_completed_history;
    }
    
    if (isset($haya_pt_peer_announcetime)) {
        $haya_pt_history_data = $haya_pt_history_data + $haya_pt_peer_announcetime;
    }
    
    haya_pt_history__create($haya_pt_history_data);
} else {
    if ($key != $haya_pt_history['key']) {
        $haya_pt_history_key_up = 0;
        $haya_pt_history_key_down = 0;
    } else {
        $haya_pt_history_key_up = $new_upload_true;
        $haya_pt_history_key_down = $new_download_true;
    }
    
    // 历史记录更新
    if (!empty($peer)) {
        $haya_pt_history_update = array(
            'uploaded+' => $new_upload,
            'realup+' => $new_upload_true,
            'downloaded+' => $new_download,
            'realdown+' => $new_download_true,
            'active' => $haya_pt_history_active,
            'client' => $agent,
            'tid' => $tid,
            'date' => time(),
            'key' => $key,
            'key_up+' => $haya_pt_history_key_up,
            'key_down+' => $haya_pt_history_key_down,
        );
        
        if (isset($haya_pt_completed_history)) {
            $haya_pt_history_update = $haya_pt_history_update + $haya_pt_completed_history;
        }
        
        if (isset($haya_pt_peer_announcetime)) {
            $haya_pt_history_update = $haya_pt_history_update + $haya_pt_peer_announcetime;
        }
        
        haya_pt_history_update_by_uid_and_infohash($uid, $info_hash, $haya_pt_history_update);
    } else {
        haya_pt_history_update_by_uid_and_infohash($uid, $info_hash, array(
            'active' => $haya_pt_history_active,
            'client' => $agent,
            'tid' => $tid,
            'date' => time(),
            'key' => $key,
        ));		
    }
}

// 更新上传 / 下载的流量
if (!empty($peer)) {
    $haya_pt_flux = 0;
    if ($new_upload > 0) {
        $haya_pt_flux = $haya_pt_flux + $new_upload;
    }
    if ($new_download > 0) {
        $haya_pt_flux = $haya_pt_flux - $new_download;
    }
    haya_pt_users__update($uid, array(
        'flux+' => $haya_pt_flux,
        'downloaded+' => $new_download_true,
        'uploaded+' => $new_upload_true,
        'last_active' => time(),
        'last_ip' => ip(),
    ));
}

// 更新种子文件的 下载流量 / 活动时间
$haya_pt_file_update_data = array(
    'dlbytes+' => $new_download_true,
    'lastactive' => time(),
);
if (isset($haya_pt_completed_file)) {
    $haya_pt_file_update_data = $haya_pt_file_update_data + $haya_pt_completed_file;
}
haya_pt_files__update($info_hash, $haya_pt_file_update_data);

// 发送信息
haya_pt_send_random_peers($tid, $peer_id, $numwant);


/********************* 函数 *********************/

// 删除一个种子
function haya_pt_kill_peer($uid, $hash) {
    haya_pt_peers_delete_by_uid_and_infohash($uid, $hash);
}

// Returns info on one peer //返回种子信息
function haya_pt_get_peer_info($uid, $hash) {
    $data = haya_pt_peers_read_by_uid_and_infohash($uid, $hash);
    if (empty($data)) {
        return false;
    }
    
    return $data;
}

function haya_pt_send_random_peers($tid, $peer_id, $numwant = 30) {
    $haya_pt_peers_cache = haya_pt_cache_get('peer_'.$tid);

    if ($haya_pt_peers_cache['time'] < time()) {
        $real_annnounce_interval = 800;
        $announce_wait = 300;
        
        $result = haya_pt_peers_find_by_rand(array(
            'tid' => $tid
        ), '', 1, $numwant);
        
        $iscompact = haya_pt_iscompact();
        $is_no_peer_id = haya_pt_is_no_peer_id();
        
        $peers = '';
        if (!empty($result)) {
            foreach ($result as $row) {
                
                $row["peer_id"] = str_pad($row["peer_id"], 20);

                // 排除自身
                if ($row["peer_id"] === $peer_id) {
                    continue;
                }
                
                if ($iscompact) {
                    $longip = ip2long($row['ip']);
                    $peers .= pack("Nn", sprintf("%d", $longip), $row["port"]);
                } elseif ($is_no_peer_id) {
                    // 没有peer_id的时候发送
                    $peers[] = array(
                        'ip' => $peer['ip'],
                        'port' => (int)$peer['port'],
                    );
                } else {
                    $peers[] = array(
                        'ip' => $peer['ip'],
                        'peer id' => $peer['peer_id'],
                        'port' => (int)$peer['port'],
                    );
                }
                
            }
        }
        
        $haya_pt_file = haya_pt_files_read_by_tid($tid);
        $seeders = $haya_pt_file['seeds'];
        $leechers = $haya_pt_file['leechers'];

        $output = array(
            'interval' => (int)$real_annnounce_interval,
            'min interval' => (int)$announce_wait,
            'peers' => $peers,
            'complete' => (int)$seeders,
            'incomplete' => (int)$leechers,
        );
        
        $haya_pt_tid_life_time = 0.5 * 60 * 60; // 半小时清空一次缓存
        haya_pt_cache_set('peer_'.$tid, array(
            'data' => $output,
            'time' => time() + $haya_pt_tid_life_time,
        ));
        
    } else {
        $output = $haya_pt_peers_cache['data'];
    }
    
    $resp = BEncode($output);
    haya_pt_response($resp);
}

function haya_pt_show_error($message) {
    $output = BEncode(array(
        'min interval' => (int)1800,
        'failure reason' => (string)$message,
        'warning message' => (string)$message,
    ));
    
    haya_pt_response($output);
}

function haya_pt_response($data = '') {
    ob_clean();
    
    $iscompact = haya_pt_iscompact();
    
    // controll if client can handle gzip 如果客户端支持Gzip
    if (true) {
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") 
            && extension_loaded('zlib') 
            && ini_get("zlib.output_compression") == 0
        ) {
            if (ini_get('output_handler') != 'ob_gzhandler' 
                && !$iscompact
            ){
                ob_start("ob_gzhandler");
            } else{
                ob_start();
            }
        } else {
            ob_start();
        }
    }

    header("Content-type: text/plain");
    header("Pragma: no-cache");

    die($data);	
}

function haya_pt_iscompact() {
    $iscompact = (isset($_GET["compact"]) ? $_GET["compact"] == '1' : false);

    return $iscompact;
}

function haya_pt_is_no_peer_id() {
    $is_no_peer_id = (isset($_GET["no_peer_id"]) ? $_GET["no_peer_id"] == '1' : false);

    return $is_no_peer_id;
}

function haya_pt_getip() {
    if (isset($_SERVER["HTTP_X_REAL_IP"])) {
        return $_SERVER["HTTP_X_REAL_IP"];
    }
    if (getenv('HTTP_CLIENT_IP') && long2ip(ip2long(getenv('HTTP_CLIENT_IP')))==getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')))
        return getenv('HTTP_CLIENT_IP');
    if (getenv('HTTP_X_FORWARDED_FOR') && long2ip(ip2long(getenv('HTTP_X_FORWARDED_FOR')))==getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')))
        return getenv('HTTP_X_FORWARDED_FOR');
    if (getenv('HTTP_X_FORWARDED') && long2ip(ip2long(getenv('HTTP_X_FORWARDED')))==getenv('HTTP_X_FORWARDED') && validip(getenv('HTTP_X_FORWARDED')))
        return getenv('HTTP_X_FORWARDED');
    if (getenv('HTTP_FORWARDED_FOR') && long2ip(ip2long(getenv('HTTP_FORWARDED_FOR')))==getenv('HTTP_FORWARDED_FOR') && validip(getenv('HTTP_FORWARDED_FOR')))
        return getenv('HTTP_FORWARDED_FOR');
    if (getenv('HTTP_FORWARDED') && long2ip(ip2long(getenv('HTTP_FORWARDED')))==getenv('HTTP_FORWARDED') && validip(getenv('HTTP_FORWARDED')))
        return getenv('HTTP_FORWARDED');
    return long2ip(ip2long($_SERVER['REMOTE_ADDR']));
}

?>