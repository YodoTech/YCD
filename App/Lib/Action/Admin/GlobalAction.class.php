<?php
// 全局设置
class GlobalAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function websetting()
    {
		$list = M('global')->order("order_sn DESC")->select();
		$this->assign('list', de_xie($list));
        $this->display();
    }
	//添加
    public function doAdd()
    {
		$glo = D('Global');

		if($glo->create()) {
			$newid = $glo->add();
			if($newid) $this->success('修改成功');
			else $this->error('修改失败');
		}else{
			$this->error($glo->getError());
		}

    }

	//添加
    public function doDelweb()
    {
		$data = $_POST;
		$sys = M('global')->getFieldById($data['id'],'is_sys');
		if($sys==1){
			$a_data['status'] = 0;
			$a_data['message'] = "系统参数，禁止删除";
			exit(json_encode($a_data));
		}
		$delnum = M('global')->where("id = '{$data['id']}'")->delete(); 
		
		if($delnum){			
			$a_data['status'] = 1;
			$a_data['id'] = $data['id'];
		}else{
			$a_data['status'] = 0;
			$a_data['message'] = "删除失败";
		}
		
		exit(json_encode($a_data));
    }
	//编辑
    public function doEdit()
    {
		$data = $_POST;
		foreach($data as $key => $v){
			if(is_numeric($key)) M('global')->where("id = '{$key}'")->setField('text',EnHtml($v));
		}

		$this->success('更新成功');
    }

	//添加
    public function friend()
    {
		$this->assign('friend_position', C('FRIEND_LINK'));
		
		import("ORG.Util.Page");
		
		$Friend = M('friend');
		$page_size = ($page_szie==0)?C('ADMIN_PAGE_SIZE'):$page_szie;
		
		if(empty($search)) $condition="1";
		else $condition = $search;
		
		
		$count  = $Friend->where($condition)->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$page_size); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = ($fields=="")?"*":$fields;
		$order =  ($order=="")?'link_order DESC':$order;
		
		$list = $Friend->field($fields)->where($condition)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

		$FriendList = $list;
		$Friend_p = C('FRIEND_LINK');
			  
		foreach($FriendList as $key => $v){
			foreach($v as $key_s => $v_s){
				if($key_s == 'link_type') $v_s = $Friend_p[$v_s];
				elseif($key_s == 'game_name' && empty($v_s)) $v_s = "无";
				else if($key_s == 'is_show'){
					if($v_s==1) $v_s="是";
					else $v_s="否";
				}
				$FriendList[$key][$key_s] = $v_s;
			}
		} 
		
		$FriendArr['FriendList'] = $FriendList;
		$FriendArr['PageBar'] = $show;



		$this->assign('friend_list', $FriendArr['FriendList']);
		$this->assign('pagebar', $FriendArr['PageBar']);
		$this->assign('position', "友情链接");
        $this->display();
    }

    public function addFriend()
    {
		
		$data = $_POST;
		foreach($data as $key => $v){
			if(!empty($key)) $data[$key]=EnHtml($v);
		}
		
		if(!empty($_FILES['imgfile']['name'])){
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$this->savePathNew = C('ADMIN_UPLOAD_DIR').'Friends/'; 
			$info = $this->CUpload();
			$data['link_img'] = $info[0]['savepath'].$info[0]['savename'];
		}
		if(!isset($_POST['fid'])){//新增
			$data['game_id'] = 0;
			$newid = M('friend')->add($data);
			if(!$newid>0){
				$this->error('添加失败，请确认填入数据正确');
				exit;
			}
				
			$this->assign('jumpUrl', U('/admin/global/friend/'));
			$this->success('友情链接添加成功');
		}else{//编辑
		
			$data['id']=intval($_POST['fid']);
			$newid = M('friend')->save($data);
			if(!$newid>0){
				$this->error('编辑失败，请确认填入数据正确');
				exit;
			}
	
			$this->assign('jumpUrl', U('/admin/global/friend/'));
			$this->success('友情链接编辑成功');
		}
    }

	//添加
    public function doDeleteFriend()
    {
		$data = $_POST;
		
		foreach($data as $key => $v){
			$data[$key] = EnHtml($v);
		}
		
		$idarray = $data['idarr'];
		
		$delnum = M('friend')->where("id in ({$idarray})")->delete(); 
		
		if($delnum){
			$a_data['success'] = $rid;
			$a_data['success_msg'] = "友情链接删除成功";
			$a_data['aid'] = $idarray;
		}else{
			$a_data['success'] = 0;
			$a_data['error_msg'] = "友情链接删除失败";
		}
		
		exit(json_encode($a_data));
    }

    public function searchFriend()
    {
		$this->assign('friend_position', C('FRIEND_LINK'));
		//搜索

		import("ORG.Util.Page");
		if($_POST){
			$data=$_POST;
		
			$searchKey = array('link_txt','link_href','link_type','is_show','game_id');
			foreach($data as $key => $v){
				if(in_array($key,$searchKey)){
					if($key=='link_href' && !empty($v)) $condition['link_href']=array('exp',' <> "" AND instr(link_href,"'.$v.'")>0');
					elseif(!empty($v)) $condition[$key]=array('eq',EnHtml($v));
				}
			
			}
		}
		
		$Friend = M('friend');
		$page_size = ($page_szie==0)?C('ADMIN_PAGE_SIZE'):$page_szie;
		
		if(empty($condition)) $condition="1";
		else $condition = $condition;
		
		
		$count  = $Friend->where($condition)->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,$page_size); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = ($fields=="")?"*":$fields;
		$order =  ($order=="")?'link_order DESC':$order;
		
		$list = $Friend->field($fields)->where($condition)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();

		$FriendList = $list;
		$Friend_p = C('FRIEND_LINK');
			  
		foreach($FriendList as $key => $v){
			foreach($v as $key_s => $v_s){
				if($key_s == 'link_type') $v_s = $Friend_p[$v_s];
				elseif($key_s == 'game_name' && empty($v_s)) $v_s = "无";
				else if($key_s == 'is_show'){
					if($v_s==1) $v_s="是";
					else $v_s="否";
				}
				$FriendList[$key][$key_s] = $v_s;
			}
		} 
		
		$FriendArr['FriendList'] = $FriendList;
		$FriendArr['PageBar'] = $show;

		$this->assign('friend_list', $FriendArr['FriendList']);
		$this->assign('pagebar', $FriendArr['PageBar']);
		$this->assign('position', "友情链接");
        $this->display('friend');
    }


    public function cleanall()
    {
		$dirs	=	array(C('APP_ROOT').'Runtime');
		foreach($dirs as $value)
		{
			rmdirr($value);
		
			echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" 目录下缓存清除成功! </div> <br /><br />";
		
			@mkdir($value,0777,true);
		
		}
		
	}


    public function cleandata()
    {
		$dirs	=	array(C('APP_ROOT').'Runtime/Temp');
		foreach($dirs as $value)
		{
			rmdirr($value);
		
			echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" 目录下缓存清除成功! </div> <br /><br />";
		
			@mkdir($value,0777,true);
		
		}
		
	}


    public function cleantemplet()
    {
		if($_GET['acahe']==1){//前台
			$dirs	=	array(C('APP_ROOT').'Runtime/Cache/Home');
		}elseif($_GET['acahe']==2){//后台
			$dirs	=	array(C('APP_ROOT').'Runtime/Cache/Admin');
		}elseif($_GET['acahe']==3){//会员中心
			$dirs	=	array(C('APP_ROOT').'Runtime/Cache/Member');
		}else{
			exit("ERROR");
		}
		foreach($dirs as $value)
		{
			rmdirr($value);
		
			echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" 目录下缓存清除成功! </div> <br /><br />";
		
			@mkdir($value,0777,true);
		
		}
		
	}
}
?>