<?php exit;

if (!(isset($haya_pt_config['open_user_exchange_flux_group'])
	&& $haya_pt_config['open_user_exchange_flux_group'] == 1
)) {
	message(1, jump('没有启用兑换流量', url('pt-shop')));
}

$haya_pt_check_user_exchange_flux_group = haya_pt_check_user_exchange_flux_group($gid);
if (!$haya_pt_check_user_exchange_flux_group) {
	$haya_pt_exchange_flux_group = array();
	if (!empty($grouplist)) {
		$haya_pt_user_exchange_flux_group = $haya_pt_config['user_exchange_flux_group'];
		foreach ($grouplist as $_gid => $_group) {
			if (in_array($_gid, $haya_pt_user_exchange_flux_group)) {
				$haya_pt_exchange_flux_group[] = $_group['name'];
			}
		}
	}
	
	$haya_pt_user_exchange_flux_group_tip = trim($haya_pt_config['user_exchange_flux_group_tip']);
	
	$haya_pt_user_exchange_flux_group_tip = str_replace(
		array('{user_group}', '{exchange_group}'), 
		array($group['name'], implode('，', $haya_pt_exchange_flux_group)), 
		$haya_pt_user_exchange_flux_group_tip
	);	
	
	message(1, jump($haya_pt_user_exchange_flux_group_tip, url('pt-shop')));
}

?>