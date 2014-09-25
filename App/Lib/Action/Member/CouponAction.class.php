<?php
// 本类由系统自动生成，仅供测试用途
class CouponAction extends MCommonAction {

    public function index() {
		$this->display();
    }

    //条件判断
	private function _ajax_filter() {
		//状态判断
		if (!$this->uid) {//登录状态判断
			$data['message'] = '请先<a class="red" href="'.__APP__.'/member/common/login?redirect_uri='.__APP__.'/member/coupon#fragment-1">登录</a>!';
			ajaxmsg($data, 0);
		}

		$coupon_code = text($_POST['coupon_code']);
		$vo = m('coupon')->where('coupon_code = \''.$coupon_code.'\'')->find();
		if (empty($vo)) {
			$data['message'] = '优惠券不存在!';
			ajaxmsg($data, 0);
		} elseif($vo['is_used']) {
			$data['message'] = '优惠券已使用，时间为：<span class="red">'.date('Y-m-d H:i:s', $vo['used_time']).'</span>';
			ajaxmsg($data, 0);
		} elseif(intval($vo['over_time']) > 0 && intval($vo['over_time']) <= time()) {
			$data['message'] = '优惠券已过期!';
			ajaxmsg($data, 0);
		} elseif($vo['is_lock']) {
			$data['message'] = '优惠券被锁定，暂时无法使用!';
			ajaxmsg($data, 0);
		}

		return array('vo'=>$vo);
	}

    //使用面板
	public function ajax_convert() {
		$vv = $this->_ajax_filter();

		/****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey = md5(MODULE_NAME.'-'.time());
		cookie(strtolower(MODULE_NAME).'-convert', $cookieKey, $cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/
		
		$this->assign('vo', $vv['vo']);
		$data['content'] = $this->fetch();
		ajaxmsg($data, 1);
	}

	//使用操作
	public function convert() {
		$vv = $this->_ajax_filter();
		//密码判断
		if ($_POST['coupon_pass'] != $vv['vo']['coupon_pass']) {
			$data['message'] = '密码错误，请重试!';
			ajaxmsg($data, 0);
		}

		/****** 防止模拟表单提交 *********/
		$cookieKeyS = cookie(strtolower(MODULE_NAME).'-convert');
		if($cookieKeyS != $_POST['cookieKey']) {
			$data['message'] = '非正常提交!';
			ajaxmsg($data, 0);
		}
		/****** 防止模拟表单提交 *********/
		
		$couponLog = d('coupon_log');
    	$couponLog->startTrans();

    	$coupon_log_id = m('coupon_log')->add(array(
    		'uid' 			=> $this->uid,
    		'coupon_id' 	=> $vv['vo']['id'],
    		'info' 			=> '',
    		'add_ip' 		=> get_client_ip(),
    		'add_time' 		=> time()
    	));//添加日志
    	$upcoupon_info = m('coupon')->where('id = '.$vv['vo']['id'])->save(array(
    		'is_used' 	=> 1,
    		'used_time' => time()
    	));
    	if ($coupon_log_id && $upcoupon_info) {
    		$couponLog->commit();
    		$data['message'] = '使用成功!';
    		$state = 1;
    	} else {
    		$couponLog->rollback();
    		$data['message'] = '使用失败，请重试';
    		$state = 0;
    	}
    	ajaxmsg($data, $state);
	}
}