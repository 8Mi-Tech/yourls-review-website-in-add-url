<?php
// 定义表名
$tables = array(
    "reviewurl_system_blacklist_url",
    "reviewurl_system_whitelist_url",
    "reviewurl_custom_blacklist_url",
    "reviewurl_custom_whitelist_url",
);
// 创建表函数
function create_db_table($table){
    global $tables;
    if(!in_array($table, $tables)){
        return false;
    }
    if(!yourls_db_table_exists($table)){
        yourls_create_db_table($table);
    }
}
?>