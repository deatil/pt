<?php

/**
 * plugin-pt
 *
 * PT服务端
 * 
 * @author deatil
 * @create 2018-4-5
 */

defined('DEBUG') OR exit('Forbidden');

$header['title'] = 'BAN - PT';

$pagesize = 10;
$srchtype = param(2);
$keyword  = trim(xn_urldecode(param(3)));
$page     = param(4, 1);

$where = array();
if ($keyword) {
	if (in_array($srchtype, array(
		'ban_uid', 
	))) {
		$where[$srchtype] = array('LIKE' => $keyword); 
	}	
}

$total = haya_pt_banlist__count($where);
$haya_pt_banlist = haya_pt_banlist_find($where, array('ban_id' => -1), $page, $pagesize);

$pagination = pagination(url("pt-banlist-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

include _include(haya_pt_plugin_view('htm/admin/banlist.htm'));

?>