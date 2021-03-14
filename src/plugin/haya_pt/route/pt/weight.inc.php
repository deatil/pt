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

if ($method == 'GET') {
	
	include haya_pt_plugin_view('htm/torrent/weight.htm');
	
} else {
	$haya_pt_type = param('type', 5);
	if (empty($haya_pt_type)) {
		message(-1, '请选择一个下载权值');
	}
	
	if (!in_array($haya_pt_type, array(1, 2, 3, 4, 5))) {
		$haya_pt_type = 5;
	}

	$tidarr = param('tidarr', array(0));
	if (empty($tidarr)) {
		message(-1, '请至少选择一个主题');
	}
	
	$haya_pt_threadlist = thread_find_by_tids($tidarr);
	
	// hook plugin_haya_pt_weight_start.php
	
	$haya_pt_merge_tids_arr = array();
	if (!empty($haya_pt_threadlist)) {
		foreach ($haya_pt_threadlist as $haya_pt_thread_one) {
			$haya_pt_fid = $haya_pt_thread_one['fid'];
			$haya_pt_tid = $haya_pt_thread_one['tid'];
			
			if (forum_access_mod($haya_pt_fid, $gid, 'allowtop')) {
				haya_pt_files_update_by_tid($haya_pt_tid, array(
					'type' => $haya_pt_type,
				));
			}
		}
	}
	
	// hook plugin_haya_pt_weight_end.php

	message(0, '下载权值更改成功');
	
}

?>