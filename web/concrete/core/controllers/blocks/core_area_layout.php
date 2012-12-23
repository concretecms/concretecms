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

		public function save($post) {
			if (!$post['spacing']) {
				$post['spacing'] = 0;
			}
			if (!$post['isautomated']) {
				$post['iscustom'] = 1;
			} else {
				$post['iscustom'] = 0;
			}

			parent::save($post);

			if ($post['iscustom']) {
				$db = Loader::db();
				$db->Execute('delete from btCoreAreaLayoutCustomColumns where bID = ?', array($this->bID));
				for ($i = 0; $i < $post['columns']; $i++) {
					$width = (isset($post['width'][$i])) ? $post['width'][$i] : 0;
					$db->Execute('insert into btCoreAreaLayoutCustomColumns (bID, columnIndex, width) values (?, ?, ?)', array($this->bID, $i, $width));
				}
			}
		}
		
		public function on_page_view() {
			$wrapper = 'ccm-layout-column-wrapper-' . $this->bID;
			$width = (100 / $this->columns);
			$margin = ($this->spacing / 2);

			$css = <<<EOL

			#{$wrapper} div.ccm-layout-column {
				width: {$width}%;
			}

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