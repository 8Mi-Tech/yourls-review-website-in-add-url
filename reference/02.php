<?php
//定义函数用于获取白名单和黑名单的URL
function query_db($type) {
    //连接数据库
    $db = new mysqli("host", "username", "password", "yourls");
    //预处理 SQL 语句
    $stmt = $db->prepare("SELECT * FROM ? WHERE active=1");
    //绑定参数
    $stmt->bind_param('s', $type);
    //执行查询
    $stmt->execute();
    //绑定结果
    $result = $stmt->get_result();
    //获取数据
    $data = $result->fetch_all(MYSQLI_ASSOC);
    //返回数据
    return $data;
}
//定义函数用于检测提交的链接是否在白名单和黑名单中
function check_url($url) {
    //获取白名单和黑名单
    $whitelist_url = query_db("system_whitelist_url") + query_db("custom_whitelist_url");
    $blacklist_url = query_db("system_blacklist_url") + query_db("custom_blacklist_url");
    //检查链接是否在白名单中
    if (in_array($url, $whitelist_url)) {
        return true;
    }
    //检查链接是否在黑名单中
    if (in_array($url, $blacklist_url)) {
        return false;
    }
    //检测网站是否有违法元素
    if (has_illegal_elements($url)) {
        //如果有，则将链接添加到system_blacklist_url中
        add_url_to_db("system_blacklist_url", $url);
        return false;
    }
    //没有则将链接添加到system_whitelist_url中
    add_url_to_db("system_whitelist_url", $url);
    return true;
}
//定义函数用于检测网站是否有违法元素
function has_illegal_elements($url) {
    //使用爬虫获取网页内容，并检测是否包含色情、成人内容、六合彩、赌博等违法元素
    //...
}
//定义函数用于添加链接到数据库
function add_url_to_db($type, $url) {
    //连接数据库
    $db = new mysqli("host", "username", "password", "yourls");
    //预处理 SQL 语句
    $stmt = $db->prepare("INSERT INTO ? (url, active) VALUES (?, 1)");
    //绑定参数
    $stmt->bind_param('ss', $type, $url);
    //执行添加
    $stmt->execute();
}
?>