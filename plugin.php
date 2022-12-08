<?php 
/*
Plugin Name: Review Website in add URL
Plugin URI: https://github.com/8Mi-Tech/yourls-review-website-in-add-url
Description: One line description of your plugin
Version: 1.0
Author: 8Mi-Tech
Author URI: https://8mi.ink
*/

//建立4个表
function yourls_review_website_in_add_url_db_tables() {
 global $ydb;
 $ydb->query("CREATE TABLE IF NOT EXISTS `".YOURLS_DB_PREFIX."system_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
 
 $ydb->query("CREATE TABLE IF NOT EXISTS `".YOURLS_DB_PREFIX."system_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  
 $ydb->query("CREATE TABLE IF NOT EXISTS `".YOURLS_DB_PREFIX."custom_blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
 
 $ydb->query("CREATE TABLE IF NOT EXISTS `".YOURLS_DB_PREFIX."custom_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
}

// 注册激活插件
yourls_add_action( 'activated_yourls-review-website-in-add-url/plugin.php', 'yourls_review_website_in_add_url_db_tables' );
// 添加插件hook，监听add link事件
yourls_add_action('pre_add_url', 'review_website_in_add_url');

// 定义插件函数
function review_website_in_add_url($args) {
    global $yourls_db;
	
	// 定义黑白名单数组
    $blacklist = array("custom_blacklist", "system_blacklist");
    $whitelist = array("custom_whitelist", "system_whitelist");

    // 获取传入的链接参数
    $url = $args[0];

    // 查询黑白名单
    foreach ($blacklist as $bl) {
        $sql = "SELECT * FROM $bl WHERE url = '$url'";
        $check_blacklist = $yourls_db->get_results($sql);
        if (!empty($check_blacklist)) {
            // 如果在黑名单中，则禁止生成短连接
            die("禁止生成短连接!");
        }
    }

    foreach ($whitelist as $wl) {
        $sql = "SELECT * FROM $wl WHERE url = '$url'";
        $check_whitelist = $yourls_db->get_results($sql);
        if (!empty($check_whitelist)) {
            // 如果在白名单中，则允许生成短连接
            return;
        }
    }

    // 检查网站是否有色情、成人内容、六合彩、赌博等违法元素
    // TODO: 此处应该实现对网站内容的检查
    $check_result = check_url($url);
    if ($check_result) {
        // 如果有违法元素，则自动加入system_blacklist，并禁止生成短链接
        $sql = "INSERT INTO system_blacklist (url) VALUES ('$url')";
        $yourls_db->query($sql);
        die("禁止生成短连接!");
    } else {
        // 如果没有违法元素，则自动加入system_whitelist，并允许生成短链接
        $sql = "INSERT INTO system_whitelist (url) VALUES ('$url')";
        $yourls_db->query($sql);
        return;
    }

}

// 定义检查网站是否有违法元素的函数
function check_url($url) {
    // TODO: 此处应该实现对网站内容的检查
    return false;
}
