<?php
// 全局设置
class DbAction extends ACommonAction
{
	var $b_insertf = "replace ";//插入方式
	var $b_filesize = "500";//默认每个备份大小以k为单位
	var $waitbaktime = "0";//默认每组备份等待时间以秒为单位
	
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function truncate()
    {
		if($_POST){
			$ar = file(C('DB_BAKPATH')."/truncate.txt");
			foreach($ar as $v){
				if(!empty($v)) M()->query($v);
			}
			$this->success("成功清空数据表");
		}else{
			$this->display();	
		}
    }
    public function index()
    {
		$all_table_info = M()->query("SHOW TABLE STATUS");
		$this->assign("tablelist",$all_table_info);//数据库内所有表信息
        $this->display();
    }
	//删除备份
	public function delbak(){
		$db_dir = C("DB_BAKPATH");
		$dirname = explode(",",$_REQUEST['idarr']);
        foreach($dirname as $v){
			Rmall($db_dir."/".$v);
		}
		
		$this->success(L('删除成功'),'',$_REQUEST['idarr']);
	}
	//查看表结构
	public function showtable(){
		$table=str_replace(";","",$_REQUEST['tables']);
		//数据表结构
		//设置引号
		M()->query("SET SQL_QUOTE_SHOW_CREATE=1;");
		$r=M()->query("SHOW columns from `$table`;");
		$this->assign("tablecontent",$r);
        $this->display();
	}
	//数据备份打包
	public function dozip(){
		$zippath = C("WEB_ROOT").C("ZIP_PATH");
		$dbpath = C("DB_BAKPATH");
		MakeDir($zippath);
		//$_REQUEST['bakup']="useful";
		$bakup=$_REQUEST['bakup'];
		//zip文件名
		$zipname=date("YmdHis",time()).rand(1,100).$bakup.".zip";
		import('ORG.Util.Phpzip');
		$z=new PHPZip(); //新建立一个zip的类
		$res = $z->Zip($dbpath."/".$bakup,$zippath."/".$zipname); //添加指定目录
		if($res==1){
			$this->success($zipname,'',__APP__."/".C("ZIP_PATH")."/".$zipname);
		}else{
			$this->error("压缩失败");
		}
	}
	//下载备份
	public function downzip(){
		$down=$_REQUEST['url'];
		$zipname=$_REQUEST['zipname'];
		$this->assign("downurl",$down);
		$this->assign("zipname",$zipname);
        $this->display();
	}
	//删除备份
	public function delzip(){
		$zipname=$_REQUEST['zipname'];
		$zippath = C("WEB_ROOT").C("ZIP_PATH")."/";
		$zipfile=$zippath.$zipname;
		if(is_file($zipfile)) $res = unlink($zipfile);
		if($res==1){
			$this->success("zip备份删除成功");
		}else{
			$this->error("zip备份删除失败，或者文件不存在");
		}
		
	}
	//备份参数
    public function set()
    {
        $this->display();
    }
	//恢复数据
	public function restore()
	{
		Mheader("utf-8");
		$db_dir = C("DB_BAKPATH")."/".$_REQUEST['path'];
		if(!empty($db_dir)&&$od=opendir($db_dir))   //$d是目录名
		{
				while(($file=readdir($od))!==false)  //读取目录内文件
				{
					preg_match('|\.(\w+)$|i',$file, $ext);
					$extend = strtolower($ext[1]);//文件后缀
					if($file!="." && $file!=".." && !is_dir($db_dir."/".$file) && $extend=="php"){
						require($db_dir."/".$file);
						echo $file."已恢复<br />";
					}
				}
		}
		echo "<span style='color:red'>恭喜，顺利恢复完成</a>";
	}
	//已备份数据
    public function baklist()
    {
		$list=array();
		$db_dir = C("DB_BAKPATH");
		if(!empty($db_dir)&&$od=opendir($db_dir))   //$d是目录名
		{		
				while(($file=readdir($od))!==false)  //读取目录内文件
				{
					if($file!="." && $file!=".." && is_dir($db_dir."/".$file)){
						$row=array();
						$row['dirname'] = $file;
						//备份文件夹内部文件
						if($od2=opendir($db_dir."/".$file) ){
							while(($file2=readdir($od2))!==false)  //读取目录内文件
							{
								preg_match('|\.(\w+)$|i',$file2, $ext);
								$extend = strtolower($ext[1]);//文件后缀
								
								if($file2!="." && $file2!=".." && !is_dir($db_dir."/$file/".$file2)){
									if($extend=="txt"){
										$row['baktime'] = date("Y-m-d H:i:s",filemtime("$db_dir/$file/$file2"));
										$row['bakdetail'] = ReadFiletext("$db_dir/$file/$file2");
									}
									$row['baksize'] = $row['baksize'] + filesize("$db_dir/$file/$file2");
								}
								
							}
						}
						//备份文件夹内部文件
						$list[]=$row;
					}
				}//while
		}

		$this->assign("baklist",$list);
        $this->display();
    }
	//执行备份(按文件大小)
	//$savepath 文件保存路径 
	//fnum当前表的字段数
	public function backup(){
		$t=$_GET['t'];
		$s=$_GET['e'];
		$p=$_GET['p'];
		$savepath=$_REQUEST['savepath']?$_REQUEST['savepath']:date("YmdHis",time());
		$alltotal=$_GET['alltotal'];
		$thenof=$_GET['thenof'];
		$fnum=$_GET['fnum'];
		$stime=$_GET['stime'];
		$bakpath = C("DB_BAKPATH");
		$b_table=$_SESSION['b_table'];//要备份的表checkbox$_POST['checkbox'];
		
		
		if($_REQUEST['baktable']){
			$_SESSION['b_table'] = $_REQUEST['baktable'];//备份信息
			$b_table=$_REQUEST['baktable'];//要备份的表checkbox$_POST['checkbox'];
		}
		
		if($_REQUEST['info']) $_SESSION['bak_info'] = $_REQUEST['info'];//备份信息
		
		Mheader("utf-8");
		if(empty($savepath))//备份文件保存目录
		{
			$this->error("必须指定备份数据库保存目录");
		}
		$path=$bakpath."/".$savepath;

		if(empty($b_table))
		{
			$this->error("要备份的数据表不能为空");
		}
		

		$waitbaktime=$this->waitbaktime;
		if(empty($stime))//开始时间
		{
			$stime=time();
		}
		
		$btb=explode(",",$b_table);
		$count=count($btb);
		$t=(int)$t;
		$s=(int)$s;
		$p=(int)$p;
		//备份完毕
		$btb[$t] = str_replace(";","",$btb[$t]);
		if($t>=$count)
		{
			MakeFile($_SESSION['bak_info'],$path."/info.txt");//保存备注信息
			unset($_SESSION['b_table'],$_SESSION['bak_info']);
			echo"<script>alert('备份完成\\n\\n共用时".UseTime($stime)."');self.location.href='".__URL__."';</script>";
			exit();
		}
		$cpright=Cpright();//版权信息
		//编码
		$b_dbchar="utf-8";//默认使用utf-8
		if($b_dbchar=='auto')
		{
			if(empty($s))
			{
				$status_r=Ebak_GetTotal($b_dbname,$btb[$t]);
				$collation=Ebak_GetSetChar($status_r['Collation']);
				DoSetDbChar($collation);
				//总记录数
				$num=$limittype?-1:$status_r['Rows'];
			}
			else
			{
				$collation=$_GET['collation'];
				DoSetDbChar($collation);
				$num=(int)$alltotal;
			}
			$dumpsql.=Ebak_ReturnSetNames($collation);
		}
		else
		{
			if(empty($s))
			{
				//总记录数
				if($limittype)
				{
					$num=-1;
				}
				else
				{
					$status_r=M()->query("SHOW TABLE STATUS LIKE '".$btb[$t]."';");//当前表的总记录数
					$num=$status_r[0]['Rows'];
				}
			}
			else
			{
				$num=(int)$alltotal;
			}
		}
		//备份数据库结构
		if(empty($s))
		{
			$dumpsql.=DB_t_stru($btb[$t]);
		}
		//取得字段数
		if(empty($fnum))
		{
			$return_fr=GetTbField($btb[$t]);
			$fieldnum=$return_fr['num'];//字段数
			$noautof=$return_fr['autof'];
		}
		else
		{
			$fieldnum=$fnum;
			$noautof=$thenof;
		}

		//完整插入
		$inf='';
		if($b_beover==1)
		{
			$inf='('.GetInsertSql($btb[$t]).')';//插入语句的字段
		}
		$b=0;
		$sql=mysql_query("select * from `".$btb[$t]."` limit $s,$num");
		while($r = mysql_fetch_array($sql))
		{
			$b=1;
			$s++;
			$dumpsql.="DB_I(\"".($this->b_insertf)." into `".$btb[$t]."`".$inf." values(";
			$first=1;
			for($i=0;$i<$fieldnum;$i++)
			{
				//首字段
				if(empty($first))
				{
					$dumpsql.=',';
				}
				else
				{
					$first=0;
				}
				$myi=$i+1;
				if(!isset($r[$i])||strstr($noautof,','.$myi.','))
				{
					$dumpsql.='NULL';
				}
				else
				{
					$dumpsql.= GetFieldStr($r[$i]);
				}
			}
			$dumpsql.=");\");\r\n";
			//是否超过限制
			if(strlen($dumpsql)>=$this->b_filesize*1024)
			{
				$p++;
				$sfile=$path."/".$btb[$t]."_".$p.".php";
				$dumpsql="<?php\r\n".$cpright.$dumpsql."\r\n?>";
				MakeFile($dumpsql,$sfile);
				//M()->free($sql);释放内存
				echo"<meta http-equiv=\"refresh\" content=\"".$this->waitbaktime.";url=".__URL__."/backup?e=$s&p=$p&t=$t&savepath=$savepath&alltotal=$num&thenof=$noautof&fieldnum=$fieldnum&stime=$stime&waitbaktime=$this->waitbaktime&collation=$collation\">成功备份{$btb[$t]}的第{$p}个分卷".EchoBakSt($btb[$t],$count,$t,$num,$s);
				exit();
			}
		}
		//最后一个备份
		if(empty($p)||$b==1)
		{
			$p++;
			$sfile=$path."/".$btb[$t]."_".$p.".php";
			$dumpsql="<?php\r\n".$cpright.$dumpsql."\r\n?>";
			MakeFile($dumpsql,$sfile);
		}
		//ReFlashConfig($p,$btb[$t],$path);
		$t++;
		//M()->free($sql);释放内存
		//进入下一个表
		echo"<meta http-equiv=\"refresh\" content=\"".$this->waitbaktime.";url=".__URL__."/backup?e=0&p=0&t=$t&savepath=$savepath&stime=$stime&waitbaktime=$this->waitbaktime\">成功备份".$btb[$t-1]."表";
		exit();
	}
	
	
}
//得到文件大小
function getMb($size)
{
	$mbsize=$size/1024/1024;
	if($mbsize>0)
	{
		list($t1,$t2)=explode(".",$mbsize);
		$mbsize=$t1.".".substr($t2,0,2);
	}
	
	if($mbsize<1){
		$kbsize=$size/1024;
		list($t1,$t2)=explode(".",$kbsize);
		$kbsize=$t1.".".substr($t2,0,2);
		return $kbsize."KB";
	}else{
		return "<span style='color:blue'>".$mbsize."MB</span>";
	}
}

