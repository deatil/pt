<?php
exit;

// 种子数的计划任务
$haya_pt_peers_cron_time = 300;

$haya_pt_cron_1_last_date = runtime_get('haya_pt_cron_1_last_date');
$haya_pt_t = $time - $haya_pt_cron_1_last_date;
if ($haya_pt_t > $haya_pt_peers_cron_time || $force) {
	$lock = cache_get('haya_pt_cron_lock_1');
	if ($lock === NULL) {
		cache_set('haya_pt_cron_lock_1', 1, 10); // 设置 10 秒超时

		haya_pt_peers__delete(array(
			'last_action' => array('<' => time() - 60*60)
		));
		
		// hook haya_pt_cron_peers_end.php
		
		runtime_set('haya_pt_cron_1_last_date', $time);

		cache_delete('haya_pt_cron_lock_1');
	}
}


// 种子文件的计划任务
$haya_pt_files_cron_time = 86400;

$haya_pt_cron_2_last_date = runtime_get('haya_pt_cron_2_last_date');
$haya_pt_t = $time - $haya_pt_cron_2_last_date;
if ($haya_pt_t > $haya_pt_files_cron_time || $force) {
	
	$lock = cache_get('haya_pt_cron_lock_2'); 
	if ($lock === NULL) {
		cache_set('haya_pt_cron_lock_2', 1, 10); // 设置 10 秒超时

		haya_pt_delete_torrent_by_tids(0);
		
		list($y, $n, $d) = explode(' ', date('Y n j', $time)); 	// 0 点
		$today = mktime(0, 0, 0, $n, $d, $y);			// -8 hours
		runtime_set('haya_pt_cron_2_last_date', $today, TRUE);		// 加到1天后
	
		// hook haya_pt_cron_files_end.php
		
		cache_delete('haya_pt_cron_lock_2');
	}
}

?>