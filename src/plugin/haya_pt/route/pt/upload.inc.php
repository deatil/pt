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

if (!function_exists("sha1")) {
	message(1, 'SHA扩展不存在');
}

$user = user_read($uid);
user_login_check();

$width = param('width', 0);
$height = param('height', 0);
$is_image = param('is_image', 0);
$name = param('name');
$data = param('data', '', FALSE, FALSE);

if (empty($data)) {
	message(-1, '上传错误');
}

$data = base64_decode_file_data($data);

$ext = file_ext($name, 7);
if (strtolower($ext) != 'torrent') {
	message(-1, '后缀名错误');
}

$alltorrent = $data;
$array = BDecode($alltorrent);
if (!isset($array)) {
	message(-1, '上传文件错误');
}
if (!$array){
	message(-1, '上传文件格式错误');
}

// hook plugin_haya_pt_upload_start.php

$array["info"]["private"] = 1;
$hash = sha1(BEncode($array["info"]));

if (isset($hash) && $hash){
	$url = $hash . ".btf";
} else {
	$url = 0;
}

if (isset($array["info"]) && $array["info"]) {
	$upfile = $array["info"];
} else {
	$upfile = 0;
}

if (!isset($array["announce"])){
	message(-1, '种子文件丢失种子服务器地址');
}

$haya_pt_file = haya_pt_files_read_by_info_hash($hash);
if (!empty($haya_pt_file) && $haya_pt_file['tid'] != 0) {
	message(-1, '该资源已经被分享');
}

$filesize = strlen($data);
if ($filesize > 20480000) {
	message(-1, '上传文件过大');
}

sess_restart();

if (empty($haya_pt_file)) {

	$torrent_file = haya_pt_plugin_torrent($hash . ".btf");
	$mf = file_put_contents($torrent_file, $data);
	if (!$mf) {
		haya_pt_files_delete_by_info_hash($hash);

		message(-1, '上传文件失败');
	}

	@chmod($torrent_file, 0766);
	
	$filesize = filesize($torrent_file);

	if (isset($upfile["length"])) {
		$size = (float)($upfile["length"]);
	} elseif (isset($upfile["files"])){
		// multifiles torrent
		$size = 0;
		foreach ($upfile["files"] as $file){
			$size += (float)($file["length"]);
		}
	} else {
		$size = "0";
	}

	$status = haya_pt_files__create(array(
		'info_hash' => $hash,
		'tid' => 0,
		'filename' => $name,
		'url' => $url,
		'date' => time(),
		'size' => $size,
		'filesize' => $filesize,
		'anonymous' => 'false',
		'dlbytes' => '0',
		'seeds' => '0',
		'leechers' => '0',
		'finished' => '0',
		'lastactive' => time(),
		'type' => '5',
		'create_date' => time(),
		'create_ip' => $longip,
	));

	if ($status === false) {
		@unlink($torrent_file);
		
		message(-1, '种子文件上传失败');
	}
} else {
	haya_pt_files__update($hash, array(
		'tid' => 0,
		'filename' => $name,
		'url' => $url,
		'date' => '0000-00-00 00:00:00',
		'filesize' => $filesize,
		'anonymous' => 'false',
		'dlbytes' => '0',
		'seeds' => '0',
		'leechers' => '0',
		'finished' => '0',
		'lastactive' => time(),
		'type' => '5',
	));
}

// hook plugin_haya_pt_upload_end.php

message(0, array(
	'hash' => $hash,
	'name' => htmlspecialchars_decode($name),
	'message' => '上传成功',
));

?>