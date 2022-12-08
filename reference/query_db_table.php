<?php
// 创建函数
function query_db_table($table_name){
	// 定义变量
	$results = array();

	// 检查表是否存在
	if(YOURLS_DB::table_exists($table_name)){
		// 获取表的内容
		$results = YOURLS_DB::get_results("SELECT * FROM $table_name");
	}
	
	// 返回数据
	return $results;
}

// 使用函数
// $blacklist_urls = query_db_table("reviewurl_system_blacklist_url");
?>