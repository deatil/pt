<?php exit;

if ($haya_pt_open_upload_forum && $haya_pt_open_user_upload_group) {

	haya_pt_files__update($torrent, array(
		'tid' => $tid,
		'lastactive' => time(),
	));
	
}


?>