<?php

defined('DEBUG') OR exit('Forbidden');

$tablepre = $db->tablepre;

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_files` (
  `fid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `info_hash` varchar(40) NOT NULL DEFAULT '',
  `tid` int(8) NOT NULL,
  `filename` varchar(250) NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `date` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `filesize` bigint(20) NOT NULL DEFAULT '0',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `anonymous` enum('true','false') NOT NULL DEFAULT 'false',
  `dlbytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `seeds` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '种子数',
  `leechers` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载中',
  `finished` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成数',
  `downloads` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载数',
  `lastactive` int(10) NOT NULL DEFAULT '0' COMMENT '最近活动时间',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '权值类型，下载权值：1-0，2-0.2，3-0.5，4-0.8，5-1',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  PRIMARY KEY (`info_hash`),
  KEY `fid` (`fid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_history` (
  `uid` int(10) DEFAULT NULL,
  `tid` int(10) NOT NULL DEFAULT '0',
  `infohash` varchar(40) NOT NULL DEFAULT '',
  `makedate` int(10) DEFAULT NULL COMMENT '创建时间',
  `date` int(10) DEFAULT NULL COMMENT '最近更新时间',
  `active` enum('yes','no') NOT NULL DEFAULT 'no',
  `client` varchar(60) NOT NULL DEFAULT '',
  `uploaded` bigint(20) NOT NULL DEFAULT '0' COMMENT '计算上的已上传',
  `downloaded` bigint(20) NOT NULL DEFAULT '0' COMMENT '计算上的已下载',
  `realdown` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '真实下载量',
  `realup` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '真实上传量',
  `seedtime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `leechtime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key` varchar(8) NOT NULL DEFAULT '' COMMENT '客户端KEY值',
  `key_up` bigint(20) NOT NULL DEFAULT '0' COMMENT '计算KEY的已上传',
  `key_down` bigint(20) NOT NULL DEFAULT '0' COMMENT '计算KEY的已下载',
  `completedat` int(10) DEFAULT NULL COMMENT '完成时间',
  `finished` enum('yes','no') NULL DEFAULT 'no',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  KEY `tid_uid` (`tid`, `uid`),
  UNIQUE KEY `uid` (`uid`,`infohash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_peers` (
  `uid` int(10) NOT NULL,
  `tid` int(9) NOT NULL,
  `infohash` varchar(40) NOT NULL DEFAULT '',
  `peer_id` binary(20) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT 'error.x',
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `to_go` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` enum('leecher','seeder') NOT NULL DEFAULT 'leecher',
  `started` int(10) unsigned NOT NULL DEFAULT '0',
  `last_action` int(10) unsigned NOT NULL DEFAULT '0',
  `prev_action` int(10) unsigned NOT NULL DEFAULT '0',
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `client` varchar(60) NOT NULL DEFAULT '',
  `finishedat` int(10) unsigned NOT NULL DEFAULT '0',
  `downloadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uploadoffset` bigint(20) unsigned NOT NULL DEFAULT '0',
  `speed_up` bigint(20) NOT NULL DEFAULT '0' COMMENT '上传速度',
  `speed_down` bigint(20) NOT NULL DEFAULT '0' COMMENT '下载速度',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  KEY `ip` (`ip`),
  KEY `tid_uid` (`tid`, `uid`),
  KEY `uid_infohash` (`uid`, `infohash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_users` (
  `uid` int(10) unsigned NOT NULL,
  `pid` varchar(32) NOT NULL DEFAULT '',
  `flux` bigint(20) DEFAULT '0',
  `downloaded` bigint(20) DEFAULT '0',
  `uploaded` bigint(20) DEFAULT '0',
  `last_active` int(10) NOT NULL DEFAULT '0' COMMENT '最近活动时间',
  `last_ip` varchar(50) NOT NULL DEFAULT 'error.x',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  PRIMARY KEY (`uid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_banlist` (
  `ban_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ban_uid` int(10) NOT NULL DEFAULT '0',
  `ban_ip` varchar(50) NOT NULL DEFAULT '0',
  `ban_email` varchar(255) NOT NULL DEFAULT '',
  `action_uid` int(10) NOT NULL DEFAULT '0',
  `action_date` int(10) NOT NULL DEFAULT '0' COMMENT '操作的时间',
  `action_ip` int(10) NULL DEFAULT '0' COMMENT '操作的IP',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  KEY `ban_id` (`ban_id`),
  KEY `ban_ip_user_id` (`ban_ip`, `ban_uid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$tablepre}pt_log` (
  `lid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) NOT NULL DEFAULT '0',
  `action` varchar(10) NOT NULL DEFAULT 'add',
  `type` varchar(15) NOT NULL DEFAULT 'credits',
  `money` bigint(20) NOT NULL DEFAULT '0',
  `flux` bigint(20) NOT NULL DEFAULT '0',
  `content` text NOT NULL DEFAULT '',
  `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
  `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
  PRIMARY KEY (`lid`),
  KEY `uid` (`uid`),
  KEY `action_type` (`action`, `type`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 ROW_FORMAT=DYNAMIC;
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TRIGGER `after_peers_insert` 
AFTER INSERT ON `{$tablepre}pt_peers` 
FOR EACH ROW 
BEGIN 
	UPDATE {$tablepre}pt_files 
	SET leechers = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='leecher' 
			AND infohash=new.infohash
	),
	seeds = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='seeder' 
			AND infohash=new.infohash
	) 
	where info_hash=new.infohash;
