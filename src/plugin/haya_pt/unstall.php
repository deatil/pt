<?php
 
defined('DEBUG') OR exit('Forbidden');

$tablepre = $db->tablepre;

db_exec("DROP TABLE IF EXISTS {$tablepre}pt_files;");
db_exec("DROP TABLE IF EXISTS {$tablepre}pt_history;");
db_exec("DROP TABLE IF EXISTS {$tablepre}pt_peers;");
db_exec("DROP TABLE IF EXISTS {$tablepre}pt_users;");
db_exec("DROP TABLE IF EXISTS {$tablepre}pt_banlist;");
db_exec("DROP TABLE IF EXISTS {$tablepre}pt_log;");

// 删除插件配置
kv_delete('haya_pt'); 
