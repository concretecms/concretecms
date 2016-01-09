<?php
namespace Concrete\Controller\Frontend;
use Concrete\Core\Area\Layout\CustomLayout;
use Concrete\Core\Area\Layout\Layout;
use Controller;
use Page;
use Permissions;
use Block;
use Area;
use Response;
class Stylesheet extends Controller {

	public function page_version($cID, $stylesheet, $cvID)
    {
		$c = Page::getByID($cID);
		if (is_object($c) && !$c->isError()) {
			$cp = new Permissions($c);
			if ($cp->canViewPageVersions()) {
				$c->loadVersionObject($cvID);

                $theme = $c->getCollectionThemeObject();
                $stylesheet = $theme->getStylesheetObject($stylesheet);
                $style = $c->getCustomStyleObject();
                if (is_object($style)) {
                    $scl = $style->getValueList();
                    $stylesheet->setValueList($scl);
                }
				$response = new Response();
				$response->headers->set('Content-Type', 'text/css');
				$response->setContent($stylesheet->getCss());
				return $response;
			}
		}
	}

    public function page($cID, $stylesheet)
    {
		$c = Page::getByID($cID, 'ACTIVE');
		if (is_object($c) && !$c->isError()) {
			$cp = new Permissions($c);
			if ($cp->canViewPage()) {
                $theme = $c->getCollectionThemeObject();
                $stylesheet = $theme->getStylesheetObject($stylesheet);
                $style = $c->getCustomStyleObject();
                if (is_object($style)) {
                    $scl = $style->getValueList();
                    $stylesheet->setValueList($scl);
                }
				$response = new Response();
				$response->headers->set('Content-Type', 'text/css');
				$response->setContent($stylesheet->getCss());
				return $response;
			}
		}
    }

	public function layout($arLayoutID) {
		$arLayout = Layout::getByID($arLayoutID);
		if (is_object($arLayout) && $arLayout instanceof CustomLayout) {

			$css = <<<EOL
	div.ccm-layout-column {
			float: left;
		}

		/* clearfix */

		div.ccm-layout-column-wrapper {*zoom:1;}
		div.ccm-layout-column-wrapper:before, div.ccm-layout-column-wrapper:after {display:table;content:"";line-height:0;}
		div.ccm-layout-column-wrapper:after {clear:both;}

EOL;
			$wrapper = 'ccm-layout-column-wrapper-' . $arLayout->getAreaLayoutID();
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

				$response = new Response();
				$response->setContent($css);
				$response->headers->set('Content-Type', 'text/css');
				return $response;
			}
		}
	}
}

