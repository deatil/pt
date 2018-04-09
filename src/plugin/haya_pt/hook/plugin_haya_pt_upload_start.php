<?php exit;

$haya_pt_fid = param('fid', 0);
if (empty($haya_pt_fid)) {
	message(1, '参数错误');
}

$haya_pt_open_upload_forum = haya_pt_check_upload_forum($haya_pt_fid);
if (!$haya_pt_open_upload_forum) {
	$haya_pt_upload_forum_tip = trim($haya_pt_config['upload_forum_tip']);
	message(1, $haya_pt_upload_forum_tip);
}

$haya_pt_open_user_upload_group = haya_pt_check_user_upload_group($gid);
if (!$haya_pt_open_user_upload_group) {
	$haya_pt_upload_group = array();
	if (!empty($grouplist)) {
		$haya_pt_user_upload_group = $haya_pt_config['user_upload_group'];
		foreach ($grouplist as $_gid => $_group) {
			if (in_array($_gid, $haya_pt_user_upload_group)) {
				$haya_pt_upload_group[] = $_group['name'];
			}
		}
	}
	
	$haya_pt_user_upload_group_tip = trim($haya_pt_config['user_upload_group_tip']);
	
	$haya_pt_user_upload_group_tip = str_replace(
		array('{user_group}', '{upload_group}'), 
		array($group['name'], implode('，', $haya_pt_upload_group)), 
		$haya_pt_user_upload_group_tip
	);	
	
	message(1, $haya_pt_user_upload_group_tip);
}

?>