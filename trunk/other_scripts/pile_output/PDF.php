<?
require_once(FPDF);

class POPDF extends FPDF {
	
	function Footer() {
		$this->SetY(6.5);
		$this->SetFont('Arial','',9);
		$this->Cell(0,0.1,'Page ' . $this->PageNo() ,0,0,'C');
	}
	
	var $B=0;
	var $I=0;
	var $U=0;
	var $HREF='';
	var $ALIGN='';
	var $col = 0;
	
	var $pageArray = array();
	var $lastBufferIndex = 0;
	
	function WriteHTML($html)
	{
		//HTML parser
		$html=str_replace("\n",' ',$html);
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
		if($i%2==0)
		{
		//Text
		if($this->HREF)
		$this->PutLink($this->HREF,$e);
		elseif($this->ALIGN == 'center')
		$this->Cell(0,0.2,$e,0,1,'C');
		else
		$this->Write(0.2,$e);
		}
		else
		{
		//Tag
		if($e{0}=='/')
		$this->CloseTag(strtoupper(substr($e,1)));
		else
		{
		//Extract properties
		$a2=split(' ',$e);
		$tag=strtoupper(array_shift($a2));
		$prop=array();
		foreach($a2 as $v)
		if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
		$prop[strtoupper($a3[1])]=$a3[2];
		$this->OpenTag($tag,$prop);
		}
		}
		}
	}
	
	
	function OpenTag($tag,$prop) {
		//Opening tag
		if($tag=='B' or $tag=='I' or $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF=$prop['HREF'];
		if($tag=='BR')
			$this->Ln(0.2);
		if($tag=='P')
			$this->ALIGN=$prop['ALIGN'];
		if($tag=='HR') {
			if( $prop['WIDTH'] != '' )
				$Width = $prop['WIDTH'];
			else
				$Width = $this->w - $this->lMargin-$this->rMargin;
			$this->Ln(2);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetLineWidth(0.4);
			$this->Line($x,$y,$x+$Width,$y);
			$this->SetLineWidth(0.2);
			$this->Ln(0.2);
		}
		if ($tag == 'IMG') {
			if (isset($prop['SRC'])) {
				$this->Image($prop['SRC'], $this->GetX(), $this->GetY());
				$this->Ln();
			}
		}
	}
	
	function px2mm($px) {
		return $px * 25.4 / 72;
	}
	
	function CloseTag($tag) {
		if($tag=='B' or $tag=='I' or $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
		if($tag=='P')
			$this->ALIGN='';
	}
	
	function SetStyle($tag,$enable) {
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s) {
			if($this->$s>0) {
				$style.=$s;
			}
		}
		$this->SetFont('',$style);
	}
	
	function PutLink($URL,$txt) {
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(0.1,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
}

class PileOutput_PDF {
	
	function output($poArray) {
		if (is_array($poArray)) {
			$pdf = new POPDF('P','in', array('3.5','7'));
			$pdf->setMargins(0,0);
			$pb = array();		
			foreach($poArray as $po) {
				$pdf->AddPage();
				$pdf->SetFont('Arial','B',16);
				$pdf->Cell(0,0.4, $po->title);
				$pdf->SetFont('Arial','',12);
				$pdf->Ln();
				
				//$content = str_replace(array('<br/>','<br>','<br />'), array("\n","\n","\n"), $po->content);
				//$content = strip_tags($content);
				//$pdf->MultiCell(0,0.2, $po->content, 0,'L');
				$pdf->WriteHTML($po->content);
			}
			
			// ensure that we get an even number of pages

			if (count($pdf->pages) %2 != 0) {
				$pdf->AddPage();
			}
			$pageSub = count($pdf->pages) / 2;
			if ($pageSub % 2 != 0) {
				$pdf->AddPage();
				$pdf->AddPage();
			}
			$tempName = rand(000000,999999) . '.pdf';
			$pdf->Output(FPDF_TEMP . '/' . $tempName, 'F');
			
			$height = "8.5";
			$width = "5.5";
			$gutter = "0.5";
			$units = "in";
			
			$w = $height; // sizes of booklet sheet, not original pdf page
			$h = $width*2; // h & w could be read from template (but not units?)
			
			$size = array($w, $h);
			$pdf2 = new fpdi('l', $units, $size); // should always be landscape
			$dm = 'real';
			$pdf2->setDisplayMode($dm);
			$pagecount = $pdf2->setSourceFile(FPDF_TEMP . '/' . $tempName);
			$upper = $pagecount;
			
			for($lower=1; $lower <= ($pagecount / 2); $lower++){
				
				$pdf2->addPage();
								
				if ($lower%2 == 0) {       // even
					$right = $upper;
					$left = $lower;
				} else {                  // odd
					$right = $lower;
					$left = $upper;
				}
				
				$lower==1 ? $gut = 0 : $gut = $gutter; // no gutter on cover page
				$tplidx = $pdf2->ImportPage($left);
				$pdf2->useTemplate($tplidx, 1, 0.75);
				
				// page for right side of sheet
				$tplidx = $pdf2->ImportPage($right);
				$pdf2->useTemplate($tplidx, 6.5, 0.75);
				$upper--;			
			}
			
			$pdf2->Output();
		}
	}
	
}


class PileContentOutput_PDF extends PileContentOutput {

	// nothing in this class yet, though
	
	
}
?>