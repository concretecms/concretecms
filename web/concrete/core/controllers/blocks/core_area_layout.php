<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreAreaLayout extends BlockController {

		protected $btCacheBlockRecord = false;
		protected $btSupportsInlineEditing = true;		
		protected $btTable = 'btCoreAreaLayout';
		/* protected $btIsInternal = true; */

		public function getBlockTypeDescription() {
			return t("Proxy block for area layouts.");
		}
		
		public function getBlockTypeName() {
			return t("Area Layout (Core)");
		}

		public function on_start() {
			$this->arLayout = AreaLayout::getByID($this->arLayoutID);
		}

		public function save($post) {
			if (!$post['arLayoutID']) {
				// we are adding a new layout 

			
				if (!$post['isautomated']) {
					$iscustom = 1;
				} else {
					$iscustom = 0;
				}
				$ar = AreaLayout::add($post['spacing'], $iscustom);
				for ($i = 0; $i < $post['columns']; $i++) {
					$width = ($post['width'][$i]) ? $post['width'][$i] : 0;
					$ar->addLayoutColumn($width);
				}
				$values = array('arLayoutID' => $ar->getAreaLayoutID());
			}

			parent::save($values);
		}

		public function view() {
			$this->set('columns', $this->arLayout->getAreaLayoutColumns());
		}

		public function edit() {
			$this->view();
			$this->set('spacing', $this->arLayout->getAreaLayoutSpacing());
			$this->set('iscustom', $this->arLayout->hasAreaLayoutCustomColumnWidths());
		}

		public function on_page_view() {
			$this->on_start();
			$wrapper = 'ccm-layout-column-wrapper-' . $this->bID;
			$columns = $this->arLayout->getAreaLayoutColumns();
			if (count($columns) > 0) {
				$margin = ($this->arLayout->getAreaLayoutSpacing() / 2);
				if ($this->arLayout->hasAreaLayoutCustomColumnWidths()) {
					$css = '';
					foreach($columns as $col) {
						$arLayoutColumnIndex = $col->getAreaLayoutColumnIndex();
						$width = $col->getAreaLayoutColumnWidth();
						if ($width) {
							$width .= 'px';
						}

						$css .= "#{$wrapper} div#ccm-layout-column-{$arLayoutColumnIndex} { width: {$width}; }\n";
					}

				} else {
					$width = (100 / count($columns));
					$css = <<<EOL

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

				$this->addHeaderItem('<style type="text/css">' . $css  . '</style>');
			}
		}


	}