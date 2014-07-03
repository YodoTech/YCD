<?php
// 本类由系统自动生成，仅供测试用途
class WelcomeAction extends ACommonAction {

	var $justlogin = true;
	
    public function index(){
		$row['borrow_1'] = M('borrow_info')->where('borrow_status=0')->count('id');//初审
		$row['borrow_2'] = M('borrow_info')->where('borrow_status=4')->count('id');//复审
		$row['limit_a'] = M('member_apply')->where('apply_status=0')->count('id');//额度
		$row['data_up'] = M('member_data_info')->where('status=0')->count('id');//上传资料
		$row['vip_a'] = M('vip_apply')->where('status=0')->count('id');//VIP审核
		$row['video_a'] = M('video_apply')->where('apply_status=0')->count('id');//视频认证		
		$row['face_a'] = M('face_apply')->where('apply_status=0')->count('id');//现场认证		
		$row['real_a'] = M('members_status')->where('id_status=3')->count('uid');//现场认证		
		$this->assign("row",$row);
		
		
		
		
		$this->display();
    }
	
}