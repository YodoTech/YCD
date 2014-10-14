<?php
// 全局设置
class CouponAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function _MyInit() {
    	//配置面值
    	$coupon_val_list = array('50','100','200','500','800');
    	$this->assign('coupon_val_list', $coupon_val_list);
    }
    public function index() {
    	$field= 'id,coupon_code,coupon_val,is_used,used_time,is_lock,over_time,add_time';
		$this->_list(D('coupon'),$field,'','id','DESC');
    	$this->display();
    }

    public function log() {
    	$field= 'id,uid,coupon_id,info,add_ip,add_time';
		$this->_list(D('coupon_log'),$field,'','add_time','DESC');
    	$this->display();
    }

    /**
    * 创建新的优惠券
    * @param int $val 面值（根据面值生成对应的编号）
    * @param int $sn 序列号
    * @return string $result
    */
    private function _code($val, $sn) {
    	$result = '';
		//生成规则：
		//用三位数表示面值，比如050表示为50元，100表示为100元，以此类推；
		$s1 = sprintf('%1$03d', $val);
		//插入当前年月，比如1403；
		$s2 = date('ym');
		//序列号，比如00010；
		$s3 = sprintf('%1$05d', $sn);
		$result = $s1.$s2.$s3;
    	return $result;
    }
    //生成密码
    private function _pass() {
    	//生成规则：随机6位数
		import('ORG.Util.String');
		$result = String::randString(6, 1);
		return $result;
    }
    //获取序列号
    private function _sn() {
        $thisMonth = date('Y-m-01');
        $nextMonth = strtotime("$thisMonth +1 month");
        $thisMonth = strtotime(date('Y-m-01'));
        
		$sn = m('coupon')->where('add_time >= '.$thisMonth.' and add_time < '.$nextMonth)->order('id DESC')->getField('coupon_code');
		if (empty($sn)) {
			$sn = 1;
		} else {
			$sn = substr($sn, -5);
			$sn = intval($sn) + 1;
		}
		return $sn;
    }

    public function doAdd() {
		$coupon = d('coupon');
    	$coupon->startTrans();

    	$coupon_val = intval($_POST['coupon_val']);
    	$addnum = intval($_POST['addnum']);
    	$state = true;

    	$sn = $this->_sn();
    	for ($i=0; $i < $addnum; $i++) {
    		$coupon_id = m('coupon')->add(array(
	    		'coupon_code' 	=> $this->_code($coupon_val, $sn),
	    		'coupon_pass' 	=> $this->_pass(),
	    		'coupon_val' 	=> $coupon_val,
	    		'coupon_info' 	=> '',
	    		'add_time' 		=> time()
	    	));
	    	if (!$coupon_id) {
	    		$state = false;
	    		break;
	    	} else {
	    		$sn = $sn + 1;
	    	}
    	}
    	if ($state) {//保存成功
    		$coupon->commit();
    		//成功提示
    		$this->assign('jumpUrl', __URL__.'/'.session('listaction'));
            $this->success(L('新增成功'));
    	} else {
    		$coupon->rollback();
    		//失败提示
    		$this->error(L('新增失败'));
    	}
    }
	
	//添加数据
    public function doEdit() {
    	$arr = array();

    	$id = intval($_POST['id']);
    	$reset_pass = intval($_POST['reset_pass']);
    	if ($reset_pass == 1) {
    		$arr['coupon_pass'] = $this->_pass();
    	}
    	$arr['is_lock'] = intval($_POST['is_lock']);
    	$is_over_time = $_POST['is_over_time'];
    	if ($is_over_time == 1) {
    		$arr['over_time'] = strtotime($_POST['over_time']);
    	} else {
    		$arr['over_time'] = 0;
    	}

		if(m('coupon')->where('id = '.$id)->save($arr)) {
			$this->success(L('修改成功'));
		} else {
			$this->error(L('修改失败'));
		}
    }
}
?>