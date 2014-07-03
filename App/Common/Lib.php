<?php
/*********************/
/*  Version : 1.0    */
/*  Author  : mudou  */
/*********************/

function EnHtml($v)
{
    return $v;
}

function mydate($format, $time, $default = "")
{
    if (10000 < intval($time)) {
        return date($format, $time);
    } else {
        return $default;
    }
}

function textPost($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $v) {
            $x[$key] = text($v);
        }
    }
    return $x;
}

function MU($url, $type, $vars = array(), $domain = false)
{
    $path = explode("/", trim($url, "/"));
    $model = strtolower($path[1]);
    $action = isset($path[2]) ? strtolower($path[2]) : "";
    $http = ud($path, $domain);

    switch ($type) {
        case
        "article" :
            if (!isset($vars['id'])) {
                unset($path[0]);
                $url = implode("/", $path) . "/";
                $newurl = $url;
            } else {
                if ($UN_1 || strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
                    unset($path[0]);
                    $url = implode("/", $path) . "/";
                }
                $newurl = $url . $vars['id'] . $vars['suffix'];
            }
            break;
        case "typelist" :
            if ($UN_1 || strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
                unset($path[0]);
                $url = implode("/", $path);
            }
            $newurl = $url . $vars['suffix'];
            break;
        default :
    }
    return $http . $newurl;
}

function UD($url = array(), $domain = false)
{
    $isDomainGroup = true;
    $isDomainD = false;
    $asdd = c("APP_SUB_DOMAIN_DEPLOY");
    if ($asdd) {
        foreach (c("APP_SUB_DOMAIN_RULES") as $keyr => $ruler) {
            if (strtolower($url[0] . "/") == strtolower($ruler[0])) {
                $isDomainGroup = true;
                $isDomainD = true;
                break;
            }
        }
    }
    if (strtolower(GROUP_NAME) == strtolower(c("DEFAULT_GROUP"))) {
        $isDomainGroup = true;
    }
    if ($domain === true) {
        $domain = $_SERVER['HTTP_HOST'];
        if ($asdd) {
            $xdomain = explode(".", $_SERVER['HTTP_HOST']);
            if (!isset($xdomain[2])) {
                $ydomain = "www." . $_SERVER['HTTP_HOST'];
            } else {
                $ydomain = $_SERVER['HTTP_HOST'];
            }
            $domain = $domain == "localhost" ? "localhost" : "www" . strstr($ydomain, ".");
            foreach (c("APP_SUB_DOMAIN_RULES") as $key => $rule) {
                if (false === strpos($key, "*") && $isDomainD) {
                    $domain = $key . strstr($domain, ".");
                    $url = substr_replace($url, "", 0, strlen($rule[0]));
                    break;
                }
            }
        }
    }
    if (!$isDomainGroup) {
        $gpurl = __APP__ . "/" . $url[0] . "/";
    } else {
        $gpurl = __APP__ . "/";
    }
    if ($domain) {
        $url = "http://" . $domain . $gpurl;
    } else {
        $url = $gpurl;
    }
    return $url;
}

function Mheader($type)
{
    header("Content-Type:text/html;charset={$type}");
}

function auto_charset($fContents, $from = "gbk", $to = "utf-8")
{
    $from = strtoupper($from) == "UTF8" ? "utf-8" : $from;
    $to = strtoupper($to) == "UTF8" ? "utf-8" : $to;
    if ($to == "utf-8" && is_utf8($fContents) || strtoupper($from) === strtoupper($to) || empty($fContents) || is_scalar($fContents) && !is_string($fContents)) {
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists("mb_convert_encoding")) {
            return mb_convert_encoding($fContents, $to, $from);
        } else if (function_exists("iconv")) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } else if (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key) {
                unset($fContents[$key]);
            }
        }
        return $fContents;
    } else {
        return $fContents;
    }
}

function is_utf8($string)
{
    return preg_match("%^(?:\r\n\t\t [\\x09\\x0A\\x0D\\x20-\\x7E]            # ASCII\r\n\t   | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n\t   |  \\xE0[\\xA0-\\xBF][\\x80-\\xBF]        # excluding overlongs\r\n\t   | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n\t   |  \\xED[\\x80-\\x9F][\\x80-\\xBF]        # excluding surrogates\r\n\t   |  \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}     # planes 1-3\r\n\t   | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n\t   |  \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}     # plane 16\r\n   )*\$%xs", $string);
}

function get_date($date, $t = "d", $n = 0)
{
    if ($t == "d") {
        $firstday = date("Y-m-d 00:00:00", strtotime("{$n} day"));
        $lastday = date("Y-m-d 23:59:59", strtotime("{$n} day"));
    } else if ($t == "w") {
        if ($n != 0) {
            $date = date("Y-m-d", strtotime("{$n} week"));
        }
        $lastday = date("Y-m-d 00:00:00", strtotime("{$date} Sunday"));
        $firstday = date("Y-m-d 23:59:59", strtotime("{$lastday} -6 days"));
    } else if ($t == "m") {
        if ($n != 0) {
            if (date("m", time()) == 1) {
                $date = date("Y-m-d", strtotime("{$n} months -1 day"));
            } else {
                $date = date("Y-m-d", strtotime("{$n} months"));
            }
        }
        $firstday = date("Y-m-01 00:00:00", strtotime($date));
        $lastday = date("Y-m-d 23:59:59", strtotime("{$firstday} +1 month -1 day"));
    }
    return array(
        $firstday,
        $lastday
    );
}

function rand_string($ukey = "", $len = 6, $type = "1", $utype = "1", $addChars = "")
{
    $str = "";
    switch ($type) {
        case 0 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        case 1 :
            $chars = str_repeat("0123456789", 3);
            break;
        case 2 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" . $addChars;
            break;
        case 3 :
            $chars = "abcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        default :
            $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789" . $addChars;
            break;
    }
    if (10 < $len) {
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    if (!empty($ukey)) {
        $vd['code'] = $str;
        $vd['send_time'] = time();
        $vd['ukey'] = $ukey;
        $vd['type'] = $utype;
        m("verify")->add($vd);
    }
    return $str;
}

function is_verify($uid, $code, $utype, $timespan)
{
    if (!empty($uid)) {
        $vd['ukey'] = $uid;
    }
    $vd['type'] = $utype;
    $vd['send_time'] = array(
        "lt",
        time() + $timespan
    );
    $vd['code'] = $code;
    $vo = m("verify")->field("ukey")->where($vd)->find();
    if (is_array($vo)) {
        return $vo['ukey'];
    } else {
        return false;
    }
}

function get_global_setting()
{
    $list = array();
    if (!s("global_setting")) {
        $list_t = m("global")->field("code,text")->select();
        foreach ($list_t as $key => $v) {
            $list[$v['code']] = de_xie($v['text']);
        }
        s("global_setting", $list);
        s("global_setting", $list, 3600 * c("TTXF_TMP_HOUR"));
    } else {
        $list = s("global_setting");
    }
    return $list;
}

function get_user_acl($uid = "")
{
    $model = strtolower(MODULE_NAME);
    if (empty($uid)) {
        return false;
    }
    $gid = m("ausers")->field("u_group_id")->find($uid);
    $al = get_group_data($gid['u_group_id']);
    $acl = $al['controller'];
    $acl_key = acl_get_key();
 
    if (array_keys($acl[$model], $acl_key)) {
        return true;
    } else {
        //return false;
        return true;
    }
}

function get_group_data($gid = 0)
{
    $gid = intval($gid);
    $list = array();
    if ($gid == 0) {
        if (!s("ACL_all")) {
            $_acl_data = m("acl")->select();
            $acl_data = array();
            foreach ($_acl_data as $key => $v) {
                $acl_data[$v['group_id']] = $v;
                $acl_data[$v['group_id']]['controller'] = unserialize($v['controller']);
            }
            s("ACL_all", $acl_data, c("ADMIN_CACHE_TIME"));
            $list = $acl_data;
        } else {
            $list = s("ACL_all");
        }
    } else if (!s("ACL_" . $gid)) {
        $_acl_data = m("acl")->find($gid);
        $_acl_data['controller'] = unserialize($_acl_data['controller']);
        $acl_data = $_acl_data;
        s("ACL_" . $gid, $acl_data, c("ADMIN_CACHE_TIME"));
        $list = $acl_data;
    } else {
        $list = s("ACL_" . $gid);
    }

    return $list;
}

function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== ($entry = $dir->read())) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
    }
    $dir->close();
    return rmdir($dirname);
}

function Rmall($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== ($file = $dir->read())) {
        if ($file == "." || $file == "..") {
            continue;
        }
        if (!is_dir($dirname . "/" . $file)) {
            unlink($dirname . "/" . $file);
        } else {
            rmall($dirname . "/" . $file);
        }
        rmdir($dirname . "/" . $file);
    }
    $dir->close();
    rmdir($dirname);
    return true;
}

function ReadFiletext($filepath)
{
    $htmlfp = @fopen($filepath, "r");
    while ($data = @fread($htmlfp, 1000)) {
        $string .= $data;
    }
    @fclose($htmlfp);
    return $string;
}

function MakeFile($con, $filename)
{
    makedir(dirname($filename));
    $fp = fopen($filename, "w");
    fwrite($fp, $con);
    fclose($fp);
}

function MakeDir($dir)
{
    return is_dir($dir) || makedir(dirname($dir)) && mkdir($dir, 511);
}

