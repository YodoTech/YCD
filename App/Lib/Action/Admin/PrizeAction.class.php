<?php
// 全局设置
class PrizeAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
    	$field= 'id,prize_name,prize_img,need_credits,cumulate_num,is_used';
		$this->_list(D('prize'),$field,'','add_time','DESC');
    	$this->display();
    }

    public function log()
    {
    	$field= 'id,uid,prize_id,current_num,current_cost,cumulate_cost,info,is_send,add_ip,add_time';
		$this->_list(D('prize_log'),$field,'','add_time','DESC');
    	$this->display();
    }

    public function _listFilter($list) {
    	$row = array();
		foreach($list as $k => $v) {
			$v['uname']  = m('members')->getFieldById($v['uid'], 'user_name');
			$row[$k] = $v;
		}
		return $row;
	}

    private function _doFilter($m)
    {
    	$row = array();
		foreach ($GLOBALS['_POST']['swfimglist'] as $key => $v) {
			$row[$key]['img'] = substr( $v, 1 );
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$m->prize_img = serialize($row);
		return $m;
    }

	public function _doAddFilter($m)
	{
		$m = $this->_doFilter($m);
		$m->add_time = time();
		return $m;
	}

	public function _doEditFilter($m)
	{
		$m = $this->_doFilter($m);
		return $m;
	}

	public function _doDelFilter($id)
	{
		$_list = m("prize")->field("prize_img")->where("id in ({$id})")->select();
		$list = array();
		foreach ($_list as $key => $v) {
			$v['prize_img'] = array_shift(unserialize($v['prize_img']));
			$list[$key] = $v['prize_img']['img'];
		}
		if (!empty($list)) {
			$prize_imglist = serialize($list);
			session('prize_imglist', $prize_imglist);
		}
	}

	public function _AfterDoDel()
	{
		if (!empty($_SESSION['prize_imglist'])) {
			$prize_imglist = unserialize($_SESSION['prize_imglist']);
			foreach ($prize_imglist as $v) {
				unlink(C('WEB_ROOT').$v);
				unlink(C("WEB_ROOT").get_thumb_pic($v));
			}
			session('prize_imglist', null);
		}
	}

	public function swfUpload( )
	{
		if ( $_POST['picpath'] )
		{
			$imgpath = substr( $_POST['picpath'], 1 );
			if ( in_array( $imgpath, $_SESSION['imgfiles'] ) )
			{
				unlink( C( "WEB_ROOT" ).$imgpath );
				$thumb = get_thumb_pic( $imgpath );
				$res = unlink(C("WEB_ROOT").$thumb );
				if ( $res )
				{
					$this->success("删除成功<script>var stats = swfu.getStats();stats.successful_uploads--;swfu.setStats(stats);</script>", "", $_POST['oid'] );
				}
				else
				{
					$this->error( "删除失败", "", $_POST['oid'] );
				}
			}
			else
			{
				$this->error( "图片不存在", "", $_POST['oid'] );
			}
		}
		else
		{
			$this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Prize/";
			$this->thumbMaxWidth = "100";
			$this->thumbMaxHeight = "100";
			$this->saveRule = date( "YmdHis", time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if ( !isset( $_SESSION['count_file'] ) )
			{
				$_SESSION['count_file'] = 1;
			}
			else
			{
				++$_SESSION['count_file'];
			}
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];
		}
	}

	public function express() {
        $id = intval($_REQUEST['id']);
 		
		setBackUrl();

        $this->assign('vo', D('prize_log')->find($id));
        $this->display();
	}

	public function doExpress() {
		$arr = array(
        	'is_send' => intval($_POST['is_send']),
        	'info' => text($_POST['info'])
        );
        if (m('prize_log')->where('id='.intval($_POST['id']))->save($arr)) {
            //成功提示
            $this->assign('jumpUrl', __URL__."/".session('listaction'));
            $this->success(L('操作成功'));
        } else {
            //失败提示
            $this->error(L('操作失败'));
        }
	}
}
?>