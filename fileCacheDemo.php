<?php
// 文件缓存
function get_dbsplit_config($group_id = 0, $option = [])
{
    $expire = 60;
    $cache_path = CACHE_PATH . '_config_file_/';
    if (!is_dir($cache_path)) {
        $old_mask = umask(0);
        mkdir($cache_path, 0777, true);
        umask($old_mask);
    }
    $cache_file = $cache_path . 'dbsplit.conf';
    if (!is_file($cache_file) or time() - filemtime($cache_file) > $expire or $option['refresh']) {
        // generator 之后可替换成别的数据源
        $data = @include COMMON_PATH . 'Conf/db_split.php';

        $tmp_file = $cache_file . '.' . REQUEST_ID;
        $content  = '<?php' . PHP_EOL;
        $content  .= 'return ' . var_export($data, true) . ';' . PHP_EOL;
        file_put_contents($tmp_file, $content, LOCK_EX);
        rename($tmp_file, $cache_file);
        chmod($cache_file, 0777);
    } else {
        $data = @include $cache_file;
    }
    return $group_id && isset($data[$group_id]) ? $data[$group_id] : $data;
}