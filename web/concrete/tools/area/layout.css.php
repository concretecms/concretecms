<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/numbers')->integer($_REQUEST['bID'])) {
	$b = Block::getByID($_REQUEST['bID']);
	if (is_object($b)) {
		$bc = $b->getController();
		if ($bc instanceof CoreAreaLayoutBlockController) {
			if (!is_object($bc->getAreaLayoutObject())) {
				die;
			}

			$arLayout = $bc->getAreaLayoutObject();

			header("Content-Type: text/css");
			$css = <<<EOL
div.ccm-layout-column {
	float: left;
}

/* clearfix */

div.ccm-layout-column-wrapper {*zoom:1;}
div.ccm-layout-column-wrapper:before, div.ccm-layout-column-wrapper:after {display:table;content:"";line-height:0;}
div.ccm-layout-column-wrapper:after {clear:both;}

EOL;
			$wrapper = 'ccm-layout-column-wrapper-' . $b->getBlockID();
			$columns = $arLayout->getAreaLayoutColumns();
			if (count($columns) > 0) {
				$margin = ($arLayout->getAreaLayoutSpacing() / 2);
				if ($arLayout->hasAreaLayoutCustomColumnWidths()) {
					foreach($columns as $col) {
						$arLayoutColumnID = $col->getAreaLayoutColumnID();
						$width = $col->getAreaLayoutColumnWidth();
						if ($width) {
							$width .= 'px';
						}

						$css .= "#{$wrapper} div#ccm-layout-column-{$arLayoutColumnID} { width: {$width}; }\n";
					}

				} else {
					$width = (100 / count($columns));
					$css .= <<<EOL

#{$wrapper} div.ccm-layout-column {
	width: {$width}%;
}
EOL;
				
				}

				$css .= <<<EOL

#{$wrapper} div.ccm-layout-column-inner {
	margin-right: {$margin}px;
	margin-left: {$margin}px;
}

#{$wrapper} div.ccm-layout-column:first-child div.ccm-layout-column-inner {
	margin-left: 0px;
}

#{$wrapper} div.ccm-layout-column:last-child div.ccm-layout-column-inner  {
	margin-right: 0px;
}
EOL;

				print $css;
			}			
		}
	}
}
