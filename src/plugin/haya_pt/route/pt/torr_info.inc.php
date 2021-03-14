<?php 

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-3-31
 */

defined('DEBUG') OR exit('Forbidden');

if (empty($user)) {
	message(1, '请登录');
}

$tid = param('tid', 0);
if (empty($tid)) {
	message(1, '参数错误');
}

$results = haya_pt_files_read_by_tid($tid);
if (empty($results)) {
	message(1, "该种子可能已被删除");
}

$btfilename = haya_pt_plugin_torrent($results['url']); // 存储目录

$fd = fopen($btfilename, "rb");
if (!$fd) {
	message(1, "该种子可能已被删除");
}
$alltorrent = fread($fd, filesize($btfilename));
fclose($fd);
$array = BDecode($alltorrent);

if (isset($array["info"])) {
	if (isset($array["creation date"])) {
		if (is_numeric($array["creation date"])) {
			$date = date("Y 年 m 月 d 日", $array["creation date"]);
		} else {
			$date = $array["creation date"];
		}
	}
	
	$info = $array["info"]; 
	$a_files = "";
	$files = "";
	$allsize = 0;
	if (isset($info["files"])) { // 多个文件时
		foreach ($info["files"] as $file) {
			if (isset($file["path"][1])) {
				$files .= $file["path"][0];
				for ($i = 1; isset($file["path"][$i]); $i++) {
					$files .= "/".$file["path"][$i];
					$a_files .= "<tr><td>".$file["path"][0]."/".$file["path"][$i]."</td>";
				}
			} else {
				$files .= $file["path"][0];
				$a_files .= "<tr><td>".$file["path"][0]."</td>";
			}
			$files .= "<font color=''>(".haya_pt_sizecount($file["length"]).")</font><br />";
			$a_files .= "<td>".haya_pt_sizecount($file["length"])."</td></tr>";
			$allsize = $allsize + $file["length"];
		}
	} else {
		// 单个文件时 haya_pt_substr($results['filename'], 40);
		$files = $info["name"]."&nbsp;&nbsp;&nbsp;<font color='blue'>(".haya_pt_sizecount($info["length"]).")</font>";
		$a_files .= "<tr><td>".haya_pt_substr($info["name"], 20)."</td><td>".haya_pt_sizecount($info["length"])."</td><tr>";
		$info["name"] = "";
		$allsize = $info["length"];
	}

	$allsize = haya_pt_sizecount($allsize);
}

$a_date = date("Y-m-d H:i:s", $array["creation date"]);

include haya_pt_plugin_view('htm/torrent/torr_info.htm');

?>