END
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TRIGGER `after_peers_delete` 
AFTER DELETE ON `{$tablepre}pt_peers` 
FOR EACH ROW 
BEGIN 
	UPDATE {$tablepre}pt_files 
	SET leechers = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='leecher' 
			AND infohash=old.infohash
	),
	seeds = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='seeder' 
			AND infohash=old.infohash
	) 
	WHERE info_hash=old.infohash;
END
EOF;
db_exec($sql);

$sql = <<<EOF
CREATE TRIGGER `after_peers_update` 
AFTER UPDATE ON `{$tablepre}pt_peers` 
FOR EACH ROW 
BEGIN 
	UPDATE {$tablepre}pt_files 
	SET leechers = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='leecher' 
			AND infohash=old.infohash
	),
	seeds = ( 
		SELECT COUNT(*) 
		FROM {$tablepre}pt_peers 
		WHERE status='seeder' 
			AND infohash=old.infohash
	) 
	WHERE info_hash=old.infohash;
END
EOF;
db_exec($sql);

// 添加插件配置 
$haya_pt_config = array(
	'tracker_url' => url('pt-announce', array('pid' => '__pid__')), //tracker地址
	'help_url' => 'help.htm', //做种教程地址，在发帖页面显示，请自行修改
	'website' => '[PT]', // 网站名称
	'connect_ip_type' => 'all', // ipv4-ipv4，ipv4-ipv6，all-ipv4/ipv6
	
	'open_upload_forum' => 0,
	'upload_forum' => '',
	'upload_forum_tip' => '该板块不允许添加资源！',
	
	'open_user_upload_group' => 0,
	'user_upload_group' => '',
	'user_upload_group_tip' => '允许添加资源用户组【{upload_group}】，你当前所属用户组【{user_group}】不能添加资源！',
	
	'open_user_download_group' => 0,
	'user_download_group' => '',
	'user_download_group_tip' => '允许下载资源用户组【{download_group}】，你当前所属用户组【{user_group}】不能下载资源！',
	
	'open_user_exchange_flux_group' => 0,
	'user_exchange_flux_group' => '',
	'user_exchange_flux_group_tip' => '允许兑换流量用户组【{exchange_group}】，你当前所属用户组【{user_group}】不能兑换流量！',
	'exchange_flux_money' => 20,
	
	'open_user_gift_flux' => 0,
);
kv_set('haya_pt', $haya_pt_config); 


?>