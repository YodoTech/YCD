<?php
// 本类由系统自动生成，仅供测试用途
class PrizeAction extends HCommonAction {
	public function index() {
		$list = m("prize")->field('id,prize_name,prize_img,need_credits,cumulate_num')->where('is_used = 1')->select();
		foreach ($list as $k => $v) {
			$list[$k]['prize_img'] = array_shift(unserialize($v['prize_img']));
		}
		$this->assign('list', $list);

		$this->display();
	}

	//条件判断
	private function _ajax_filter()
	{
		//状态判断
		if (!$this->uid) {//登录状态判断
			$data['message'] = '请先<a class="red" href="'.__APP__.'/member/common/login?redirect_uri='.__APP__.'/prize/">登录</a>!';
			ajaxmsg($data, 0);
		}

		$id = intval($_POST['id']);
		$vo = m('prize')->field('id,prize_name,prize_img,need_credits')->where('is_used = 1 and id = '.$id)->find();
		if (empty($vo)) {
			$data['message'] = '奖品不存在!';
			ajaxmsg($data, 0);
		}
		$vo['prize_img'] = array_shift(unserialize($vo['prize_img']));

		$vm = getMinfo($this->uid,true);
		//获取总消费积分
		$vl = m('prize_log')->field('cumulate_cost')->where('uid = '.$this->uid)->order('add_time desc')->find();
		$vl['cumulate_cost'] = intval($vl['cumulate_cost']);
		if ($vm['credits'] - $vl['cumulate_cost'] < $vo['need_credits']) {//积分状态判断:是否足够本次兑换
			$data['message'] = '您的积分不足，无法完成本次兑换!';
			ajaxmsg($data, 0);
		}
		return array('vo'=>$vo, 'vm'=>$vm, 'vl'=>$vl);
	}

	//兑换面板
	public function ajax_convert() {
		$vv = $this->_ajax_filter();

		/****** 防止模拟表单提交 *********/
		$cookieTime = 15*3600;
		$cookieKey = md5(MODULE_NAME.'-'.time());
		cookie(strtolower(MODULE_NAME).'-convert', $cookieKey, $cookieTime);
		$this->assign("cookieKey",$cookieKey);
		/****** 防止模拟表单提交 *********/
		
		$this->assign('vo', $vv['vo']);
		$this->assign('vm', $vv['vm']);
		$this->assign('vl', $vv['vl']);
		$data['content'] = $this->fetch();
		ajaxmsg($data, 1);
	}

	//兑换操作
	public function convert() {
		$vv = $this->_ajax_filter();

		/****** 防止模拟表单提交 *********/
		$cookieKeyS = cookie(strtolower(MODULE_NAME).'-convert');
		if($cookieKeyS != $_POST['cookieKey']) {
			$data['message'] = '非正常提交!';
			ajaxmsg($data, 0);
		}
		/****** 防止模拟表单提交 *********/
		
		$prizeLog = d('prize_log');
    	$prizeLog->startTrans();

    	$prize_log_id = m('prize_log')->add(array(
    		'uid' 			=> $this->uid,
    		'prize_id' 		=> $vv['vo']['id'],
    		'current_num' 	=> '1',
    		'current_cost' 	=> $vv['vo']['need_credits'],
    		'cumulate_cost' => $vv['vl']['cumulate_cost'] + $vv['vo']['need_credits'],
    		'info' 			=> '',
    		'add_ip' 		=> get_client_ip(),
    		'add_time' 		=> time()
    	));//添加日志
    	$upprize_info = m('prize')->where('id = '.$vv['vo']['id'])->setInc('cumulate_num');//数量加1
    	if ($prize_log_id && $upprize_info) {
    		$prizeLog->commit();
    		$data['message'] = '兑换成功!';
    		$state = 1;
    	} else {
    		$prizeLog->rollback();
    		$data['message'] = '兑换失败，请重试';
    		$state = 0;
    	}
    	ajaxmsg($data, $state);
	}
}