<?php
exit;

$haya_pt_config_menu = array(
	'pt' => array(
		'url' => url('pt-files'), 
		'text' => 'PT', 
		'icon' => 'icon-file',
		'tab' => array (
		)
	),
);

$menu = array_merge($menu, $haya_pt_config_menu);

?>