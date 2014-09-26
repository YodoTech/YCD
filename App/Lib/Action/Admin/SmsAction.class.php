<?php
// 全局设置
class SmsAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
    	//...
    }

    public function log()
    {
    	$field= 'id,uid,is_admin,to,content,result,add_ip,add_time';
		$this->_list(D('sms_log'),$field,'','add_time','DESC');
    	$this->display();
    }

    public function _listFilter($list)
    {
        $row = array();
        foreach($list as $key=>$v) {
            if ($v['is_admin']) {
                $v['uname']  = get_admin_name($v['uid']);
                $v['utype'] = '管理员';
            } else {
                $v['uname'] = m("members")->getFieldById($v['uid'], "user_name");
                $v['utype'] = '用户';
            }
            $arr = explode("/", $v['result']);
            $v['state'] = $arr[0] == '000'? '发送成功' : '发送失败';
            $row[$key] = $v;
        }
        return $row;
    }
	
}
?>