<?php exit;

$haya_pt_thread = thread__read($tid);
$haya_pt_open_upload_forum = haya_pt_check_upload_forum($haya_pt_thread['fid']);
if (!$haya_pt_open_upload_forum) {
	$haya_pt_upload_forum_tip = trim($haya_pt_config['upload_forum_tip']);
	message(1, $haya_pt_upload_forum_tip);
}

$haya_pt_open_user_download_group = haya_pt_check_user_download_group($gid);
if (!$haya_pt_open_user_download_group) {
	$haya_pt_download_group = array();
	if (!empty($grouplist)) {
		$haya_pt_user_download_group = $haya_pt_config['user_download_group'];
		foreach ($grouplist as $_gid => $_group) {
			if (in_array($_gid, $haya_pt_user_download_group)) {
				$haya_pt_download_group[] = $_group['name'];
			}
		}
	}
	
	$haya_pt_user_download_group_tip = trim($haya_pt_config['user_download_group_tip']);
	
	$haya_pt_user_download_group_tip = str_replace(
		array('{user_group}', '{download_group}'), 
		array($group['name'], implode('，', $haya_pt_download_group)), 
		$haya_pt_user_download_group_tip
	);	
	
	message(1, $haya_pt_user_download_group_tip);
}

?>