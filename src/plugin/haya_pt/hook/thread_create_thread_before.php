<?php exit;

$haya_pt_open_upload_forum = haya_pt_check_upload_forum($fid);
$haya_pt_open_user_upload_group = haya_pt_check_user_upload_group($gid);

if ($haya_pt_open_upload_forum && $haya_pt_open_user_upload_group) {

	$torrent = param('torrent');
	if (empty($torrent)) {
		message(-1, '种子文件不能为空');
	}
	
}


?>