function get_home_friend($type, $datas = array())
{
    $condition['is_show'] = array("eq", 1);
    $condition['link_type'] = array(
        "eq",
        $type
    );
    $type = "friend_home" . $type;
    if (!s($type)) {
        $_list = m("friend")->field("link_txt,link_href,link_img,link_type")->where($condition)->order("link_order DESC")->select();
        $list = array();
        foreach ($_list as $key => $v) {
            $list[$key] = $v;
        }
        s($type, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($type);
    }
    return $list;
}

function get_ad($id)
{
    $stype = "home_ad" . $id;
    if (!s($stype)) {
        $list = array();
        $condition['start_time'] = array(
            "lt",
            time()
        );
        $condition['end_time'] = array(
            "gt",
            time()
        );
        $condition['id'] = array(
            "eq",
            $id
        );
        $_list = m("ad")->field("ad_type,content")->where($condition)->find();
        if ($_list['ad_type'] == 1) {
            $_list['content'] = unserialize($_list['content']);
        }
        $list = $_list;
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($stype);
    }
    if ($list['ad_type'] == 0 || !$list['content']) {
        if (!$list['content']) {
            $list['content'] = "广告未上传或已过期";
        }
        echo $list['content'];
    } else {
        return $list['content'];
    }
}

function get_type_leve($id = "0")
{
    $model = d("Acategory");
    if (!s("type_son_type")) {
        $allid = array();
        $data = $model->field("id,type_nid")->where("parent_id = {$id}")->select();
        if (0 < count($data)) {
            foreach ($data as $v) {
                $allid[$v['type_nid']] = $v['id'];
                $data_1 = array();
                $data_1 = $model->field("id,type_nid")->where("parent_id = {$v['id']}")->select();
                if (0 < count($data_1)) {
                    foreach ($data_1 as $v1) {
                        $allid[$v['type_nid'] . "-" . $v1['type_nid']] = $v1['id'];
                        $data_2 = array();
                        $data_2 = $model->field("id,type_nid")->where("parent_id = {$v1['id']}")->select();
                        if (0 < count($data_2)) {
                            foreach ($data_2 as $v2) {
                                $allid[$v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid']] = $v2['id'];
                                $data_3 = array();
                                $data_3 = $model->field("id,type_nid")->where("parent_id = {$v2['id']}")->select();
                                if (0 < count($data_3)) {
                                    foreach ($data_3 as $v3) {
                                        $allid[$v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid'] . "-" . $v3['type_nid']] = $v3['id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        s("type_son_type", $allid, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $allid = s("type_son_type");
    }
    return $allid;
}

function get_area_type_leve($id = "0", $area_id = 0)
{
    $model = d("Aacategory");
    if (!s("type_son_type_area" . $area_id)) {
        $allid = array();
        $data = $model->field("id,type_nid")->where("parent_id = {$id} AND area_id={$area_id}")->select();
        if (0 < count($data)) {
            foreach ($data as $v) {
                $allid[$area_id . $v['type_nid']] = $v['id'];
                $data_1 = array();
                $data_1 = $model->field("id,type_nid")->where("parent_id = {$v['id']}")->select();
                if (0 < count($data_1)) {
                    foreach ($data_1 as $v1) {
                        $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid']] = $v1['id'];
                        $data_2 = array();
                        $data_2 = $model->field("id,type_nid")->where("parent_id = {$v1['id']}")->select();
                        if (0 < count($data_2)) {
                            foreach ($data_2 as $v2) {
                                $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid']] = $v2['id'];
                                $data_3 = array();
                                $data_3 = $model->field("id,type_nid")->where("parent_id = {$v2['id']}")->select();
                                if (0 < count($data_3)) {
                                    foreach ($data_3 as $v3) {
                                        $allid[$area_id . $v['type_nid'] . "-" . $v1['type_nid'] . "-" . $v2['type_nid'] . "-" . $v3['type_nid']] = $v3['id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        s("type_son_type_area" . $area_id, $allid, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $allid = s("type_son_type_area" . $area_id);
    }
    return $allid;
}

function get_type_leve_nid($id = "0")
{
    if (empty($id)) {
        return;
    }
    global $allid;
    static $r = array();
    get_type_leve_nid_run($id);
    $r = $allid;
    $GLOBALS['GLOBALS']['allid'] = NULL;
    return array_reverse($r);
}

function get_type_leve_nid_run($id = "0")
{
    global $allid;
    $data_parent = $data = "";
    $data = d("Acategory")->field("parent_id,type_nid")->find($id);
    $data_parent = d("Acategory")->field("id,type_nid")->where("id = {$data['parent_id']}")->find();
    if (0 < isset($data_parent['type_nid'])) {
        if (!isset($allid[0])) {
            $allid[] = $data['type_nid'];
        }
        $allid[] = $data_parent['type_nid'];
        get_type_leve_nid_run($data_parent['id']);
    } else if (!isset($allid[0])) {
        $allid[] = $data['type_nid'];
    }
}

function get_type_leve_area_nid($id = "0", $area_id = 0)
{
    if (empty($id) || empty($area_id)) {
        return;
    }
    global $allid_area;
    static $r = array();
    get_type_leve_area_nid_run($id);
    $r = $allid_area;
    $GLOBALS['GLOBALS']['allid_area'] = NULL;
    return array_reverse($r);
}

function get_type_leve_area_nid_run($id = "0")
{
    global $allid_area;
    $data_parent = $data = "";
    $data = d("Aacategory")->field("parent_id,type_nid,area_id")->find($id);
    $data_parent = d("Aacategory")->field("id,type_nid,area_id")->where("id = {$data['parent_id']}")->find();
    if (0 < isset($data_parent['type_nid'])) {
        if (!isset($allid_area[0])) {
            $allid_area[] = $data['type_nid'];
        }
        $allid_area[] = $data_parent['type_nid'];
        get_type_leve_area_nid_run($data_parent['id']);
    } else if (!isset($allid_area[0])) {
        $allid_area[] = $data['type_nid'];
    }
}

function get_son_type($id)
{
    $tempname = "type_sfs_son_all" . $id;
    if (!s($tempname)) {
        $row = get_son_type_run($id);
        s($tempname, $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s($tempname);
    }
    return $row;
}

function get_son_type_run($id)
{
    static $rerow = NULL;
    global $allid;
    $data = m("type")->field("id")->where("parent_id in ({$id})")->select();
    if (0 < count($data)) {
        foreach ($data as $key => $v) {
            $allid[] = $v['id'];
            $nowid[] = $v['id'];
        }
        $id = implode(",", $nowid);
        get_son_type_run($id);
    }
    $rerow = $allid;
    $allid = array();
    return $rerow;
}

function get_type_son($id = 0)
{
    $tempname = "type_son_all" . $id;
    if (!s($tempname)) {
        $row = get_type_son_all($id);
        s($tempname, $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s($tempname);
    }
    return $row;
}

function get_type_son_all($id = "0")
{
    static $rerow = NULL;
    global $get_type_son_all_row;
    if (empty($id)) {
        exit();
    }
    $data = m("type")->where("parent_id = {$id}")->field("id")->select();
    foreach ($data as $key => $v) {
        $get_type_son_all_row[] = $v['id'];
        $data_son = m("type")->where("parent_id = {$v['id']}")->field("id")->select();
        if (0 < count($data_son)) {
            get_type_son_all($v['id']);
        }
    }
    $rerow = $get_type_son_all_row;
    $get_type_son_all_row = array();
    return $rerow;
}

function get_type_parent_nid()
{
    $row = array();
    $p_nid_new = array();
    if (!s("type_parent_nid_temp")) {
        $data = m("type")->field("id")->select();
        if (0 < count($data)) {
            foreach ($data as $key => $v) {
                $p_nid = get_type_leve_nid($v['id']);
                $i = $n = count($p_nid);
                if (1 < $i) {
                    $j = 0;
                    for (; $j < $n; ++$j, --$i) {
                        $p_nid_new[$i - 1] = $p_nid[$j];
                    }
                } else {
                    $p_nid_new = $p_nid;
                }
                $row[$v['id']] = $p_nid_new;
            }
        }
        s("type_parent_nid_temp", $row, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $row = s("type_parent_nid_temp");
    }
    return $row;
}

function get_type_list($model, $field = true)
{
    $acaheName = md5("type_list_temp" . $model . $field);
    if (!s($acaheName)) {
        $list = d(ucfirst($model))->getField($field);
        s($acaheName, $list, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $list = s($acaheName);
    }
    return $list;
}

function get_type_infos()
{
    $row = array();
    $type_list = get_type_list("acategory", "id,type_nid,type_set");
    if (!isset($_GET['typeid'])) {
        $type_nid = get_type_leve();
        $rurl = explode("?", $_SERVER['REQUEST_URI']);
        $xurl_tmp = explode("/", str_replace(array("index.html", ".html"), array("", ""), $rurl[0]));
        $zu = implode("-", array_filter($xurl_tmp));
        $typeid = $type_nid[$zu];
        $typeset = $type_list[$typeid]['type_set'];
    } else {
        $typeid = intval($_GET['typeid']);
        $typeset = $type_list[$typeid]['type_set'];
    }
    if ($typeset == 1) {
        $templet = "list_index";
    } else {
        $templet = "index_index";
    }
    $row['typeset'] = $typeset;
    $row['templet'] = $templet;
    $row['typeid'] = $typeid;
    return $row;
}

function get_area_type_infos($area_id = 0)
{
    $row = array();
    $type_list = get_type_list("aacategory", "id,type_nid,type_set,area_id");
    if (!isset($_GET['typeid'])) {
        $type_nid = get_area_type_leve(0, $area_id);
        $rurl = explode("?", $_SERVER['REQUEST_URI']);
        $xurl_tmp = explode("/", str_replace(array("index.html", ".html"), array("", ""), $rurl[0]));
        $zu = implode("-", array_filter($xurl_tmp));
        $typeid = $type_nid[$area_id . $zu];
        $typeset = $type_list[$typeid]['type_set'];
    } else {
        $typeid = intval($_GET['typeid']);
        $typeset = $type_list[$typeid]['type_set'];
    }
    if ($typeset == 1) {
        $templet = "list_index";
    } else {
        $templet = "index_index";
    }
    $row['typeset'] = $typeset;
    $row['templet'] = $templet;
    $row['typeid'] = $typeid;
    return $row;
}

function get_type_leve_list($id = 0, $modelname = false)
{
    static $rerow = NULL;
    global $get_type_leve_list_run_row;
    if (!$modelname) {
        $model = d("type");
    } else {
        $model = d(ucfirst($modelname));
    }
    $stype = $modelname . "home_type_leve_list" . $id;
    if (!s($stype)) {
        get_type_leve_list_run($id, $model);
        $rerow = $get_type_leve_list_run_row;
        $GLOBALS['GLOBALS']['get_type_leve_list_run_row'] = NULL;
        $data = $rerow;
        s($stype, $data, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $data = s($stype);
    }
    return $data;
}

function get_type_leve_list_run($id = 0, $model)
{
    global $get_type_leve_list_run_row;
    $spa = "----";
    if (count($get_type_leve_list_run_row) < 1) {
        $get_type_leve_list_run_row = array();
    }
    $typelist = $model->where("parent_id={$id}")->field("type_name,id,parent_id")->order("sort_order DESC")->select();
    foreach ($typelist as $k => $v) {
        $leve = intval(get_typeleve($v['id'], $model));
        $v['type_name'] = str_repeat($spa, $leve) . $v['type_name'];
        $get_type_leve_list_run_row[] = $v;
        $typelist_s1 = $model->where("parent_id={$v['id']}")->field("type_name,id")->select();
        if (0 < count($typelist_s1)) {
            get_type_leve_list_run($v['id'], $model);
        }
    }
}

function get_type_leve_list_area($id = 0, $modelname = false, $area_id = 0)
{
    static $rerow = NULL;
    global $get_type_leve_list_area_run_row;
    if (!$modelname) {
        $model = d("type");
    } else {
        $model = d(ucfirst($modelname));
    }
    $stype = $modelname . "home_type_leve_list_area" . $id . $area_id;
    if (!s($stype)) {
        get_type_leve_list_area_run($id, $model, $area_id);
        $rerow = $get_type_leve_list_area_run_row;
        $GLOBALS['GLOBALS']['get_type_leve_list_area_run_row'] = NULL;
        $data = $rerow;
        s($stype, $data, 3600 * c("HOME_CACHE_TIME"));
    } else {
        $data = s($stype);
    }
    return $data;
}

function get_type_leve_list_area_run($id = 0, $model, $area_id)
{
    global $get_type_leve_list_area_run_row;
    $spa = "----";
    if (count($get_type_leve_list_area_run_row) < 1) {
        $get_type_leve_list_area_run_row = array();
    }
    $typelist = $model->where("parent_id={$id} AND area_id={$area_id}")->field("type_name,id,parent_id")->order("sort_order DESC")->select();
    foreach ($typelist as $k => $v) {
        $leve = intval(get_typeleve($v['id'], $model));
        $v['type_name'] = str_repeat($spa, $leve) . $v['type_name'];
        $get_type_leve_list_area_run_row[] = $v;
        $typelist_s1 = $model->where("parent_id={$v['id']}")->field("type_name,id")->select();
        if (0 < count($typelist_s1)) {
            get_type_leve_list_area_run($v['id'], $model, $area_id);
        }
    }
}

function get_typeLeve($typeid, $model)
{
    $typeleve = 0;
    global $typeleve;
    static $rt = 0;
    get_typeleve_run($typeid, $model);
    $rt = $typeleve;
    unset($GLOBALS['typeleve']);
    return $rt;
}

function get_typeLeve_run($typeid, $model)
{
    global $typeleve;
    $condition['id'] = $typeid;
    $v = $model->field("parent_id")->where($condition)->find();
    if (0 < $v['parent_id']) {
        ++$typeleve;
        get_typeleve_run($v['parent_id'], $model);
    }
}

function de_xie($arr)
{
    $data = array();
    if (is_array($arr)) {
        foreach ($arr as $key => $v) {
            if (is_array($v)) {
                foreach ($v as $skey => $sv) {
                    if (is_array($sv)) {
                    } else {
                        $v[$skey] = stripslashes($sv);
                    }
                }
                $data[$key] = $v;
            } else {
                $data[$key] = stripslashes($v);
            }
        }
    } else {
        $data = stripslashes($arr);
    }
    return $data;
}

function text($text, $parseBr = false, $nr = false)
{
    $text = htmlspecialchars_decode($text);
    $text = safe($text, "text");
    if (!$parseBr && $nr) {
        $text = str_ireplace(array("\r", "\n", "\t", "&nbsp;"), "", $text);
        $text = htmlspecialchars($text, ENT_QUOTES);
    } else if (!$nr) {
        $text = htmlspecialchars($text, ENT_QUOTES);
    } else {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = nl2br($text);
    }
    $text = trim($text);
    return $text;
}

function safe($text, $type = "html", $tagsMethod = true, $attrMethod = true, $xssAuto = 1, $tags = array(), $attr = array(), $tagsBlack = array(), $attrBlack = array())
{
    $text_tags = "";
    $font_tags = "<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>";
    $base_tags = $font_tags . "<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>";
    $form_tags = $base_tags . "<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>";
    $html_tags = $base_tags . "<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed>";
    $all_tags = $form_tags . $html_tags . "<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>";
    $text = strip_tags($text, ${$type . "_tags"});
    if ($type != "all") {
        while (preg_match("/(<[^><]+) (onclick|onload|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i", $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match("/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i", $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

function h($text, $tags = null)
{
    $text = trim($text);
    $text = preg_replace("/<!--?.*-->/", "", $text);
    $text = preg_replace("/<!--?.*-->/", "", $text);
    $text = preg_replace("/<\\?|\\?>/", "", $text);
    $text = preg_replace("/<script?.*\\/script>/", "", $text);
    $text = str_replace("[", "&#091;", $text);
    $text = str_replace("]", "&#093;", $text);
    $text = str_replace("|", "&#124;", $text);
    $text = preg_replace("/\\r?\\n/", "", $text);
    $text = preg_replace("/<br(\\s\\/)?>/i", "[br]", $text);
    $text = preg_replace("/(\\[br\\]\\s*){10,}/i", "[br]", $text);
    while (preg_match("/(<[^><]+) (lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1], $text);
    }
    while (preg_match("/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . $mat[3], $text);
    }
    if (empty($tags)) {
        $tags = "table|tbody|td|th|tr|i|b|u|strong|img|p|br|div|span|em|ul|ol|li|dl|dd|dt|a|alt|h[1-9]?";
        $tags .= "|object|param|embed";
    }
    $text = preg_replace("/<(\\/?(?:" . $tags . "))( [^><\\[\\]]*)?>/i", "[\\1\\2]", $text);
    $text = preg_replace("/<\\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|style|xml)[^><]*>/i", "", $text);
    while (preg_match("/<([a-z]+)[^><\\[\\]]*>[^><]*<\\/\\1>/i", $text, $mat)) {
        $text = str_replace($mat[0], str_replace(">", "]", str_replace("<", "[", $mat[0])), $text);
    }
    while (preg_match("/(\\[[^\\[\\]]*=\\s*)(\\\"|')([^\\2\\[\\]]+)\\2([^\\[\\]]*\\])/i", $text, $mat)) {
        $text = str_replace($mat[0], $mat[1] . "|" . $mat[3] . "|" . $mat[4], $text);
    }
    $text = str_replace("<", "&lt;", $text);
    $text = str_replace(">", "&gt;", $text);
    $text = str_replace("\"", "&quot;", $text);
    $text = str_replace("[", "<", $text);
    $text = str_replace("]", ">", $text);
    $text = str_replace("|", "\"", $text);
    $text = str_replace("  ", " ", $text);
    return $text;
}

function get_thumb_pic($str)
{
    $path = explode("/", $str);
    $sc = count($path);
    $path[$sc - 1] = "thumb_" . $path[$sc - 1];
    return implode("/", $path);
}

function get_kvtable($nid = "")
{
    $stype = "kvtable" . $nid;
    $list = array();
    if (!s($stype)) {
        if (!empty($nid)) {
            $tmplist = m("kvtable")->where("nid='{$nid}'")->field(true)->select();
        } else {
            $tmplist = m("rule")->field(true)->select();
        }
        foreach ($tmplist as $v) {
            $list[$v['id']] = $v;
        }
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
        $row = $list;
    } else {
        $list = s($stype);
        $row = $list;
    }
    return $row;
}

function cnsubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true)
{
    $str = strip_tags($str);
    if (function_exists("mb_substr")) {
        if (mb_strlen($str, $charset) <= $length) {
            return $str;
        }
        $slice = mb_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-]|[�-�][�-�]|[�-�][�-�]{2}|[�-�][�-�]{3}/";
        $re['gb2312'] = "/[\x01-]|[�-�][�-�]/";
        $re['gbk'] = "/[\x01-]|[�-�][@-�]/";
        $re['big5'] = "/[\x01-]|[�-�]([@-~]|�-�])/";
        preg_match_all($re[$charset], $str, $match);
        if (count($match[0]) <= $length) {
            return $str;
        }
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if ($suffix) {
        return $slice . "…";
    }
    return $slice;
}

function getLastTimeFormt($time, $type = 0)
{
    if ($type == 0) {
        $f = "m-d H:i";
    } else if ($type == 1) {
        $f = "Y-m-d H:i";
    }
    $agoTime = time() - $time;
    if ($agoTime <= 60 && 0 <= $agoTime) {
        return $agoTime . "秒前";
    } else if ($agoTime <= 3600 && 60 < $agoTime) {
        return intval($agoTime / 60) . "分钟前";
    } else if (date("d", $time) == date("d", time()) && 3600 < $agoTime) {
        return "今天 " . date("H:i", $time);
    } else if (date("d", $time + 86400) == date("d", time()) && $agoTime < 172800) {
        return "昨天 " . date("H:i", $time);
    } else {
        return date($f, $time);
    }
}

function get_avatar($uid, $size = "middle", $type = "")
{
    $size = in_array($size, array("big", "middle", "small")) ? $size : "big";
    $uid = abs(intval($uid));
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    $typeadd = $type == "real" ? "_real" : "";
    $path = __ROOT__ . "/Style/header/customavatars/" . $dir1 . "/" . $dir2 . "/" . $dir3 . "/" . substr($uid, -2) . $typeadd . "_avatar_{$size}.jpg";
    if (!file_exists(c("WEB_ROOT") . $path)) {
        $path = __ROOT__ . "/Style/header/images/" . "noavatar_{$size}.gif";
    }
    return $path;
}

function get_Area_list($id = "")
{
    $cacheName = "temp_area_list_s";
    if (!s($cacheName)) {
        $list = m("area")->getField("id,name");
        s($cacheName, $list, 3.6e+009);
    } else {
        $list = s($cacheName);
    }
    if (!empty($id)) {
        return $list[$id];
    } else {
        return $list;
    }
}

function ip2area($ip = "")
{
    if (strlen($ip) < 6) {
        return;
    }
    import("ORG.Net.IpLocation");
    $Ip = new IpLocation("CoralWry.dat");
    $area = $Ip->getlocation($ip);
    $area = auto_charset($area);
    if ($area['country']) {
        $res = $area['country'];
    }
    if ($area['area']) {
        $res = $res . "(" . $area['area'] . ")";
    }
    if (empty($res)) {
        $res = "未知";
    }
    return $res;
}

function second2string($second, $type = 0)
{
    $day = floor($second / 86400);
    $second %= 86400;
    $hour = floor($second / 3600);
    $second %= 3600;
    $minute = floor($second / 60);
    $second %= 60;
    switch ($type) {
        case 0 :
            if (1 <= $day) {
                $res = $day . "天";
            } else if (1 <= $hour) {
                $res = $hour . "小时";
            } else {
                $res = $minute . "分钟";
            }
            break;
        case 1 :
            if (5 <= $day) {
                $res = date("Y-m-d H:i", time() + $second);
            } else if (1 <= $day && $day < 5) {
                $res = $day . "天前";
            } else if (1 <= $hour) {
                $res = $hour . "小时前";
            } else {
                $res = $minute . "分钟前";
                break;
            }
    }
    return $res;
}

function getOrderSN($type, $id, $uid)
{
    switch ($type) {
        case "buy" :
            $str = substr(time(), 5) . $id . $uid;
            $str = "A" . str_pad($str, 14, "0", STR_PAD_LEFT);
            break;
        case "bc" :
            $str = substr(time(), 5) . $id . $uid;
            $str = "B" . str_pad($str, 14, "0", STR_PAD_LEFT);
            break;
    }
    return $str;
}

function FS($filename, $data = "", $path = "")
{
    $path = c("WEB_ROOT") . $path;
    if ($data == "") {
        $f = explode("/", $filename);
        $num = count($f);
        if (2 < $num) {
            $fx = $f;
            array_pop($f);
            $pathe = implode("/", $f);
            $re = f($fx[$num - 1], "", $pathe . "/");
        } else {
            isset($f[1]) ? ($re = f($f[1], "", c("WEB_ROOT") . $f[0] . "/")) : ($re = f($f[0]));
        }
        return $re;
    } else if (!empty($path)) {
        $re = f($filename, $data, $path);
    } else {
        $re = f($filename, $data);
    }
}

function formtUrl($url)
{
    if (!stristr($url, "http://")) {
        $url = str_replace("http://", "", $url);
    }
    $fourl = explode("/", $url);
    $domain = get_domain("http://" . $fourl[0]);
    $perfix = str_replace($domain, "", $fourl[0]);
    return $perfix . $domain;
}

function get_domain($url)
{
    $pattern = "/[/w-]+/.(com|net|org|gov|biz|com.tw|com.hk|com.ru|net.tw|net.hk|net.ru|info|cn|com.cn|net.cn|org.cn|gov.cn|mobi|name|sh|ac|la|travel|tm|us|cc|tv|jobs|asia|hn|lc|hk|bz|com.hk|ws|tel|io|tw|ac.cn|bj.cn|sh.cn|tj.cn|cq.cn|he.cn|sx.cn|nm.cn|ln.cn|jl.cn|hl.cn|js.cn|zj.cn|ah.cn|fj.cn|jx.cn|sd.cn|ha.cn|hb.cn|hn.cn|gd.cn|gx.cn|hi.cn|sc.cn|gz.cn|yn.cn|xz.cn|sn.cn|gs.cn|qh.cn|nx.cn|xj.cn|tw.cn|hk.cn|mo.cn|org.hk|is|edu|mil|au|jp|int|kr|de|vc|ag|in|me|edu.cn|co.kr|gd|vg|co.uk|be|sg|it|ro|com.mo)(/.(cn|hk))*/";
    preg_match($pattern, $url, $matches);
    if (0 < count($matches)) {
        return $matches[0];
    } else {
        $rs = parse_url($url);
        $main_url = $rs['host'];
        if (!strcmp(long2ip(sprintf("%u", ip2long($main_url))), $main_url)) {
            return $main_url;
        } else {
            $arr = explode(".", $main_url);
            $count = count($arr);
            $endArr = array("com", "net", "org");
            if (in_array($arr[$count - 2], $endArr)) {
                $domain = $arr[$count - 3] . "." . $arr[$count - 2] . "." . $arr[$count - 1];
            } else {
                $domain = $arr[$count - 2] . "." . $arr[$count - 1];
            }
            return $domain;
        }
    }
}

function getFloatValue($f, $len)
{
    $tmpInt = intval($f);
    $tmpDecimal = $f - $tmpInt;
    $str = "{$tmpDecimal}";
    $subStr = strstr($str, ".");
    if (false === $subStr) {
        0 < $len ? ($repetstr = "." . str_repeat("0", $len)) : ($repetstr = "");
        return $tmpInt . $repetstr;
    }
    if (strlen($subStr) < $len + 1) {
        $repeatCount = $len + 1 - strlen($subStr);
        $str = $str . "" . str_repeat("0", $repeatCount);
    }
    return $tmpInt . "" . substr($str, 1, 1 + $len);
}

function get_remote_img($content)
{
    $rt = c("WEB_ROOT");
    $img_dir = c("REMOTE_IMGDIR") ? c("REMOTE_IMGDIR") : "/UF/Remote";
    $base_dir = substr($rt, 0, strlen($rt) - 1);
    $content = stripslashes($content);
    $img_array = array();
    preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\\/\\/(.*)\\.(gif|jpg|jpeg|bmp|png|ico))/isU", $content, $img_array);
    $img_array = array_unique($img_array[2]);
    set_time_limit(0);
    $imgUrl = $img_dir . "/" . strftime("%Y%m%d", time());
    $imgPath = $base_dir . $imgUrl;
    $milliSecond = strftime("%H%M%S", time());
    if (!is_dir($imgPath)) {
        makedir($imgPath, 511);
    }
    foreach ($img_array as $key => $value) {
        $value = trim($value);
        $get_file = @file_get_contents($value);
        $rndFileName = $imgPath . "/" . $milliSecond . $key . "." . substr($value, -3, 3);
        $fileurl = $imgUrl . "/" . $milliSecond . $key . "." . substr($value, -3, 3);
        if ($get_file) {
            $fp = @fopen($rndFileName, "w");
            @fwrite($fp, $get_file);
            @fclose($fp);
        }
        $content = ereg_replace($value, $fileurl, $content);
    }
    return $content;
}

function ajaxmsg($msg = "", $type = 1, $is_end = true)
{
    $json['status'] = $type;
    if (is_array($msg)) {
        foreach ($msg as $key => $v) {
            $json[$key] = $v;
        }
    } else if (!empty($msg)) {
        $json['message'] = $msg;
    }
    if ($is_end) {
        exit(json_encode($json));
    } else {
        echo json_encode($json);
    }
}

function hidecard($cardnum, $type = 1, $default = "")
{
    if (empty($cardnum)) {
        return $default;
    }
    if ($type == 1) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", 12) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 2) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", 5) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 3) {
        $cardnum = str_repeat("*", strlen($cardnum) - 4) . substr($cardnum, strlen($cardnum) - 4);
    } else if ($type == 4) {
        $cardnum = substr($cardnum, 0, 3) . str_repeat("*", strlen($cardnum) - 3);
    }
    return $cardnum;
}

function setmb($size)
{
    $mbsize = $size / 1024 / 1024;
    if (0 < $mbsize) {
        list($t1, $t2) = explode(".", $mbsize);
        $mbsize = $t1 . "." . substr($t2, 0, 2);
    }
    if ($mbsize < 1) {
        $kbsize = $size / 1024;
        list($t1, $t2) = explode(".", $kbsize);
        $kbsize = $t1 . "." . substr($t2, 0, 2);
        return $kbsize . "KB";
    } else {
        return $mbsize . "MB";
    }
}

function getMoneyFormt($money)
{
    if (100000 <= $money) {
        $res = ($money / 10000) . "万";
    } else {
        $res = getfloatvalue($money, 0);
    }
    return $res;
}

function getArea()
{
    $area = fs("Webconfig/area");
    if (!is_array($area)) {
        $list = m("area")->getField("id,name");
        fs("area", $list, "Webconfig/");
    } else {
        return $area;
    }
}

function getLeveIco($num, $type = 1)
{
    $leveconfig = fs("Webconfig/leveconfig");
    foreach ($leveconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            if ($type == 1) {
                return "/UF/leveico/" . $v['icoName'];
            } else {
                return "<img src=\"" . __ROOT__ . "/UF/leveico/" . $v['icoName'] . "\" title=\"" . $v['name'] . "\"/>";
            }
        }
    }
}

function getAgeName($num)
{
    $ageconfig = fs("Webconfig/ageconfig");
    foreach ($ageconfig as $key => $v) {
        if ($v['start'] <= $num && $num < $v['end']) {
            return $v['name'];
        }
    }
}

function getLocalhost()
{
    $vo['id'] = 1;
    $vo['name'] = "主站";
    $vo['domain'] = "www";
    return $vo;
}

function Fmoney($money)
{
    if (!is_numeric($money)) {
        return "￥0.00";
    }
    $sb = "";
    if ($money < 0) {
        $sb = "-";
        $money *= -1;
    }
    $dot = explode(".", $money);
    $tmp_money = strrev_utf8($dot[0]);
    $format_money = "";
    $i = 3;
    for (; $i < strlen($dot[0]); $i += 3) {
        $format_money .= substr($tmp_money, 0, 3) . ",";
        $tmp_money = substr($tmp_money, 3);
    }
    $format_money .= $tmp_money;
    if (empty($sb)) {
        $format_money = "￥" . strrev_utf8($format_money);
    } else {
        $format_money = "￥-" . strrev_utf8($format_money);
    }
    if ($dot[1]) {
        return $format_money . "." . $dot[1];
    } else {
        return $format_money;
    }
}

function strrev_utf8($str)
{
    return join("", array_reverse(preg_split("//u", $str)));
}

function getInvestUrl($id)
{
    return __APP__ . "/invest/{$id}" . c("URL_HTML_SUFFIX");
}

function get_admin_name($id = false)
{
    $stype = "adminlist";
    $list = array();
    if (!s($stype)) {
        $rule = m("ausers")->field("id,user_name")->select();
        foreach ($rule as $v) {
            $list[$v['id']] = $v['user_name'];
        }
        s($stype, $list, 3600 * c("HOME_CACHE_TIME"));
        if (!$id) {
            $row = $list;
        } else {
            $row = $list[$id];
        }
    } else {
        $list = s($stype);
        if ($id === false) {
            $row = $list;
        } else {
            $row = $list[$id];
        }
    }
    return $row;
}

function getIco($map)
{
    $str = "";
    if (0 < $map['reward_type']) {
        $str .= "<img src=\"" . __ROOT__ . "/Style/H/images/j.gif\" align=\"absmiddle\">";
    }
    if ($map['borrow_type'] == 2) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/d.gif\" align=\"absmiddle\">";
    } else if ($map['borrow_type'] == 3) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/m.gif\" align=\"absmiddle\">";
    } else if ($map['borrow_type'] == 4) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/jing.gif\" align=\"absmiddle\">";
    } else if ($map['borrow_type'] == 1) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/xin.gif\" align=\"absmiddle\">";
    } else if ($map['borrow_type'] == 5) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/ya.gif\" align=\"absmiddle\">";
    }
    if ($map['repayment_type'] == 1) {
        $str .= "&nbsp;<img src=\"" . __ROOT__ . "/Style/H/images/t.jpg\" align=\"absmiddle\">";
    }
    return $str;
}

function addMsg($from, $to, $title, $msg, $type = 1)
{
    if (empty($from) || empty($to)) {
        return;
    }
    $data['from_uid'] = $from;
    $data['from_uname'] = m("members")->getFieldById($from, "user_name");
    $data['to_uid'] = $to;
    $data['to_uname'] = m("members")->getFieldById($to, "user_name");
    $data['title'] = $title;
    $data['msg'] = $msg;
    $data['add_time'] = time();
    $data['is_read'] = 0;
    $data['type'] = $type;
    $newid = m("member_msg")->add($data);
    return $newid;
}

function getSubSite()
{
    $map['is_open'] = 1;
    $list = m("area")->field(true)->where($map)->select();
    $cdomain = explode(".", $_SERVER['HTTP_HOST']);
    $cpx = array_pop($cdomain);
    $doamin = array_pop($cdomain);
    $host = "." . $doamin . "." . $cpx;
    foreach ($list as $key => $v) {
        $list[$key]['host'] = "http://" . $v['domain'] . $host;
    }
    return $list;
}

function notice($type, $uid, $data = array())
{
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $msgconfig = fs("Webconfig/msgconfig");
    $emailTxt = fs("Webconfig/emailtxt");
    $smsTxt = fs("Webconfig/smstxt");
    $msgTxt = fs("Webconfig/msgtxt");
    $emailTxt = de_xie($emailTxt);
    $smsTxt = de_xie($smsTxt);
    $msgTxt = de_xie($msgTxt); 

    import("ORG.Net.Email");
    $port = 25;
    $smtpserver = $msgconfig['stmp']['server'];
    $smtpuser = $msgconfig['stmp']['user'];
    $smtppwd = $msgconfig['stmp']['pass'];
    $mailtype = "HTML";
    $sender = $msgconfig['stmp']['user'];
    $smtp = new smtp($smtpserver, $port, true, $smtpuser, $smtppwd, $sender);
	$minfo = m("members")->field("user_email,user_name,user_phone")->find($uid);
    $uname = $minfo['user_name'];
    switch ($type) {
        case 1 :
            $vcode = rand_string($uid, 32, 0, 1);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/emailverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $msgTxt['regsuccess']);
            addInnerMsg($uid, "恭喜您注册成功", $body);
            $subject = "您刚刚在" . $datag['web_name'] . "注册成功";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['regsuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 2 :
            $vcode = rand_string($uid, 10, 3, 3);
            $pcode = rand_string($uid, 6, 1, 3);
            $subject = "您刚刚在" . $datag['web_name'] . "注册成功";
            $body = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $emailTxt['safecode']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $pcode
            ), $smsTxt['safecode']);
            $sendp = sendsms($minfo['user_phone'], $content);
            return $send;
        case 3 :
            $vcode = rand_string($uid, 6, 1, 4);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $smsTxt['safecode']);
            $send = sendsms($minfo['user_phone'], $content);
            return $send;
        case 4 :
            $vcode = rand_string($uid, 6, 1, 5);
            $content = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $smsTxt['safecode']);
            $send = sendsms($data['phone'], $content);
            return $send;
        case 5 :
            $vcode = rand_string($uid, 10, 1, 6);
            $subject = "您刚刚在" . $datag['web_name'] . "申请更换手机的安全码";
            $body = str_replace(array(
                "#CODE#"
            ), array(
                $vcode
            ), $emailTxt['changephone']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 6 :
            $subject = "恭喜，你在" . $datag['web_name'] . "发布的借款审核通过";
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $emailTxt['verifysuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            $body = str_replace(array(
                "#UserName#"
            ), array(
                $uname
            ), $msgTxt['verifysuccess']);
            addInnerMsg($uid, "恭喜借款审核通过", $body);
            return $send;
        case 7 :
            $vcode = rand_string($uid, 32, 0, 7);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/getpasswordverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $subject = "您刚刚在" . $datag['web_name'] . "申请了密码找回";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['getpass']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
        case 8 :
            $vcode = rand_string($uid, 32, 0, 1);
            $link = "<a href=\"" . c("WEB_URL") . "/member/common/emailverify?vcode=" . $vcode . "\">点击链接验证邮件</a>";
            $subject = "您刚刚在" . $datag['web_name'] . "申请邮件验证";
            $body = str_replace(array(
                "#UserName#",
                "#LINK#"
            ), array(
                $uname,
                $link
            ), $emailTxt['regsuccess']);
            $to = $minfo['user_email'];
            $send = $smtp->sendmail($to, $sender, $subject, $body, $mailtype);
            return $send;
    }
}

function SMStip($type, $mob, $from = array(), $to = array())
{
    if (empty($mob)) {
        return;
    }
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $smsTxt = fs("Webconfig/smstxt");
    $smsTxt = de_xie($smsTxt);
    if ($smsTxt[$type]['enable'] == 1) {
        $body = str_replace($from, $to, $smsTxt[$type]['content']);
        $to = $minfo['user_email'];
        $send = sendsms($mob, $body);
    } else {
        return;
    }
}

function MTip($type, $uid = 0, $info = "")
{
    $datag = get_global_setting();
    $datag = de_xie($datag);
    $port = 25;
    $id1 = "{$type}_1";
    $id2 = "{$type}_2";
    $per = c("DB_PREFIX");
    $sql = "select 1 as tip1,0 as tip2,m.user_email,m.id from {$per}members m WHERE m.id={$uid}";
    $memail = m()->query($sql);
    switch ($type) {
        case "chk1" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "修改了登陆密码";
            $body = "您刚刚在" . $datag['web_name'] . "修改了登陆密码,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚修改了登陆密码,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚修改了登陆密码", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk2" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "修改了提现的银行帐户";
            $body = "您刚刚在" . $datag['web_name'] . "修改了提现的银行帐户,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚修改了提现的银行帐户,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚修改了提现的银行帐户", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk6" :
            $to = "";
            $subject = "您刚刚在" . $datag['web_name'] . "申请了提现操作";
            $body = "您刚刚在" . $datag['web_name'] . "申请了提现操作,如不是自己操作,请尽快联系客服";
            $innerbody = "您刚刚申请了提现操作,如不是自己操作,请尽快联系客服";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您刚刚申请了提现操作", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk7" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的借款标刚刚初审未通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号借款标刚刚初审未通过";
            $innerbody = "您发布的第{$info}号借款标刚刚初审未通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的借款标初审未通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk8" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的借款标刚刚初审通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号借款标刚刚初审通过";
            $innerbody = "您发布的第{$info}号借款标刚刚初审通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的借款标初审通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk9" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "发布的借款标刚刚复审通过";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号借款标刚刚复审通过";
            $innerbody = "您发布的第{$info}号借款标刚刚复审通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的借款标复审通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk12" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的发布的借款标刚刚复审未通过";
            $body = "您在" . $datag['web_name'] . "的发布的第{$info}号借款标复审未通过";
            $innerbody = "您发布的第{$info}号借款标复审未通过";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的借款标复审未通过", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk10" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的借款标已满标";
            $body = "刚刚您在" . $datag['web_name'] . "的第{$info}号借款标已满标，请登陆查看";
            $innerbody = "刚刚您的借款标已满标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的第{$info}号借款标已满标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk11" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的借款标已流标";
            $body = "您在" . $datag['web_name'] . "发布的第{$info}号借款标已流标，请登陆查看";
            $innerbody = "您的第{$info}号借款标已流标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "刚刚您的借款标已流标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk25" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "的借入的还款进行了还款操作";
            $body = "您对在" . $datag['web_name'] . "借入的第{$info}号借款进行了还款，请登陆查看";
            $innerbody = "您对借入的第{$info}号借款进行了还款";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您对借入标还款进行了还款操作", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk27" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "设置的自动投标按设置投了新标";
            $body = "您在" . $datag['web_name'] . "设置的自动投标按设置对第{$info}号借款进行了投标，请登陆查看";
            $innerbody = "您设置的自动投标对第{$info}号借款进行了投标";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您设置的自动投标按设置投了新标", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk14" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "投标的借款流标了";
            $body = "您在" . $datag['web_name'] . "投标的第{$info}号借款借出成功了";
            $innerbody = "您投标的借款成功了";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您投标的第{$info}号借款借款成功", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk15" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "投标的借款流标了";
            $body = "您在" . $datag['web_name'] . "投标的第{$info}号借款流标了，相关资金已经返回帐户，请登陆查看";
            $innerbody = "您投标的借款流标了";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您投标的第{$info}号借款流标了，相关资金已经返回帐户", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk16" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "借出的借款收到了新的还款";
            $body = "您在" . $datag['web_name'] . "借出的第{$info}号借款收到了新的还款，请登陆查看";
            $innerbody = "您借出的借款收到了新的还款";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您借出的第{$info}号借款收到了新的还款", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
        case "chk18" :
            $to = "";
            $subject = "您在" . $datag['web_name'] . "借出的借款逾期网站代还了本金";
            $body = "您在" . $datag['web_name'] . "借出的第{$info}号借款逾期网站代还了本金，请登陆查看";
            $innerbody = "您借出的第{$info}号借款逾期网站代还了本金";
            foreach ($memail as $v) {
                if (0 < $v['tip1']) {
                    addInnerMsg($v['id'], "您借出的第{$info}号借款逾期网站代还了本金", $innerbody);
                }
                if (0 < $v['tip2']) {
                    $to = empty($to) ? $v['user_email'] : $to . "," . $v['user_email'];
                }
            }
            break;
    }
    return $send;
}

function investMoney($uid, $borrow_id, $money, $_is_auto = 0)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $datag = get_global_setting();
    $binfo = m("borrow_info")->field("borrow_uid,borrow_money,borrow_interest_rate,borrow_type,borrow_duration,repayment_type,has_borrow")->find($borrow_id);
    $vminfo = getminfo($uid, "m.user_leve,m.time_limit,mm.account_money");
    if ($vminfo['account_money'] < $money) {
        return "对不起，可用余额不足，不能投标";
    }
    $fee_rate = $datag['fee_invest_manage'] / 100;
    $havemoney = $binfo['has_borrow'];
    if ($binfo['borrow_money'] - $havemoney - $money < 0) {
        return "对不起，此标还差" . ($binfo['borrow_money'] - $havemoney) . "元满标，您最多投标" . ($binfo['borrow_money'] - $havemoney) . "元";
    }
    $investMoney = d("borrow_investor");
    $investMoney->startTrans();
    $investinfo['status'] = 1;
    $investinfo['borrow_id'] = $borrow_id;
    $investinfo['investor_uid'] = $uid;
    $investinfo['borrow_uid'] = $binfo['borrow_uid'];
    $investinfo['investor_capital'] = $money;
    $investinfo['is_auto'] = $_is_auto;
    $investinfo['add_time'] = time();
    $savedetail = array();
    switch ($binfo['repayment_type']) {
        case 1 :
            $investinfo['investor_interest'] = getfloatvalue($binfo['borrow_interest_rate'] * $investinfo['investor_capital'] * $binfo['borrow_duration'] / 100, 2);
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'] / 100, 2);
            $invest_info_id = m("borrow_investor")->add($investinfo);
            $i = 1;
            $investdetail['borrow_id'] = $borrow_id;
            $investdetail['invest_id'] = $invest_info_id;
            $investdetail['investor_uid'] = $uid;
            $investdetail['borrow_uid'] = $binfo['borrow_uid'];
            $investdetail['capital'] = $investinfo['investor_capital'];
            $investdetail['interest'] = $investinfo['investor_interest'];
            $investdetail['interest_fee'] = $investinfo['invest_fee'];
            $investdetail['status'] = 0;
            $investdetail['sort_order'] = $i;
            $investdetail['total'] = $binfo['borrow_duration'];
            $savedetail[] = $investdetail;
            break;
        case 2 :
            $monthData['type'] = "all";
            $monthData['money'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['duration'] = $binfo['borrow_duration'];
            $repay_detail = equalmonth($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("borrow_investor")->add($investinfo);
            $monthDataDetail['money'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $monthDataDetail['duration'] = $binfo['borrow_duration'];
            $repay_list = equalmonth($monthDataDetail);
            $i = 1;
            foreach ($repay_list as $key => $v) {
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 0;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
        case 3 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = equalseason($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_money'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = equalseason($monthDataDetail);
            $i = 1;
            foreach ($repay_list as $key => $v) {
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 0;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
        case 4 :
            $monthData['month_times'] = $binfo['borrow_duration'];
            $monthData['account'] = $investinfo['investor_capital'];
            $monthData['year_apr'] = $binfo['borrow_interest_rate'];
            $monthData['type'] = "all";
            $repay_detail = equalendmonth($monthData);
            $investinfo['investor_interest'] = $repay_detail['repayment_account'] - $investinfo['investor_capital'];
            $investinfo['invest_fee'] = getfloatvalue($fee_rate * $investinfo['investor_interest'], 2);
            $invest_info_id = m("borrow_investor")->add($investinfo);
            $monthDataDetail['month_times'] = $binfo['borrow_duration'];
            $monthDataDetail['account'] = $investinfo['investor_capital'];
            $monthDataDetail['year_apr'] = $binfo['borrow_interest_rate'];
            $repay_list = equalendmonth($monthDataDetail);
            $i = 1;
            foreach ($repay_list as $key => $v) {
                $investdetail['borrow_id'] = $borrow_id;
                $investdetail['invest_id'] = $invest_info_id;
                $investdetail['investor_uid'] = $uid;
                $investdetail['borrow_uid'] = $binfo['borrow_uid'];
                $investdetail['capital'] = $v['capital'];
                $investdetail['interest'] = $v['interest'];
                $investdetail['interest_fee'] = getfloatvalue($fee_rate * $v['interest'], 2);
                $investdetail['status'] = 0;
                $investdetail['sort_order'] = $i;
                $investdetail['total'] = $binfo['borrow_duration'];
                ++$i;
                $savedetail[] = $investdetail;
            }
            break;
    }
    $invest_defail_id = m("investor_detail")->addAll($savedetail);
    if ($invest_defail_id && $invest_info_id) {
        $investMoney->commit();
        $res = memberMoneyLog($uid, 6, 0 - $money, "对{$borrow_id}号标进行投标", $binfo['borrow_uid']);
        $upborrowsql = "update `{$pre}borrow_info` set ";
        $upborrowsql .= "`has_borrow`=" . ($havemoney + $money) . ",`borrow_times`=`borrow_times`+1";
        $upborrowsql .= " WHERE `id`={$borrow_id}";
        $upborrow_res = m()->execute($upborrowsql);
        if ($havemoney + $money == $binfo['borrow_money']) {
            borrowfull($borrow_id, $binfo['borrow_type']);
        }
        if (!$res) {
            m("investor_detail")->where("invest_id={$invest_info_id}")->delete();
            m("borrow_investor")->where("id={$invest_info_id}")->delete();
            $upborrowsql = "update `{$pre}borrow_info` set ";
            $upborrowsql .= "`has_borrow`=" . $havemoney . ",`borrow_times`=`borrow_times`-1";
            $upborrowsql .= " WHERE `id`={$borrow_id}";
            $upborrow_res = m()->execute($upborrowsql);
            $done = false;
        } else {
            $done = true;
        }
    } else {
        $investMoney->rollback();
    }
    return $done;
}

function borrowFull($borrow_id, $btype = 0)
{
    if ($btype == 3) {
        borrowapproved($borrow_id);
        borrowrepayment($borrow_id, 1);
    } else {
        $saveborrow['borrow_status'] = 4;
        $saveborrow['full_time'] = time();
        $upborrow_res = m("borrow_info")->where("id={$borrow_id}")->save($saveborrow);
    }
}

function borrowRefuse($borrow_id, $type)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowInvestor = d("borrow_investor");
    $binfo = m("borrow_info")->field("borrow_type,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
    $investorList = $borrowInvestor->field("id,investor_uid,investor_capital")->where("borrow_id={$borrow_id}")->select();
    m("investor_detail")->where("borrow_id={$borrow_id}")->delete();
    if ($binfo['borrow_type'] == 1) {
        $limit_credit = memberlimitlog($binfo['borrow_uid'], 12, $binfo['borrow_money'], $info = "{$binfo['id']}号标流标");
    }
    $borrowInvestor->startTrans();
    $bstatus = $type == 2 ? 3 : 5;
    $upborrow_info = m("borrow_info")->where("id={$borrow_id}")->setField("borrow_status", $bstatus);
    $buname = m("members")->getFieldById($binfo['borrow_uid'], "user_name");
    if (is_array($investorList)) {
        $upsummary_res = m("borrow_investor")->where("borrow_id={$borrow_id}")->setField("status", $type);
        foreach ($investorList as $v) {
            mtip("chk15", $v['investor_uid'], $borrow_id);
            $accountMoney_investor = m("member_money")->field(true)->find($v['investor_uid']);
            $datamoney_x['uid'] = $v['investor_uid'];
            $datamoney_x['type'] = $type == 3 ? 16 : 8;
            $datamoney_x['affect_money'] = $v['investor_capital'];
            $datamoney_x['account_money'] = $accountMoney_investor['account_money'] + $datamoney_x['affect_money'];
            $datamoney_x['collect_money'] = $accountMoney_investor['money_collect'];
            $datamoney_x['freeze_money'] = $accountMoney_investor['money_freeze'] - $datamoney_x['affect_money'];
            $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
            $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
            $mmoney_x['account_money'] = $datamoney_x['account_money'];
            $_xstr = $type == 3 ? "复审未通过" : "募集期内标未满,流标";
            $datamoney_x['info'] = "第{$borrow_id}号标" . $_xstr . "，返回冻结资金";
            $datamoney_x['add_time'] = time();
            $datamoney_x['add_ip'] = get_client_ip();
            $datamoney_x['target_uid'] = $binfo['borrow_uid'];
            $datamoney_x['target_uname'] = $buname;
            $moneynewid_x = m("member_moneylog")->add($datamoney_x);
            if ($moneynewid_x) {
                $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
            }
        }
    } else {
        $moneynewid_x = true;
        $bxid = true;
        $upsummary_res = true;
    }
    if ($moneynewid_x && $upsummary_res && $bxid && $upborrow_info) {
        $done = true;
        $borrowInvestor->commit();
    } else {
        $borrowInvestor->rollback();
    }
    return $done;
}

function borrowApproved($borrow_id)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowInvestor = d("borrow_investor");
    $binfo = m("borrow_info")->field("borrow_type,reward_type,reward_num,borrow_fee,borrow_money,borrow_uid,borrow_duration,repayment_type")->find($borrow_id);
    $investorList = $borrowInvestor->field("id,investor_uid,investor_capital,investor_interest")->where("borrow_id={$borrow_id}")->select();
    $endTime = strtotime(date("Y-m-d", time()) . " 23:59:59");
    if ($binfo['repayment_type'] == 1) {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} day", $endTime);
    } else {
        $deadline_last = strtotime("+{$binfo['borrow_duration']} month", $endTime);
    }
    $borrowInvestor->startTrans();
    $_investor_num = count($investorList);
    foreach ($investorList as $key => $v) {
        if (0 < $binfo['reward_type']) {
            if ($binfo['reward_type'] == 1) {
                $_reward_money = getfloatvalue($v['investor_capital'] * $binfo['reward_num'] / 100, 2);
            } else if ($binfo['reward_type'] == 2) {
                $_reward_money = getfloatvalue($binfo['reward_num'] / $_investor_num, 2);
            }
        }
        $investorList[$key]['reward_money'] = floatval($_reward_money);
        mtip("chk14", $v['investor_uid'], $borrow_id);
        $saveinfo = array();
        $saveinfo['id'] = $v['id'];
        $saveinfo['reward_money'] = floatval($_reward_money);
        $saveinfo['deadline'] = $deadline_last;
        $saveinfo['status'] = 4;
        $upsummary_res = $borrowInvestor->save($saveinfo);
    }
    $upborrow_res = m()->execute("update `{$pre}borrow_info` set `deadline`={$deadline_last} WHERE `id`={$borrow_id}");
    switch ($binfo['repayment_type']) {
        case 2 :
        case 3 :
        case 4 :
            $i = 1;
            for (; $i <= $binfo['borrow_duration']; ++$i) {
                $deadline = 0;
                $deadline = strtotime("+{$i} month", $endTime);
                $updetail_res = m()->execute("update `{$pre}investor_detail` set `deadline`={$deadline},`status`=7 WHERE `borrow_id`={$borrow_id} AND `sort_order`={$i}");
                break;
            }
        case 1 :
            $deadline = 0;
            $deadline = $deadline_last;
            $updetail_res = m()->execute("update `{$pre}investor_detail` set `deadline`={$deadline},`status`=7 WHERE `borrow_id`={$borrow_id}");
            break;
    }
    if ($updetail_res && $upsummary_res && $upborrow_res) {
        $done = true;
        $borrowInvestor->commit();
        $_P_fee = get_global_setting();
        $_borraccount = memberMoneyLog($binfo['borrow_uid'], 17, $binfo['borrow_money'], "第{$borrow_id}号标复审通过，借款金额入帐");
        if (!$_borraccount) {
            return false;
        }
        $_borrfee = memberMoneyLog($binfo['borrow_uid'], 18, 0 - $binfo['borrow_fee'], "第{$borrow_id}号标借款成功，扣除借款管理费");
        if (!$_borrfee) {
            return false;
        }
        $_freezefee = memberMoneyLog($binfo['borrow_uid'], 19, 0 - $binfo['borrow_money'] * $_P_fee['money_deposit'] / 100, "第{$borrow_id}号标借款成功，冻结{$_P_fee['money_deposit']}%的保证金");
        if (!$_freezefee) {
            return false;
        }
        $_investor_num = count($investorList);
        $_remoney_do = true;
        foreach ($investorList as $v) {
            if (0 < $v['reward_money']) {
                $_remoney_do = false;
                $_reward_m = memberMoneyLog($v['investor_uid'], 20, $v['reward_money'], "第{$borrow_id}号标复审通过，获取投标奖励", $binfo['borrow_uid']);
                $_reward_m_give = memberMoneyLog($binfo['borrow_uid'], 21, 0 - $v['reward_money'], "第{$borrow_id}号标复审通过，支付投标奖励", $v['investor_uid']);
                if ($_reward_m && $_reward_m_give) {
                    $_remoney_do = true;
                }
            }
            $remcollect = memberMoneyLog($v['investor_uid'], 15, $v['investor_capital'], "第{$borrow_id}号标复审通过，冻结本金成为待收金额", $binfo['borrow_uid']);
            $reinterestcollect = memberMoneyLog($v['investor_uid'], 28, $v['investor_interest'], "第{$borrow_id}号标复审通过，应收利息成为待收金额", $binfo['borrow_uid']);
        }
        if (!$_remoney_do || !$remcollect || !$reinterestcollect) {
            return false;
        }
    } else {
        $borrowInvestor->rollback();
    }
    return $done;
}

function lastRepayment($binfo)
{
    $x = true;
    if ($binfo['borrow_type'] == 2) {
        $x = false;
        $x = memberlimitlog($binfo['borrow_uid'], 8, $binfo['borrow_money'], $info = "{$binfo['id']}号标还款完成");
        if (!$x) {
            return false;
        }
        $vocuhlist = m("borrow_vouch")->field("uid,vouch_money")->where("borrow_id={$binfo['id']}")->select();
        foreach ($vocuhlist as $vv) {
            $x = memberlimitlog($vv['uid'], 10, $vv['vouch_money'], $info = "您担保的{$binfo['id']}号标还款完成");
        }
    } else if ($binfo['borrow_type'] == 1) {
        $x = false;
        $x = memberlimitlog($binfo['borrow_uid'], 7, $binfo['borrow_money'], $info = "{$binfo['id']}号标还款完成");
    }
    if (!$x) {
        return false;
    }
    $_P_fee = get_global_setting();
    $accountMoney_borrower = m("member_money")->field("account_money,money_collect,money_freeze")->find($binfo['borrow_uid']);
    $datamoney_x['uid'] = $binfo['borrow_uid'];
    $datamoney_x['type'] = 24;
    $datamoney_x['affect_money'] = $binfo['borrow_money'] * $_P_fee['money_deposit'] / 100;
    $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
    $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
    $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'] - $datamoney_x['affect_money'];
    $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
    $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
    $mmoney_x['account_money'] = $datamoney_x['account_money'];
    $datamoney_x['info'] = "对{$binfo['id']}还款完成的解冻保证金";
    $datamoney_x['add_time'] = time();
    $datamoney_x['add_ip'] = get_client_ip();
    $datamoney_x['target_uid'] = 0;
    $datamoney_x['target_uname'] = "@网站管理员@";
    $moneynewid_x = m("member_moneylog")->add($datamoney_x);
    if ($moneynewid_x) {
        $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
    }
    if ($bxid && $x) {
        return true;
    } else {
        return false;
    }
}

function borrowRepayment($borrow_id, $sort_order, $type = 1)
{
    $pre = c("DB_PREFIX");
    $done = false;
    $borrowDetail = d("investor_detail");
    $binfo = m("borrow_info")->field("id,borrow_uid,borrow_type,borrow_money,borrow_duration,repayment_type,has_pay,total,deadline")->find($borrow_id);
    $b_member = m("members")->field("user_name")->find($binfo['borrow_uid']);
    if ($sort_order <= $binfo['has_pay']) {
        return "本期已还过，不用再还";
    }
    if ($binfo['has_pay'] == $binfo['total']) {
        return "此标已经还完，不用再还";
    }
    if ($binfo['has_pay'] + 1 < $sort_order) {
        return (("对不起，此借款第" . ($binfo['has_pay'] + 1)) . "期还未还，请先还第" . ($binfo['has_pay'] + 1)) . "期";
    }
    if (time() < $binfo['deadline'] && $type == 2) {
        return "此标还没逾期，不用代还";
    }
    $voxe = $borrowDetail->field("sort_order,sum(capital) as capital, sum(interest) as interest,deadline,substitute_time")->where("borrow_id={$borrow_id}")->group("sort_order")->select();
    foreach ($voxe as $ee => $ss) {
        if ($ss['sort_order'] == $sort_order) {
            $vo = $ss;
        }
    }
    if ($vo['deadline'] < time()) {
        $is_expired = true;
        if (0 < $vo['substitute_time']) {
            $is_substitute = true;
        } else {
            $is_substitute = false;
        }
        $expired_days = getexpireddays($vo['deadline']);
        $expired_money = getexpiredmoney($expired_days, $vo['capital'], $vo['interest']);
        $call_fee = getexpiredcallfee($expired_days, $vo['capital'], $vo['interest']);
    } else {
        $is_expired = false;
        $expired_days = 0;
        $expired_money = 0;
        $call_fee = 0;
    }
    mtip("chk25", $binfo['borrow_uid'], $borrow_id);
    $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
    if ($type == 1 && $binfo['borrow_type'] != 3 && $accountMoney_borrower['account_money'] < $vo['capital'] + $vo['interest'] + $expired_money + $call_fee) {
        return "帐户可用余额不足，本期还款共需" . ($vo['capital'] + $vo['interest'] + $expired_money + $call_fee) . "元，请先充值";
    }
    if ($is_substitute && $is_expired) {
        $borrowDetail->startTrans();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 11;
        $datamoney_x['affect_money'] = 0 - ($vo['capital'] + $vo['interest']);
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "对{$borrow_id}号标第{$sort_order}期还款";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
        $datamoney_x = array();
        $mmoney_x = array();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 30;
        $datamoney_x['affect_money'] = 0 - $expired_money;
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "{$borrow_id}号标第{$sort_order}期的逾期罚息";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
        $datamoney_x = array();
        $mmoney_x = array();
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 31;
        $datamoney_x['affect_money'] = 0 - $call_fee;
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "{$borrow_id}号标第{$sort_order}期的逾期催收费";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `repayment_time`=" . time() . " WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
        if ($updetail_res && $bxid) {
            $borrowDetail->commit();
            return true;
        } else {
            $borrowDetail->rollback();
            return false;
        }
    }
    $detailList = $borrowDetail->field("invest_id,investor_uid,capital,interest,interest_fee,borrow_id,total")->where("borrow_id={$borrow_id} AND sort_order={$sort_order}")->select();
    if ($type == 1) {
        $day_span = ceil(($vo['deadline'] - time()) / 86400);
        $credits_money = intval($vo['capital'] / 100);
        $credits_info = "对第{$borrow_id}号标的还款操作";
        if (0 < $day_span && $day_span <= 3) {
            $credits_result = membercreditslog($binfo['borrow_uid'], 3, $credits_money, $credits_info);
            $idetail_status = 1;
        } else if (-3 < $day_span && $day_span <= 0) {
            $credits_result = membercreditslog($binfo['borrow_uid'], 4, $credits_money * -3, $credits_info);
            $idetail_status = 3;
        } else if ($day_span <= -3) {
            $credits_result = membercreditslog($binfo['borrow_uid'], 5, $credits_money * -10, $credits_info);
            $idetail_status = 5;
        } else if (3 < $day_span) {
            $credits_result = membercreditslog($binfo['borrow_uid'], 6, $credits_money * 2, $credits_info);
            $idetail_status = 2;
        }
        if (!$credits_result) {
            return "因积分记录失败，未完成还款操作";
        }
    }
    $borrowDetail->startTrans();
    $bxid = true;
    if ($type == 1) {
        $bxid = false;
        $datamoney_x['uid'] = $binfo['borrow_uid'];
        $datamoney_x['type'] = 11;
        $datamoney_x['affect_money'] = 0 - ($vo['capital'] + $vo['interest']);
        $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
        $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
        $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
        $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
        $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
        $mmoney_x['account_money'] = $datamoney_x['account_money'];
        $datamoney_x['info'] = "对{$borrow_id}号标第{$sort_order}期还款";
        $datamoney_x['add_time'] = time();
        $datamoney_x['add_ip'] = get_client_ip();
        $datamoney_x['target_uid'] = 0;
        $datamoney_x['target_uname'] = "@网站管理员@";
        $moneynewid_x = m("member_moneylog")->add($datamoney_x);
        if ($moneynewid_x) {
            $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
        }
        if ($is_expired) {
            if (0 < $expired_money) {
                $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
                $datamoney_x = array();
                $mmoney_x = array();
                $datamoney_x['uid'] = $binfo['borrow_uid'];
                $datamoney_x['type'] = 30;
                $datamoney_x['affect_money'] = 0 - $expired_money;
                $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
                $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
                $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
                $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
                $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                $mmoney_x['account_money'] = $datamoney_x['account_money'];
                $datamoney_x['info'] = "{$borrow_id}号标第{$sort_order}期的逾期罚息";
                $datamoney_x['add_time'] = time();
                $datamoney_x['add_ip'] = get_client_ip();
                $datamoney_x['target_uid'] = 0;
                $datamoney_x['target_uname'] = "@网站管理员@";
                $moneynewid_x = m("member_moneylog")->add($datamoney_x);
                if ($moneynewid_x) {
                    $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                }
            }
            if (0 < $call_fee) {
                $accountMoney_borrower = m("member_money")->field("money_freeze,money_collect,account_money")->find($binfo['borrow_uid']);
                $datamoney_x = array();
                $mmoney_x = array();
                $datamoney_x['uid'] = $binfo['borrow_uid'];
                $datamoney_x['type'] = 31;
                $datamoney_x['affect_money'] = 0 - $call_fee;
                $datamoney_x['account_money'] = $accountMoney_borrower['account_money'] + $datamoney_x['affect_money'];
                $datamoney_x['collect_money'] = $accountMoney_borrower['money_collect'];
                $datamoney_x['freeze_money'] = $accountMoney_borrower['money_freeze'];
                $mmoney_x['money_freeze'] = $datamoney_x['freeze_money'];
                $mmoney_x['money_collect'] = $datamoney_x['collect_money'];
                $mmoney_x['account_money'] = $datamoney_x['account_money'];
                $datamoney_x['info'] = "{$borrow_id}号标第{$sort_order}期的逾期催收费";
                $datamoney_x['add_time'] = time();
                $datamoney_x['add_ip'] = get_client_ip();
                $datamoney_x['target_uid'] = 0;
                $datamoney_x['target_uname'] = "@网站管理员@";
                $moneynewid_x = m("member_moneylog")->add($datamoney_x);
                if ($moneynewid_x) {
                    $bxid = m("member_money")->where("uid={$datamoney_x['uid']}")->save($mmoney_x);
                }
            }
        }
    }
    $upborrowsql = "update `{$pre}borrow_info` set ";
    $upborrowsql .= "`repayment_money`=`repayment_money`+{$vo['capital']}";
    $upborrowsql .= ",`repayment_interest`=`repayment_interest`+ {$vo['interest']}";
    if ($sort_order == $binfo['total']) {
        $upborrowsql .= ",`borrow_status`=7";
    }
    if ($type == 2) {
        $total_subs = $vo['capital'] + $vo['interest'];
        $upborrowsql .= ",`substitute_money`=`substitute_money`+ {$total_subs}";
    }
    if ($type == 1) {
        $upborrowsql .= ",`has_pay`={$sort_order}";
    }
    if ($is_expired) {
        $upborrowsql .= ",`expired_money`=`expired_money`+{$expired_money}";
    }
    $upborrowsql .= " WHERE `id`={$borrow_id}";
    $upborrow_res = m()->execute($upborrowsql);
    if ($type == 2) {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital`,`substitute_time`=" . time() . " ,`substitute_money`=`substitute_money`+{$total_subs},`status`=4 WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    } else if ($is_expired) {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital` ,`receive_interest`=(`interest`-`interest_fee`),`repayment_time`=" . time() . ",`call_fee`={$call_fee},`expired_money`={$expired_money},`expired_days`={$expired_days},`status`={$idetail_status} WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    } else {
        $updetail_res = m()->execute("update `{$pre}investor_detail` set `receive_capital`=`capital` ,`receive_interest`=(`interest`-`interest_fee`),`repayment_time`=" . time() . ", `status`={$idetail_status} WHERE `borrow_id`={$borrow_id} AND `sort_order`={$sort_order}");
    }
    $smsUid = "";
    foreach ($detailList as $v) {
        $getInterest = $v['interest'] - $v['interest_fee'];
        $upsql = "update `{$pre}borrow_investor` set ";
        $upsql .= "`receive_capital`=`receive_capital`+{$v['capital']},";
        $upsql .= "`receive_interest`=`receive_interest`+ {$getInterest},";
        if ($type == 2) {
            $total_s_invest = $v['capital'] + $getInterest;
            $upsql .= "`substitute_money` = `substitute_money` + {$total_s_invest},";
        }
        if ($sort_order == $binfo['total']) {
            $upsql .= "`status`=5,";
        }
        $upsql .= "`paid_fee`=`paid_fee`+{$v['interest_fee']}";
        $upsql .= " WHERE `id`={$v['invest_id']}";
        $upinfo_res = m()->execute($upsql);
        if ($upinfo_res) {
            $accountMoney = m("member_money")->field("money_freeze,money_collect,account_money")->find($v['investor_uid']);
            $datamoney['uid'] = $v['investor_uid'];
            $datamoney['type'] = $type == 2 ? "10" : "9";
            $datamoney['affect_money'] = $v['capital'] + $v['interest'];
            $datamoney['account_money'] = $accountMoney['account_money'] + $datamoney['affect_money'];
            $datamoney['collect_money'] = $accountMoney['money_collect'] - $datamoney['affect_money'];
            $datamoney['freeze_money'] = $accountMoney['money_freeze'];
            $mmoney['money_freeze'] = $datamoney['freeze_money'];
            $mmoney['money_collect'] = $datamoney['collect_money'];
            $mmoney['account_money'] = $datamoney['account_money'];
            $datamoney['info'] = $type == 2 ? "网站对{$v['borrow_id']}号标第{$sort_order}期代还" : "会员对{$v['borrow_id']}号标第{$sort_order}期还款";
            $datamoney['add_time'] = time();
            $datamoney['add_ip'] = get_client_ip();
            if ($type == 2) {
                $datamoney['target_uid'] = 0;
                $datamoney['target_uname'] = "@网站管理员@";
            } else {
                $datamoney['target_uid'] = $binfo['borrow_uid'];
                $datamoney['target_uname'] = $b_member['user_name'];
            }
            $moneynewid = m("member_moneylog")->add($datamoney);
            if ($moneynewid) {
                $xid = m("member_money")->where("uid={$datamoney['uid']}")->save($mmoney);
            }
            if ($type == 2) {
                mtip("chk18", $v['investor_uid'], $borrow_id);
            } else {
                mtip("chk16", $v['investor_uid'], $borrow_id);
            }
            $smsUid .= empty($smsUid) ? $v['investor_uid'] : ",{$v['investor_uid']}";
            $xid_z = true;
            if (0 < $v['interest_fee'] && $type == 1) {
                $xid_z = false;
                $accountMoney = m("member_money")->field("money_freeze,money_collect,account_money")->find($v['investor_uid']);
                $datamoney_z['uid'] = $v['investor_uid'];
                $datamoney_z['type'] = 23;
                $datamoney_z['affect_money'] = 0 - $v['interest_fee'];
                $datamoney_z['account_money'] = $mmoney['account_money'] + $datamoney_z['affect_money'];
                $datamoney_z['collect_money'] = $mmoney['money_collect'];
                $datamoney_z['freeze_money'] = $mmoney['money_freeze'];
                $mmoney_z['money_freeze'] = $datamoney_z['freeze_money'];
                $mmoney_z['money_collect'] = $datamoney_z['collect_money'];
                $mmoney_z['account_money'] = $datamoney_z['account_money'];
                $datamoney_z['info'] = "收到第{$v['borrow_id']}号标第{$sort_order}期还款的利息管理费";
                $datamoney_z['add_time'] = time();
                $datamoney_z['add_ip'] = get_client_ip();
                $datamoney_z['target_uid'] = 0;
                $datamoney_z['target_uname'] = "@网站管理员@";
                $moneynewid_z = m("member_moneylog")->add($datamoney_z);
                if ($moneynewid_z) {
                    $xid_z = m("member_money")->where("uid={$datamoney_z['uid']}")->save($mmoney_z);
                }
            }
        }
    }
    if ($updetail_res && $upinfo_res && $xid && $upborrow_res && $bxid && $xid_z) {
        $borrowDetail->commit();
        $_last = true;
        if ($binfo['total'] == $binfo['has_pay'] + 1 && $type == 1) {
            $_last = false;
            $_is_last = lastrepayment($binfo);
            if ($_is_last) {
                $_last = true;
            }
        }
        if ($_last === false) {
            return "因满标操作未完成，还款操作失败";
        }
        $done = true;
        $vphone = m("members")->field("user_phone")->where("id in({$smsUid})")->select();
        $sphone = "";
        foreach ($vphone as $v) {
            $sphone .= empty($sphone) ? $v['user_phone'] : ",{$v['user_phone']}";
        }
        smstip("payback", $sphone, array(
            "#ID#",
            "#ORDER#"
        ), array(
            $borrow_id,
            $sort_order
        ));
    } else {
        $borrowDetail->rollback();
    }
    return $done;
}

function getBorrowInterestRate($rate, $duration)
{
    return $rate / 1200 * $duration;
}

function sendsms($mob, $content)
{
    $msgconfig = fs("Webconfig/msgconfig");
    $uid = $msgconfig['sms']['user'];
    $pwd = $msgconfig['sms']['pass'];
    $mob = $mob;
    $content = urlencode(auto_charset($content, "utf-8", "gbk"));
    $sendurl = "http://service.winic.org:8009/sys_port/gateway/?id=" . $uid . "&pwd=" . $pwd . "&to=" . $mob . "&content=" . $content . "&time=";
    $xhr = new COM("MSXML2.XMLHTTP");
    $xhr->open("GET", $sendurl, false);
    $xhr->send();
    $data = explode("/", $xhr->responseText);
    if ($data[0] == "000") {
        return true;
    } else {
        return false;
    }
}

function getMoneyLog($map, $size)
{
    if (empty($map['uid'])) {
        return;
    }
    if ($size) {
        import("ORG.Util.Page");
        $count = m("member_moneylog")->where($map)->count("id");
        $p = new Page($count, $size);
        $page = $p->show();
        $Lsql = "{$p->firstRow},{$p->listRows}";
    }
    $list = m("member_moneylog")->where($map)->order("id DESC")->limit($Lsql)->select();
    $type_arr = c("MONEY_LOG");
    foreach ($list as $key => $v) {
        $list[$key]['type'] = $type_arr[$v['type']];
    }
    $row = array();
    $row['list'] = $list;
    $row['page'] = $page;
    return $row;
}

function memberMoneyLog($uid, $type, $amoney, $info = "", $target_uid = 0, $target_uname = "")
{
    $xva = floatval($amoney);
    if (empty($xva)) {
        return true;
    }
    $done = false;
    $MM = m("member_money")->field("money_freeze,money_collect,account_money")->find($uid);
    if (!is_array($MM)) {
        m("member_money")->add(array(
            "uid" => $uid
        ));
    }
    $Moneylog = d("member_moneylog");
    if (in_array($type, array("71", "72", "73"))) {
        $type_save = 7;
    } else {
        $type_save = $type;
    }
    if (empty($target_uname) && 0 < $target_uid) {
        $tname = m("members")->getFieldById($target_uid, "user_name");
    } else {
        $tname = $target_uname;
    }
    if (empty($target_uid) && empty($target_uname)) {
        $target_uid = 0;
        $target_uname = "@网站管理员@";
    }
    $Moneylog->startTrans();
    $data['uid'] = $uid;
    $data['type'] = $type_save;
    $data['info'] = $info;
    $data['target_uid'] = $target_uid;
    $data['target_uname'] = $tname;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    switch ($type) {
        case 4 :
        case 5 :
        case 6 :
        case 8 :
        case 12 :
        case 19 :
        case 24 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'] + $amoney;
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'] - $amoney;
            break;
        case 3 :
        case 17 :
        case 18 :
        case 20 :
        case 21 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'] + $amoney;
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            break;
        case 9 :
        case 10 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'] + $amoney;
            $data['collect_money'] = $MM['money_collect'] - $amoney;
            $data['freeze_money'] = $MM['money_freeze'];
            break;
        case 15 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'] + $amoney;
            $data['freeze_money'] = $MM['money_freeze'] - $amoney;
            break;
        case 28 :
        case 73 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'] + $amoney;
            $data['freeze_money'] = $MM['money_freeze'];
            break;
        case 29 :
        case 72 :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'];
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'] + $amoney;
            break;
        case 71 :
        default :
            $data['affect_money'] = $amoney;
            $data['account_money'] = $MM['account_money'] + $amoney;
            $data['collect_money'] = $MM['money_collect'];
            $data['freeze_money'] = $MM['money_freeze'];
            break;
    }
    $newid = m("member_moneylog")->add($data);
    $mmoney['money_freeze'] = $data['freeze_money'];
    $mmoney['money_collect'] = $data['collect_money'];
    $mmoney['account_money'] = $data['account_money'];
    if ($newid) {
        $xid = m("member_money")->where("uid={$uid}")->save($mmoney);
    }
    if ($xid) {
        $Moneylog->commit();
        $done = true;
    } else {
        $Moneylog->rollback();
    }
    return $done;
}

function memberLimitLog($uid, $type, $alimit, $info = "")
{
    $xva = floatval($alimit);
    if (empty($xva)) {
        return true;
    }
    $done = false;
    $MM = m("member_money")->field("money_freeze,money_collect,account_money", true)->find($uid);
    if (!is_array($MM)) {
        m("member_money")->add(array(
            "uid" => $uid
        ));
    }
    $Moneylog = d("member_moneylog");
    if (in_array($type, array("71", "72", "73"))) {
        $type_save = 7;
    } else {
        $type_save = $type;
    }
    $Moneylog->startTrans();
    $data['uid'] = $uid;
    $data['type'] = $type_save;
    $data['info'] = $info;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    $data['credit_limit'] = 0;
    $data['borrow_vouch_limit'] = 0;
    $data['invest_vouch_limit'] = 0;
    switch ($type) {
        case 1 :
        case 4 :
        case 7 :
        case 12 :
            $_data['credit_limit'] = $alimit;
            break;
        case 2 :
        case 5 :
        case 8 :
            $_data['borrow_vouch_limit'] = $alimit;
            break;
        case 3 :
        case 6 :
        case 9 :
        case 10 :
            $_data['invest_vouch_limit'] = $alimit;
            break;
        case 11 :
            $_data['credit_limit'] = $alimit;
            $mmoney['credit_limit'] = $MM['credit_limit'] + $_data['credit_limit'];
            break;
    }
    $data = array_merge($data, $_data);
    $newid = m("member_limitlog")->add($data);
    $mmoney['credit_cuse'] = $MM['credit_cuse'] + $data['credit_limit'];
    $mmoney['borrow_vouch_cuse'] = $MM['borrow_vouch_cuse'] + $data['borrow_vouch_limit'];
    $mmoney['invest_vouch_cuse'] = $MM['invest_vouch_cuse'] + $data['invest_vouch_limit'];
    if ($newid) {
        $xid = m("member_money")->where("uid={$uid}")->save($mmoney);
    }
    if ($xid) {
        $Moneylog->commit();
        $done = true;
    } else {
        $Moneylog->rollback();
    }
    return $done;
}

function memberCreditsLog($uid, $type, $acredits, $info = "无")
{
    if ($acredits == 0) {
        return true;
    }
    $done = false;
    $mCredits = m("members")->getFieldById($uid, "credits");
    $Creditslog = d("member_creditslog");
    $Creditslog->startTrans();
    $data['uid'] = $uid;
    $data['type'] = $type;
    $data['affect_credits'] = $acredits;
    $data['account_credits'] = $mCredits + $acredits;
    $data['info'] = $info;
    $data['add_time'] = time();
    $data['add_ip'] = get_client_ip();
    $newid = $Creditslog->add($data);
    $xid = m("members")->where("id={$uid}")->setField("credits", $data['account_credits']);
    if ($xid) {
        $Creditslog->commit();
        $done = true;
    } else {
        $Creditslog->rollback();
    }
    return $done;
}

function getMemberMoneySummary($uid)
{
    $pre = c("DB_PREFIX");
    $umoney = m("member_money")->field(true)->find($uid);
    $withdraw = m("member_withdraw")->field("withdraw_status,sum(withdraw_money) as withdraw_money,sum(withdraw_fee) as withdraw_fee")->where("uid={$uid}")->group("withdraw_status")->select();
    $withdraw_row = array();
    foreach ($withdraw as $wkey => $wv) {
        $withdraw_row[$wv['withdraw_status']] = $wv;
    }
    $withdraw0 = $withdraw_row[0];
    $withdraw1 = $withdraw_row[1];
    $withdraw2 = $withdraw_row[2];
    $payonline = m("member_payonline")->where("uid={$uid} AND status=1")->sum("money");
    $commission1 = m("borrow_investor")->where("investor_uid={$uid}")->sum("paid_fee");
    $commission2 = m("borrow_info")->where("borrow_uid={$uid} AND borrow_status in(3,4)")->sum("borrow_fee");
    $uplevefee = m("member_moneylog")->where("uid={$uid} AND type=2")->sum("affect_money");
    $czfee = m("member_payonline")->where("uid={$uid} AND status=1")->sum("fee");
    $capitalinfo = getmemberborrowscan($uid);
    $money['zye'] = $umoney['account_money'] + $umoney['money_collect'] + $umoney['money_freeze'];
    $money['kyxjje'] = $umoney['account_money'];
    $money['djje'] = $umoney['money_freeze'];
    $money['jjje'] = 0;
    $money['dsbx'] = $umoney['money_collect'];
    $money['dfbx'] = $capitalinfo['tj']['dhze'];
    $money['dxrtb'] = $capitalinfo['tj']['dqrtb'];
    $money['dshtx'] = $withdraw0['withdraw_money'];
    $money['clztx'] = $withdraw1['withdraw_money'];
    $money['total_1'] = $money['kyxjje'] + $money['jjje'] + $money['dsbx'] - $money['dfbx'] + $money['dxrtb'] + $money['dshtx'] + $money['clztx'];
    $money['jzlx'] = $capitalinfo['tj']['earnInterest'];
    $money['jflx'] = $capitalinfo['tj']['payInterest'];
    $money['ljjj'] = $umoney['reward_money'];
    $money['ljhyf'] = $uplevefee;
    $money['ljtxsxf'] = $withdraw2['withdraw_fee'];
    $money['ljczsxf'] = $czfee;
    $money['total_2'] = $money['jzlx'] - $money['jflx'] + $money['ljjj'] - $money['ljhyf'] - $money['ljtxsxf'] - $money['ljczsxf'];
    $money['ljtzje'] = $capitalinfo['tj']['borrowOut'];
    $money['ljjrje'] = $capitalinfo['tj']['borrowIn'];
    $money['ljczje'] = $payonline;
    $money['ljtxje'] = $withdraw2['withdraw_money'];
    $money['ljzfyj'] = $commission1 + $commission2;
    $money['dslxze'] = $capitalinfo['tj']['willgetInterest'];
    $money['dflxze'] = $capitalinfo['tj']['willpayInterest'];
    return $money;
}

function getBorrowInvest($borrowid = 0, $uid)
{
    if (empty($borrowid)) {
        return;
    }
    $vx = m("borrow_info")->field("id")->where("id={$borrowid} AND borrow_uid={$uid}")->find();
    if (!is_array($vx)) {
        return;
    }
    $binfo = m("borrow_info")->field("borrow_name,borrow_uid,borrow_type,borrow_duration,repayment_type,has_pay,total,deadline")->find($borrowid);
    $list = array();
    switch ($binfo['repayment_type']) {
        case 1 :
            $field = "borrow_id,sort_order,sum(capital) as capital,sum(interest) as interest,status,sum(receive_interest+receive_capital+if(receive_capital>0,interest_fee,0)) as paid,deadline";
            $vo = m("investor_detail")->field($field)->where("borrow_id={$borrowid} AND `sort_order`=1")->group("sort_order")->find();
            $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
            $vo['status'] = $status_arr[$vo['status']];
            $vo['needpay'] = getfloatvalue(sprintf("%.2f", $vo['interest'] + $vo['capital'] - $vo['paid']), 2);
            $list[] = $vo;
            break;
        default :
            $i = 1;
            for (; $i <= $binfo['borrow_duration']; ++$i) {
                $field = "borrow_id,sort_order,sum(capital) as capital,sum(interest) as interest,status,sum(receive_interest+receive_capital+if(receive_capital>0,interest_fee,0)) as paid,deadline";
                $vo = m("investor_detail")->field($field)->where("borrow_id={$borrowid} AND `sort_order`={$i}")->group("sort_order")->find();
                $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
                $vo['status'] = $status_arr[$vo['status']];
                $vo['needpay'] = getfloatvalue(sprintf("%.2f", $vo['interest'] + $vo['capital'] - $vo['paid']), 2);
                $list[] = $vo;
                break;
            }
    }
    $row = array();
    $row['list'] = $list;
    $row['name'] = $binfo['borrow_name'];
    return $row;
}

function getDurationCount($uid = 0)
{
    if (empty($uid)) {
        return;
    }
    $pre = c("DB_PREFIX");
    $field = "d.status,d.repayment_time";
    $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_uid={$uid}) group by d.borrow_id, d.sort_order";
    $list = m()->query($sql);
    $week_1 = array(
        strtotime("-7 day", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $time_1 = array(
        strtotime("-1 month", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $time_6 = array(
        strtotime("-6 month", strtotime(date("Y-m-d", time()) . " 00:00:00")),
        strtotime(date("Y-m-d", time()) . " 23:59:59")
    );
    $row_time_1 = array();
    $row_time_2 = array();
    $row_time_3 = array();
    $row_time_4 = array();
    foreach ($list as $v) {
        switch ($v['status']) {
            case 1 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['zc'] = $row_time_3['zc'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['zc'] = $row_time_1['zc'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['zc'] = $row_time_2['zc'] + 1;
                    }
                }
                $row_time_4['zc'] = $row_time_4['zc'] + 1;
                break;
            case 2 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['tq'] = $row_time_3['tq'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['tq'] = $row_time_1['tq'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['tq'] = $row_time_2['tq'] + 1;
                    }
                }
                $row_time_4['tq'] = $row_time_4['tq'] + 1;
                break;
            case 3 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['ch'] = $row_time_3['ch'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['ch'] = $row_time_1['ch'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['ch'] = $row_time_2['ch'] + 1;
                    }
                }
                $row_time_4['ch'] = $row_time_4['ch'] + 1;
                break;
            case 5 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['yq'] = $row_time_3['yq'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['yq'] = $row_time_1['yq'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['yq'] = $row_time_2['yq'] + 1;
                    }
                }
                $row_time_4['yq'] = $row_time_4['yq'] + 1;
                break;
            case 6 :
                if ($time_6[0] < $v['repayment_time'] && $v['repayment_time'] < $time_6[1]) {
                    $row_time_3['wh'] = $row_time_3['wh'] + 1;
                    if ($week_1[0] < $v['repayment_time'] && $v['repayment_time'] < $week_1[1]) {
                        $row_time_1['wh'] = $row_time_1['wh'] + 1;
                    }
                    if ($time_1[0] < $v['repayment_time'] && $v['repayment_time'] < $time_1[1]) {
                        $row_time_2['wh'] = $row_time_2['wh'] + 1;
                    }
                }
                $row_time_4['wh'] = $row_time_4['wh'] + 1;
                break;
        }
    }
    $row['history1'] = $row_time_1;
    $row['history2'] = $row_time_2;
    $row['history3'] = $row_time_3;
    $row['history4'] = $row_time_4;
    return $row;
}

function getMemberBorrow($uid = 0, $size = 10)
{
    if (empty($uid)) {
        return;
    }
    $pre = c("DB_PREFIX");
    $field = "b.borrow_name,d.total,d.borrow_id,d.sort_order,sum(d.capital) as capital,sum(d.interest) as interest,d.status,sum(d.receive_interest+d.receive_capital+if(d.receive_capital>0,d.interest_fee,0)) as paid,d.deadline";
    $sql = "select {$field} from {$pre}investor_detail d left join {$pre}borrow_info b ON b.id=d.borrow_id where d.borrow_id in(select tb.id from {$pre}borrow_info tb where tb.borrow_status=6 AND tb.borrow_uid={$uid}) AND d.repayment_time=0 group by d.sort_order, d.borrow_id order by  d.borrow_id,d.sort_order limit 0,10";
    $list = m()->query($sql);
    $status_arr = array("还未还", "已还完", "已提前还款", "愈期还款", "网站代还本金");
    foreach ($list as $key => $v) {
        $list[$key]['status'] = $status_arr[$v['status']];
    }
    $row = array();
    $row['list'] = $list;
    return $row;
}

function getLeftTime($timeend, $type = 1)
{
    if ($type == 1) {
        $timeend = strtotime(date("Y-m-d", $timeend) . " 23:59:59");
        $timenow = strtotime(date("Y-m-d", time()) . " 23:59:59");
        $left = ceil(($timeend - $timenow) / 3600 / 24);
    } else {
        $left_arr = timediff(time(), $timeend);
        $left = $left_arr['day'] . "天 " . $left_arr['hour'] . "小时 " . $left_arr['min'] . "分钟 " . $left_arr['sec'] . "秒";
    }
    return $left;
}

function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain %= 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array(
        "day" => $days,
        "hour" => $hours,
        "min" => $mins,
        "sec" => $secs
    );
    return $res;
}

function addInnerMsg($uid, $title, $msg)
{
    if (empty($uid)) {
        return;
    }
    $data['uid'] = $uid;
    $data['title'] = $title;
    $data['msg'] = $msg;
    $data['send_time'] = time();
    m("inner_msg")->add($data);
}

function getTypeList($parm)
{
    $Osql = "sort_order DESC";
    $field = "id,type_name,type_set,add_time,type_url,type_nid,parent_id";
    $Lsql = "{$parm['limit']}";
    $pc = d("Acategory")->where("parent_id={$parm['type_id']}")->count("id");
    if (0 < $pc) {
        $map['is_hiden'] = 0;
        $map['parent_id'] = $parm['type_id'];
        $data = d("Acategory")->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
    } else if (!isset($parm['notself'])) {
        $map['is_hiden'] = 0;
        $map['parent_id'] = d("Acategory")->getFieldById($parm['type_id'], "parent_id");
        $data = d("Acategory")->field($field)->where($map)->order($Osql)->limit($Lsql)->select();
    }
    $typefix = get_type_leve_nid($parm['type_id']);
    $typeu = $typefix[0];
    $suffix = c("URL_HTML_SUFFIX");
    foreach ($data as $key => $v) {
        if ($v['type_set'] == 2) {
            if (empty($v['type_url'])) {
                $data[$key]['turl'] = "javascript:alert('请在后台添加此栏目链接');";
            } else {
                $data[$key]['turl'] = $v['type_url'];
            }
        } else if ($parm['type_id'] == 0 || $v['parent_id'] == 0 && count($typefix) == 1) {
            $data[$key]['turl'] = mu("Home/{$v['type_nid']}/index", "typelist", array(
                "suffix" => $suffix
            ));
        } else {
            $data[$key]['turl'] = mu("Home/{$typeu}/{$v['type_nid']}", "typelist", array(
                "suffix" => $suffix
            ));
        }
    }
    $row = array();
    $row = $data;
    return $row;
}

function newTip($borrow_id)
{
    $binfo = m("borrow_info")->field("borrow_type,borrow_interest_rate,borrow_duration")->find();
    if ($binfo['borrow_type'] == 3) {
        $map['borrow_type'] = 3;
    } else {
        $map['borrow_type'] = 0;
    }
    $tiplist = m("borrow_tip")->field(true)->where($map)->select();
    foreach ($tiplist as $key => $v) {
        $minfo = m("members")->field("account_money,user_phone")->find($v['uid']);
        if ($v['interest_rate'] <= $binfo['borrow_interest_rate'] && $v['doration_from'] <= $binfo['borrow_duration'] && $binfo['borrow_duration'] <= $v['doration_to'] && $v['account_money'] <= $minfo['account_money']) {
            empty($tipPhone) ? ($tipPhone .= "{$v['user_phone']}") : ($tipPhone .= ",{$v['user_phone']}");
        }
    }
    $smsTxt = fs("Webconfig/smstxt");
    $smsTxt = de_xie($smsTxt);
    sendsms($tipPhone, $smsTxt['newtip']);
}

function autoInvest($borrow_id)
{
    $binfo = m("borrow_info")->field("borrow_uid,borrow_money,borrow_type,repayment_type,borrow_interest_rate,borrow_duration,has_vouch,has_borrow,borrow_max,borrow_min,can_auto")->find($borrow_id);
    if ($binfo['borrow_type'] == 3) {
        return true;
    }
    if ($binfo['can_auto'] == 0) {
        return true;
    }
    if ($binfo['borrow_type'] == 2 && $binfo['has_vouch'] < $binfo['borrow_money']) {
        return true;
    }
    $map['status'] = 1;
    $autolist = m("auto_borrow")->field(true)->where($map)->select();
    $needMoney = $binfo['borrow_money'] - $binfo['has_borrow'];
    $borrow_user_info = m("members")->field("user_type,credits")->find();
    $have_auto_do = array();
    foreach ($autolist as $key => $v) {
        if (in_array($v['uid'], $have_auto_do) || $v['uid'] == $binfo['borrow_uid']) {
            continue;
        }
        if ($v['my_friend'] == 1 || $v['not_black'] == 1) {
            $vo = m("member_friend")->where("uid={$v['uid']} AND friend_id={$binfo['borrow_uid']}")->find();
            if ($v['my_friend'] == 1 && $vo['apply_status'] != 1) {
                continue;
            }
            if ($v['not_black'] == 1 && $vo['apply_status'] == 3) {
                continue;
            }
        }
        if (0 < intval($v['target_user']) && intval($v['target_user']) != $borrow_user_info['user_type']) {
            continue;
        }
        if ($v['borrow_credit_status'] == 1 && !($borrow_user_info['credits'] <= $v['borrow_credit_last'] && $v['borrow_credit_first'] <= $borrow_user_info['credits'])) {
            continue;
        }
        if (0 < intval($v['repayment_type']) && $v['repayment_type'] != $binfo['repayment_type']) {
            continue;
        }
        if ($v['timelimit_status'] == 1 && !($binfo['borrow_duration'] <= $v['timelimit_month_last'] && $v['timelimit_month_first'] <= $binfo['borrow_duration'])) {
            continue;
        }
        if ($v['apr_status'] == 1 && !($binfo['borrow_interest_rate'] <= $v['apr_last'] && $v['apr_first'] <= $binfo['borrow_interest_rate'])) {
            continue;
        }
        if (0 < intval($v['borrow_type']) && $v['borrow_type'] != $binfo['borrow_type']) {
            continue;
        }
        if ($v['tender_type'] == 1) {
            $investMoney = $v['tender_account'];
        } else if ($v['tender_type'] == 2) {
            $investMoney = $v['tender_scale'] * $binfo['borrow_money'] / 100;
        }
        if ($investMoney < $binfo['borrow_min']) {
            continue;
        }
        if ($binfo['borrow_max'] < $investMoney && 0 < $binfo['borrow_max']) {
            $investMoney = $binfo['borrow_max'];
        }
        $x = investmoney($v['uid'], $borrow_id, $investMoney, 1);
        if ($x === true) {
            $have_auto_do[] = $v['uid'];
            mtip("chk27", $v['uid'], $borrow_id);
        }
    }
}

function getBorrowInterest($type, $money, $duration, $rate)
{
    switch ($type) {
        case 1 :
            $month_rate = $rate / 100;
            $interest = getfloatvalue($money * $month_rate * $duration, 2);
            break;
        case 2 :
            $parm['duration'] = $duration;
            $parm['money'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalmonth($parm);
            $interest = $intre['repayment_money'] - $money;
            break;
        case 3 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalseason($parm);
            $interest = $intre['interest'];
            break;
        case 4 :
            $parm['month_times'] = $duration;
            $parm['account'] = $money;
            $parm['year_apr'] = $rate;
            $parm['type'] = "all";
            $intre = equalendmonth($parm);
            $interest = $intre['interest'];
            break;
    }
    return $interest;
}

function EqualMonth($data = array())
{
    if (isset($data['money']) && 0 < $data['money']) {
        $account = $data['money'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['duration']) && 0 < $data['duration']) {
        $duration = $data['duration'];
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_li = pow(1 + $month_apr, $duration);
    $repayment = round($account * ($month_apr * $_li) / ($_li - 1), 2);
    $_result = array();
    if (isset($data['type']) && $data['type'] == "all") {
        $_result['repayment_money'] = $repayment * $duration;
        $_result['monthly_repayment'] = $repayment;
        $_result['month_apr'] = round($month_apr * 100, 2);
    } else {
        $i = 0;
        for (; $i < $duration; ++$i) {
            if ($i == 0) {
                $interest = round($account * $month_apr, 2);
            } else {
                $_lu = pow(1 + $month_apr, $i);
                $interest = round(($account * $month_apr - $repayment) * $_lu + $repayment, 2);
            }
            $_result[$i]['repayment_money'] = getfloatvalue($repayment, 2);
            $_result[$i]['repayment_time'] = get_times(array(
                "time" => $borrow_time,
                "num" => $i + 1
            ));
            $_result[$i]['interest'] = getfloatvalue($interest, 2);
            $_result[$i]['capital'] = getfloatvalue($repayment - $interest, 2);
        }
    }
    return $_result;
}

function EqualSeason($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if ($month_times % 3 != 0) {
        return false;
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_season = $month_times / 3;
    $_season_money = round($account / $_season, 2);
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $repay = $account - $_yes_account;
        $interest = round($repay * $month_apr, 2);
        $repayment_account += $interest;
        $capital = 0;
        if ($i % 3 == 2) {
            $capital = $_season_money;
            $_yes_account += $capital;
            $repay = $account - $_yes_account;
            $repayment_account += $capital;
        }
        $_result[$i]['repayment_money'] = getfloatvalue($interest + $capital, 2);
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = getfloatvalue($interest, 2);
        $_result[$i]['capital'] = getfloatvalue($capital, 2);
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_money'] = $repayment_account;
        $_resul['monthly_repayment'] = round($repayment_account / $_season, 2);
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}

function EqualEndMonth($data = array())
{
    if (isset($data['month_times']) && 0 < $data['month_times']) {
        $month_times = $data['month_times'];
    }
    if (isset($data['account']) && 0 < $data['account']) {
        $account = $data['account'];
    } else {
        return "";
    }
    if (isset($data['year_apr']) && 0 < $data['year_apr']) {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }
    if (isset($data['borrow_time']) && 0 < $data['borrow_time']) {
        $borrow_time = $data['borrow_time'];
    } else {
        $borrow_time = time();
    }
    $month_apr = $year_apr / 1200;
    $_yes_account = 0;
    $repayment_account = 0;
    $_all_interest = 0;
    $interest = round($account * $month_apr, 2);
    $i = 0;
    for (; $i < $month_times; ++$i) {
        $capital = 0;
        if ($i + 1 == $month_times) {
            $capital = $account;
        }
        $_result[$i]['repayment_account'] = $interest + $capital;
        $_result[$i]['repayment_time'] = get_times(array(
            "time" => $borrow_time,
            "num" => $i + 1
        ));
        $_result[$i]['interest'] = $interest;
        $_result[$i]['capital'] = $capital;
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") {
        $_resul['repayment_account'] = $account + $interest * $month_times;
        $_resul['monthly_repayment'] = $interest;
        $_resul['month_apr'] = round($month_apr * 100, 2);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } else {
        return $_result;
    }
}

function getMinfo($uid, $field = "m.pin_pass,mm.account_money")
{
    $pre = c("DB_PREFIX");
    $vm = m("members m")->field($field)->join("{$pre}member_money mm ON mm.uid=m.id")->where("m.id={$uid}")->find();
    return $vm;
}

function getMemberInfoDone($uid)
{
    $pre = c("DB_PREFIX");
    $field = "m.id,m.id as uid,m.user_name,mbank.uid as mbank_id,mi.uid as mi_id,mhi.uid as mhi_id,mci.uid as mci_id,mdpi.uid as mdpi_id,mei.uid as mei_id,mfi.uid as mfi_id,s.phone_status,s.id_status,s.email_status,s.safequestion_status";
    $row = m("members m")->field($field)->join("{$pre}member_banks mbank ON m.id=mbank.uid")->join("{$pre}member_contact_info mci ON m.id=mci.uid")->join("{$pre}member_department_info mdpi ON m.id=mdpi.uid")->join("{$pre}member_house_info mhi ON m.id=mhi.uid")->join("{$pre}member_ensure_info mei ON m.id=mei.uid")->join("{$pre}member_info mi ON m.id=mi.uid")->join("{$pre}member_financial_info mfi ON m.id=mfi.uid")->join("{$pre}members_status s ON m.id=s.uid")->where("m.id={$uid}")->find();
    $is_data = m("member_data_info")->where("uid={$row['uid']}")->count("id");
    $i == 0;
    if (0 < $row['mbank_id']) {
        ++$i;
        $row['mbank'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mbank'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mci_id']) {
        ++$i;
        $row['mci'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mci'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $is_data) {
        $row['mdi_id'] = $is_data;
        $row['mdi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mdi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mhi_id']) {
        ++$i;
        $row['mhi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mhi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mdpi_id']) {
        ++$i;
        $row['mdpi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mdpi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mei_id']) {
        ++$i;
        $row['mei'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mei'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mfi_id']) {
        ++$i;
        $row['mfi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mfi'] = "<span style='color:black'>未填写</span>";
    }
    if (0 < $row['mi_id']) {
        ++$i;
        $row['mi'] = "<span style='color:green'>已填写</span>";
    } else {
        $row['mi'] = "<span style='color:black'>未填写</span>";
    }
    $row['i'] = $i;
    return $row;
}

function getVerify($uid)
{
    $pre = c("DB_PREFIX");
    $vo = m("members m")->field("m.id,m.user_leve,m.time_limit,s.id_status,s.phone_status,s.email_status,s.video_status,s.face_status")->join("{$pre}members_status s ON s.uid=m.id")->where("m.id={$uid}")->find();
    $str = "";
    if ($vo['id_status'] == 1) {
        $str .= "<a href=\"" . __APP__ . "/member/verify\"><img alt=\"实名认证通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/id.gif\"/></a>";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/verify\"><img alt=\"实名认证未通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/id_0.gif\"/></a>";
    }
    if ($vo['phone_status'] == 1) {
        $str .= "<img alt=\"手机认证通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/phone.gif\"/>";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/verify\"><img alt=\"手机认证未通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/phone_0.gif\"/></a>";
    }
    if ($vo['email_status'] == 1) {
        $str .= "<a href=\"" . __APP__ . "/member/verify\"><img alt=\"邮件认证通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/email.gif\"/></a>";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/verify\"><img alt=\"邮件认证未通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/email_0.gif\"/></a>";
    }
    if ($vo['user_leve'] != 0 && time() < $vo['time_limit']) {
        $str .= "<a href=\"" . __APP__ . "/member/vip\"><img alt=\"VIP会员\" src=\"" . __ROOT__ . "/Style/H/images/icon/vip.gif\"/></a>";
    } else {
        $str .= "<a href=\"" . __APP__ . "/member/vip\"><img alt=\"不是VIP会员\" src=\"" . __ROOT__ . "/Style/H/images/icon/vip_0.gif\"/></a>";
    }
    if ($vo['video_status'] == 1) {
        $str .= "<a href=\"javascript:;\" onclick=\"alert('已通过视频认证');\"><img alt=\"视频认证通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/video.gif\"/></a>";
    } else {
        $str .= "<a href=\"javascript:;\" onclick=\"videoverify();\"><img alt=\"未进行视频认证\" src=\"" . __ROOT__ . "/Style/H/images/icon/video_0.gif\"/></a>";
    }
    if ($vo['face_status'] == 1) {
        $str .= "<a href=\"javascript:;\" onclick=\"alert('已通过现场认证');\"><img alt=\"现场认证通过\" src=\"" . __ROOT__ . "/Style/H/images/icon/place.gif\"/></a>";
    } else {
        $str .= "<a href=\"javascript:;\" onclick=\"faceverify();\"><img alt=\"未进行现场认证\" src=\"" . __ROOT__ . "/Style/H/images/icon/place_0.gif\"/></a>";
    }
    return $str;
}

function get_times($data = array())
{
    if (isset($data['time']) && $data['time'] != "") {
        $time = $data['time'];
    } else if (isset($data['date']) && $data['date'] != "") {
        $time = strtotime($data['date']);
    } else {
        $time = time();
    }
    if (isset($data['type']) && $data['type'] != "") {
        $type = $data['type'];
    } else {
        $type = "month";
    }
    if (isset($data['num']) && $data['num'] != "") {
        $num = $data['num'];
    } else {
        $num = 1;
    }
    if ($type == "month") {
        $month = date("m", $time);
        $year = date("Y", $time);
        $_result = strtotime("{$num} month", $time);
        $_month = ( integer )date("m", $_result);
        if (12 < $month + $num) {
            $_num = $month + $num - 12;
            $year += 1;
        } else {
            $_num = $month + $num;
        }
        if ($_num != $_month) {
            $_result = strtotime("-1 day", strtotime("{$year}-{$_month}-01"));
        }
    } else {
        $_result = strtotime("{$num} {$type}", $time);
    }
    if (isset($data['format']) && $data['format'] != "") {
        return date($data['format'], $_result);
    } else {
        return $_result;
    }
}

function getMemberBorrowScan($uid)
{
    $field = "borrow_status,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money";
    $borrowNum = m("borrow_info")->field($field)->where("borrow_uid = {$uid}")->group("borrow_status")->select();
    foreach ($borrowNum as $v) {
        $borrowCount[$v['borrow_status']] = $v;
    }
    $field = "status,sort_order,borrow_id,sum(capital) as capital,sum(interest) as interest";
    $repaymentNum = m("investor_detail")->field($field)->where("borrow_uid = {$uid}")->group("sort_order,borrow_id")->select();
    foreach ($repaymentNum as $v) {
        $repaymentStatus[$v['status']]['capital'] += $v['capital'];
        $repaymentStatus[$v['status']]['interest'] += $v['interest'];
        ++$repaymentStatus[$v['status']]['num'];
    }
    $field = "status,count(id) as num,sum(investor_capital) as investor_capital,sum(reward_money) as reward_money,sum(investor_interest) as investor_interest,sum(receive_capital) as receive_capital,sum(receive_interest) as receive_interest";
    $investNum = m("borrow_investor")->field($field)->where("investor_uid = {$uid}")->group("status")->select();
    $_reward_money = 0;
    foreach ($investNum as $v) {
        $investStatus[$v['status']] = $v;
        $_reward_money += floatval($v['reward_money']);
    }
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    $expiredNum = m("investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and borrow_uid={$uid}")->group("borrow_id,sort_order")->select();
    $_expired_money = 0;
    foreach ($expiredNum as $v) {
        $expiredStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_money += floatval($v['capital']);
    }
    $rowtj['expiredMoney'] = getfloatvalue($_expired_money, 2);
    $rowtj['expiredNum'] = count($expiredNum);
    $field = "borrow_id,sort_order,sum(`capital`) as capital,count(id) as num";
    $expiredInvestNum = m("investor_detail")->field($field)->where("`repayment_time`=0 and `deadline`<" . time() . " and investor_uid={$uid} AND status <> 0")->group("borrow_id,sort_order")->select();
    $_expired_invest_money = 0;
    foreach ($expiredInvestNum as $v) {
        $expiredInvestStatus[$v['borrow_id']][$v['sort_order']] = $v;
        $_expired_invest_money += floatval($v['capital']);
    }
    $rowtj['expiredInvestMoney'] = getfloatvalue($_expired_invest_money, 2);
    $rowtj['expiredInvestNum'] = count($expiredInvestNum);
    $rowtj['jkze'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['yhze'] = getfloatvalue(floatval($borrowCount[6]['repayment_money'] + $borrowCount[7]['repayment_money'] + $borrowCount[8]['repayment_money'] + $borrowCount[9]['repayment_money']), 2);
    $rowtj['dhze'] = getfloatvalue($rowtj['jkze'] - $rowtj['yhze'], 2);
    $rowtj['jcze'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['ysze'] = getfloatvalue(floatval($investStatus[4]['receive_capital'] + $investStatus[5]['receive_capital'] + $investStatus[6]['receive_capital']), 2);
    $rowtj['dsze'] = getfloatvalue($rowtj['jcze'] - $rowtj['ysze'], 2);
    $rowtj['fz'] = getfloatvalue($rowtj['jcze'] - $rowtj['jkze'], 2);
    $rowtj['dqrtb'] = getfloatvalue($investStatus[1]['investor_capital'], 2);
    $rowtj['earnInterest'] = getfloatvalue(floatval($investStatus[5]['receive_interest'] + $investStatus[6]['receive_interest']), 2);
    $rowtj['payInterest'] = getfloatvalue(floatval($repaymentStatus[1]['interest'] + $repaymentStatus[2]['interest'] + $repaymentStatus[3]['interest'] + $repaymentStatus[3]['interest']), 2);
    $rowtj['willgetInterest'] = getfloatvalue(floatval($investStatus[4]['investor_interest'] - $investStatus[4]['receive_interest']), 2);
    $rowtj['willpayInterest'] = getfloatvalue(floatval($repaymentStatus[7]['interest']), 2);
    $rowtj['borrowOut'] = getfloatvalue(floatval($investStatus[4]['investor_capital'] + $investStatus[5]['investor_capital'] + $investStatus[6]['investor_capital']), 2);
    $rowtj['borrowIn'] = getfloatvalue(floatval($borrowCount[6]['money'] + $borrowCount[7]['money'] + $borrowCount[8]['money'] + $borrowCount[9]['money']), 2);
    $rowtj['jkcgcs'] = $borrowCount[6]['num'] + $borrowCount[7]['num'] + $borrowCount[8]['num'] + $borrowCount[9]['num'];
    $rowtj['tbjl'] = $_reward_money;
    $row = array();
    $row['borrow'] = $borrowCount;
    $row['repayment'] = $repaymentStatus;
    $row['invest'] = $investStatus;
    $row['tj'] = $rowtj;
    return $row;
}

function getUserWC($uid)
{
    $row = array();
    $field = "count(id) as num,sum(withdraw_money) as money";
    $row['W'] = m("member_withdraw")->field($field)->where("uid={$uid} AND withdraw_status=2")->find();
    $field = "count(id) as num,sum(money) as money";
    $row['C'] = m("member_payonline")->field($field)->where("uid={$uid} AND status=1")->find();
    return $row;
}

function getExpiredDays($deadline)
{
    if ($deadline < 1000) {
        return "数据有误";
    }
    return ceil((time() - $deadline) / 3600 / 24);
}

function getExpiredMoney($expired, $capital, $interest)
{
    $glodata = get_global_setting();
    $expired_fee = explode("|", $glodata['fee_expired']);
    if ($expired <= $expired_fee[0]) {
        return 0;
    }
    return getfloatvalue(($capital + $interest) * $expired * $expired_fee[1] / 1000, 2);
}

function getExpiredCallFee($expired, $capital, $interest)
{
    $glodata = get_global_setting();
    $call_fee = explode("|", $glodata['fee_call']);
    if ($expired <= $call_fee[0]) {
        return 0;
    }
    return getfloatvalue(($capital + $interest) * $expired * $call_fee[1] / 1000, 2);
}

function getNet($minfo, $capitalinfo)
{
    return getfloatvalue($minfo['account_money'] + $minfo['money_freeze'] + $minfo['money_collect'] - intval($capitalinfo['borrow'][6]['money'] - $capitalinfo['borrow'][6]['repayment_money']), 2);
}

function setBackUrl($per = "", $suf = "")
{
    $url = $_SERVER['HTTP_REFERER'];
    $urlArr = parse_url($url);
    $query = $per . "?1=1&" . $urlArr['query'] . $suf;
    session("listaction", $query);
}
 
?>