//时间转换
function UseTime($time){
	$usetime=time()-$time;
	if($usetime<60)
	{
		$tstr=$usetime."秒";
	}
	else
	{
		$usetime=round($usetime/60);
		$tstr=$usetime."分";
	}
	return $tstr;
}

//返回版权信息
function Cpright(){
	$string="
/*
		SoftName : daiqile Version 2013
		Author   : fanyelei
		Copyright: Powered by www.daiqile.com
*/

";
	return $string;
}
//返回数据库结构
function DB_t_stru($table,$strufour=false){
	$dumpsql.="DB_D(\"DROP TABLE IF EXISTS `".$table."`;\");\r\n";
	//设置引号
	M()->query("SET SQL_QUOTE_SHOW_CREATE=1;");
	//数据表结构
	$r=M()->query("SHOW CREATE TABLE `$table`;");
	$create=str_replace("\"","\\\"",$r[0]['Create Table']);

	//转为4.0格式
	if($strufour)
	{
		$create=Ebak_ToMysqlFour($create);
	}
	$dumpsql.="DB_C(\"".$create."\");\r\n";
	return $dumpsql;
}


//返回表字段信息
function GetTbField($tbname,$autofield=""){
	$sql=M()->query("SHOW FIELDS FROM `".$tbname."`");
	$i=0;//字段数
	$autof=",";//去除自增字段列表
	$f='';//自增字段名
	$row=array();
	foreach($sql as $r)
	{
		$i++;
		if(strstr($autofield,",".$tbname.".".$r[Field].","))
		{
			$autof.=$i.",";
	    }
		if($r['Extra']=='auto_increment')
		{
			$f=$r['Field'];
		}
		$row[]=$r['Field'];
    }
	$return_r['num']=$i;
	$return_r['autof']=$autof;
	$return_r['auf']=$f;
	$return_r['fieldlist']=$row;
	return $return_r;
}
//返回插入语句的字段
function GetInsertSql($tbname){
	$sql=M()->query("SHOW FIELDS FROM `".$tbname."`");
	$f='';
	$dh='';
	foreach($sql as $r)
	{
		$f.=$dh.'`'.$r['Field'].'`';
		$dh=',';
    }
	return $f;
}
//返回字段内容
function GetFieldStr($str){
	$restr='\''.escape_str($str).'\'';
	return $restr;
}
//字符过虑
function escape_str($str){
	$str=mysql_escape_string($str);
	$str=str_replace('\\\'','\'\'',$str);
	$str=str_replace("\\\\","\\\\\\\\",$str);
	$str=str_replace('$','\$',$str);
	return $str;
}
//更新记录文件
function ReFlashConfig($p,$table,$path){
	if(empty($p))
	{$p=0;}
	$file=$path."/config.php";
	$text=ReadFiletext($file);
	$rep1="\$tb[".$table."]=0;";
	$rep2="\$tb[".$table."]=".$p.";";
	$text=str_replace($rep1,$rep2,$text);
	MakeFile($text,$file);
}
//输出备份进度条
function EchoBakSt($tbname,$tbnum,$tb,$rnum,$r){
	$table=($tb+1).'/'.$tbnum;
	$record=$r;
	if($rnum!=-1)
	{
		$record=$r.'/'.$rnum;
	}
	echo '
	<br><br>
	<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1">
		<tr><td height="25">Table Name&nbsp;:&nbsp;<b>'.$tbname.'</b></td></tr>
		<tr><td height="25">Table&nbsp;:&nbsp;<b>'.$table.'</b></td></tr>
		<tr><td height="25">Record&nbsp;:&nbsp;<b>'.$record.'</b></td></tr>
	</table><br><br>
	';
}
//运行mysql;删除语句
function DB_D($mysql){
	M()->query($mysql);
}
//运行mysql;建表语句
function DB_C($mysql){
	M()->query($mysql);
}
//运行mysql;插入语句
function DB_I($mysql){
	M()->query($mysql);
}

?>