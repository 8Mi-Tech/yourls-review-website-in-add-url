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

// 检查URL是否在白名单或黑名单
function yourls_review_website_in_add_url ($url) {
 // 定义白名单和黑名单
 $custom_blacklist = yourls_get_custom_blacklist();
 $custom_whitelist = yourls_get_custom_whitelist();
 $system_blacklist = yourls_get_system_blacklist();
 $system_whitelist = yourls_get_system_whitelist();
 
 // 检查URL
 if (in_array($url, $custom_blacklist) || in_array($url, $system_blacklist)) {
  // 如果URL在黑名单，禁止生成短连接
  return false;
 }
 elseif (in_array($url, $custom_whitelist) || in_array($url, $system_whitelist)) {
  // 如果URL在白名单，允许生成短链接
  return true;
 }
 else {
  // 检查URL是否有暴力，色情，成人内容，六合彩，赌博等违法元素
  $check_result = yourls_review_website_in_add_url_check($url);
  
  if ($check_result == 1) {
   // 如果有违法元素，自动加入system_blacklist，禁止生成短连接
   yourls_add_system_blacklist($url);
   return false;
  }
  elseif ($check_result == 0) {
   // 如果没有违法元素，自动加入system_whitelist，允许生成短链接
   yourls_add_system_whitelist($url);
   return true;
  }
 }
}

// 检查URL是否有暴力，色情，成人内容，六合彩，赌博等违法元素
function yourls_review_website_in_add_url_check($url) {
 // 检查URL
 $result = yourls_check_url_contents($url);
 
 if ($result == 1) {
  // 如果有违法元素，返回1
  return 1;
 }
 else {
  // 如果没有违法元素，返回0
  return 0;
 }
}

// 添加URL到黑名单
function yourls_add_system_blacklist($url) {
 global $ydb;
 $sql = "INSERT INTO `".YOURLS_DB_PREFIX."system_blacklist` (`url`) VALUES ('$url');";
 $ydb->query($sql);
}

// 添加URL到白名单
function yourls_add_system_whitelist($url) {
 global $ydb;
 $sql = "INSERT INTO `".YOURLS_DB_PREFIX."system_whitelist` (`url`) VALUES ('$url');";
 $ydb->query($sql);
}

// 获取用户自定义黑名单
function yourls_get_custom_blacklist() {
 global $ydb;
 $sql = "SELECT url FROM `".YOURLS_DB_PREFIX."custom_blacklist`;";
 $results = $ydb->get_results($sql);
 $blacklist = array();
 foreach ($results as $result) {
  array_push($blacklist, $result->url);
 }
 return $blacklist;
}

// 获取用户自定义白名单
function yourls_get_custom_whitelist() {
 global $ydb;
 $sql = "SELECT url FROM `".YOURLS_DB_PREFIX."custom_whitelist`;";
 $results = $ydb->get_results($sql);
 $whitelist = array();
 foreach ($results as $result) {
  array_push($whitelist, $result->url);
 }
 return $whitelist;
}

// 获取系统黑名单
function yourls_get_system_blacklist() {
 global $ydb;
 $sql = "SELECT url FROM `".YOURLS_DB_PREFIX."system_blacklist`;";
 $results = $ydb->get_results($sql);
 $blacklist = array();
 foreach ($results as $result) {
  array_push($blacklist, $result->url);
 }
 return $blacklist;
}

// 获取系统白名单
function yourls_get_system_whitelist() {
 global $ydb;
 $sql = "SELECT url FROM `".YOURLS_DB_PREFIX."system_whitelist`;";
 $results = $ydb->get_results($sql);
 $whitelist = array();
 foreach ($results as $result) {
  array_push($whitelist, $result->url);
 }
 return $whitelist;
}

// 注册插件到action
yourls_add_action( 'pre_add_url', 'yourls_review_website_in_add_url' );
?>
