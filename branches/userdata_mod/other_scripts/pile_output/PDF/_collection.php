<?
class PileOutputHandler_PDF_collection {

	function generateOutput(&$cObj) {
		// take a collection object, and return a pile output object (which basically has only 2 attributes at this point and time)
		
		$po = new PileContentOutput_PDF;
		$po->title = $cObj->getCollectionName();
		//$po->content = $cObj->getCollectionDescription();
		//$po->content .= "<br><br><br>";
		$pt = new Area('Page Content');
		
		ob_start();
		$ptb = $pt->getAreaBlocksArray($cObj);
		$pbArray = array();
		$allowedBTs = array('content','content-definable','content-reviewable','image');
		foreach($ptb as $pb) {
			if (in_array($pb->getBlockTypeHandle(), $allowedBTs)) {
				$ppb = new Permissions($pb);
				$con = new Content;
				$con->buildBlockDisplay($pb, $ppb);
			}
		}
		$contents = ob_get_contents();
		ob_end_clean();
		
		$po->content .= $contents;
		return $po;
	}
}

?>