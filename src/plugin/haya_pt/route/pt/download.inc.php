<?php 

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-4-5
 */
 
defined('DEBUG') OR exit('Forbidden');

$user = user_read($uid);
user_login_check();

$tid = param(2, 0);
if (empty($tid)) {
	message(1, '参数错误');
}

// hook plugin_haya_pt_download_start.php

$results = haya_pt_files_read_by_tid($tid);
if (empty($results)) {
	message(1, '该种子不存在');
}

haya_pt_files_update_by_tid($tid, array(
	'downloads+' => 1
));

$filepath = haya_pt_plugin_torrent($results['url']);

if (isset($haya_pt_config['website']) && !empty($haya_pt_config['website'])) {
	$haya_pt_torrent = trim($haya_pt_config['website']) . $results['filename'];
} else {
	$haya_pt_torrent = $results['filename'];
}

$f = urldecode(iconv('utf-8', 'GB2312', $haya_pt_torrent));
$fd = fopen($filepath, "rb");
$alltorrent = fread($fd, filesize($filepath));
$array = BDecode($alltorrent);
fclose($fd);

$haya_pt_user_tracker = haya_pt_get_user_tracker($uid);

$array["announce"] = htmlspecialchars_decode($haya_pt_user_tracker);
if (isset($array["announce-list"]) && is_array($array["announce-list"])){
	for ($i = 0; $i < count($array["announce-list"]); $i++) {
		if ($i == 0) {
			$array["announce-list"][$i][0] = $array["announce"];
		} else {
			$array["announce-list"][$i][0] = "";
		}
	}
}

$alltorrent = BEncode($array);

ob_clean();
header("Content-Type: application/x-bittorrent");
header('Content-Disposition: attachment; filename="'.$f.'"');
print($alltorrent);

?>