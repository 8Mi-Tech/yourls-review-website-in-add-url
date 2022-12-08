<?php
/**
 * Review Website in Add URL Plug-in for YOURLS
 *
 * @package   Review Website in Add URL
 * @author    您的名字
 * @copyright 2020 您的名字
 * @license   MIT
 * @link      您的主页
 * @version   1.0
 */

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'review_website_in_add_url_add_page' );

/**
 * Add our Admin page
 */
function review_website_in_add_url_add_page() {
    // 注册插件页面
    yourls_register_plugin_page( 'review_website_in_add_url_admin_page', 'Review Website in Add URL', 'review_website_in_add_url_do_page' );
}

/**
 * Display our Admin page
 */
function review_website_in_add_url_do_page() {
    // 检查用户是否有权限
    if( !yourls_is_valid_user() ) {
        yourls_die( 'You are not allowed to do this', 'Review Website in Add URL', 403 );
    }

    // 检查系统所需数据库表是否存在
    if ( !review_website_in_add_url_table_exists() ) {
        yourls_die( 'The required database table does not exist', 'Review Website in Add URL', 500 );
    }

    // 获取白名单和黑名单
    $blacklist_urls = review_website_in_add_url_query_db( 'system_blacklist_url' ) + review_website_in_add_url_query_db( 'custom_blacklist_url' );
    $whitelist_urls = review_website_in_add_url_query_db( 'system_whitelist_url' ) + review_website_in_add_url_query_db( 'custom_whitelist_url' );

    // 显示页面
    include_once( YOURLS_PLUGINDIR . '/review-website-in-add-url/pages/admin.php' );
}

/**
 * 检查系统所需数据库表是否存在
 */
function review_website_in_add_url_table_exists() {
    global $ydb;

    // 获取表名
    $table_names = array(
        'system_blacklist_url',
        'system_whitelist_url',
        'custom_blacklist_url',
        'custom_whitelist_url',
    );

    // 检查表是否存在
    $tables_exists = true;
    foreach ( $table_names as $table_name ) {
        if ( !$ydb->table_exists( $table_name ) ) {
            $tables_exists = false;
        }
    }

    return $tables_exists;
}

/**
 * 查询数据库
 */
function review_website_in_add_url_query_db( $table_name ) {
    global $ydb;

    // 获取表数据
    $urls = $ydb->get_results( "SELECT * FROM `$table_name`" );

    // 返回URL列表
    $url_list = array();
    foreach ( $urls as $url ) {
        $url_list[] = $url->url;
    }

    return $url_list;
}

/**
 * 检测添加的地址在哪个名单
 */
function review_website_in_add_url_check_url( $url ) {
    // 获取白名单和黑名单
    $blacklist_urls = review_website_in_add_url_query_db( 'system_blacklist_url' ) + review_website_in_add_url_query_db( 'custom_blacklist_url' );
    $whitelist_urls = review_website_in_add_url_query_db( 'system_whitelist_url' ) + review_website_in_add_url_query_db( 'custom_whitelist_url' );

    // 检查地址是否在黑名单
    if ( in_array( $url, $blacklist_urls ) ) {
        return false;
    }

    // 检查地址是否在白名单
    if ( in_array( $url, $whitelist_urls ) ) {
        return true;
    }

    // 检查网站是否有色情等违法元素
    $is_illegal = review_website_in_add_url_check_illegal_content( $url );

    // 如果有则自动加入system_blacklist_url 并且禁止生成短链接
    if ( $is_illegal ) {
        review_website_in_add_url_add_url_to_db( 'system_blacklist_url', $url );
        return false;
    }

    // 如果没有则自动加入system_whitelist_url 并且允许生成短链接
    review_website_in_add_url_add_url_to_db( 'system_whitelist_url', $url );
    return true;
}

/**
 * 检查网站是否有色情等违法元素
 */
function review_website_in_add_url_check_illegal_content( $url ) {
    // 这里是检查网站是否有色情成人内容 六合彩 赌博 等违法元素的代码
    // ...

    // 这里是模拟网站有违法元素，实际情况根据实际情况修改
    return true;
}
