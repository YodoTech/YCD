<?php
// 本类由系统自动生成，仅供测试用途
class CommonAction extends HCommonAction {
    public function video(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$vs = M('members_status')->getFieldByUid($this->uid,'video_status');
		if($vs==1) ajaxmsg("您已通过视频认证，无需再次认证",0);
		$vxs = M('video_apply')->where("uid={$this->uid} AND apply_status=0")->count('id');
		if($vxs>=1) ajaxmsg("您已经提交申请，请等待客服人员处理",0);

		
		$newid = memberMoneyLog($this->uid,22,-($this->glo['fee_video']),$info="申请视频认证");
		
		if($newid){
			$save['uid'] = $this->uid;
			$save['add_time'] = time();
			$save['add_ip'] = get_client_ip();
			$save['apply_status'] = 0;
			$newidx = M('video_apply')->add($save);
			if($newidx) ajaxmsg("申请成功，请等待客服与您联系");
			else ajaxmsg("申请失败，如已被扣除费用，请联系客服");
		}
		else ajaxmsg("申请失败，请重试");
	}
    public function face(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$vs = M('members_status')->getFieldByUid($this->uid,'face_status');
		if($vs==1) ajaxmsg("您已通过现场认证，无需再次认证",0);
		$vxs = M('face_apply')->where("uid={$this->uid} AND apply_status=0")->count('id');
		if($vxs>=1) ajaxmsg("您已经提交申请，请等待客服人员处理",0);

		$newid = memberMoneyLog($this->uid,26,-($this->glo['fee_face']),$info="申请现场认证");
		
		if($newid){
			$save['uid'] = $this->uid;
			$save['add_time'] = time();
			$save['add_ip'] = get_client_ip();
			$save['apply_status'] = 0;
			$newidx = M('face_apply')->add($save);
			if($newidx) ajaxmsg("申请成功，请等待客服与您联系");
			else ajaxmsg("申请失败，请重试");
		}
		else ajaxmsg("申请失败，请重试");
	}
}