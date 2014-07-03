<?php
// 本类由系统自动生成，仅供测试用途
class AgreementAction extends MCommonAction {
	var $mypdf=NULL;
	var $pdfPath=NULL;
	public function _MyInit(){
		$this->pdfPath = C("APP_ROOT").'Lib/Tcpdf/';
		require $this->pdfPath.'config/lang/eng.php';
		require $this->pdfPath.'tcpdf.php';
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(str_replace("http://","",C('WEB_URL')));
		$pdf->SetTitle('借款合同');
		$pdf->SetSubject('借款合同');
		$pdf->SetKeywords('借款, 合同');
		
		// set default header data
		//页头
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", str_replace("http://","",C('WEB_URL')));
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('droidsansfallback', '', 20);
		
		// add a page
		$pdf->AddPage();


		$this->mypdf = $pdf;
	}
	
    public function downfile(){
		$per = C('DB_PREFIX');
		$borrow_config = require C("APP_ROOT")."Conf/borrow_config.php";
		$invest_id=intval($_GET['id']);
		//$borrow_id=intval($_GET['id']);
		$iinfo = M('borrow_investor')->field('borrow_id,investor_capital,investor_interest,deadline,investor_uid')->where("(investor_uid={$this->uid} OR borrow_uid={$this->uid}) AND id={$invest_id}")->find();
		$binfo = M('borrow_info')->field('repayment_type,borrow_duration,borrow_uid,borrow_type,full_time,add_time,borrow_interest_rate')->find($iinfo['borrow_id']);
		$mBorrow = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$binfo['borrow_uid']}")->find();
		$mInvest = M("members m")->join("{$per}member_info mi ON mi.uid=m.id")->field('mi.real_name')->where("m.id={$iinfo['investor_uid']}")->find();
		if(!is_array($iinfo)||!is_array($binfo)||!is_array($mBorrow)||!is_array($mInvest)) exit;
		
		$type = $borrow_config['REPAYMENT_TYPE'];
		//echo $binfo['repayment_type'];
		$binfo['repayment_name'] = $type[$binfo['repayment_type']];
		$iinfo['repay'] = getFloatValue(($iinfo['investor_capital']+$iinfo['investor_interest'])/$binfo['borrow_duration'],2);
		//print_r($type);
		$this->assign('iinfo',$iinfo);
		$this->assign('binfo',$binfo);
		$this->assign('mBorrow',$mBorrow);
		$this->assign('mInvest',$mInvest);

		$detail_list = M('investor_detail')->field(true)->where("invest_id={$invest_id}")->select();
		$this->assign("detail_list",$detail_list);


		
		$html = $this->fetch('index');
		
		$this->mypdf->writeHTML($html, true, false, true, false, '');
		//$this->mypdf->MultiCell(0, 5, "ssssssssssssssssssssssssssssssss", 0, 'J', 0, 2, '', '', true, 0, false);		
		
		//路径,x坐标,y坐标,图片宽度,图片高度（''表示自适应）,网址,
		//$mask = $this->mypdf->Image($this->pdfPath.'images/alpha.png', 10, 10, 10, '', '', '', '', false, 100, '', true);
		//$this->mypdf->Image($this->pdfPath.'images/image_with_alpha.png', 70, 120, 60, 60, '', '', '', false, 10, '', true, $mask);//出图的,放在后面图就在上层，放在前面图就在下层

		// ---------------------------------------------------------
		
		//Close and output PDF document
		$this->mypdf->Output('jiedaihetong.pdf', 'I');
		
    }

}