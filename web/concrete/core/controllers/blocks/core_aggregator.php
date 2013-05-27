<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreAggregator extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreAggregator';

		public function getBlockTypeDescription() {
			return t("Displays pages and data in list or grids.");
		}
		
		public function getBlockTypeName() {
			return t("Aggregator");
		}

		public function duplicate($newBID) {
			$ni = parent::duplicate($newBID);
			$ag = Aggregator::getByID($this->agID);
			$nr = $ag->duplicate();
			$db = Loader::db();
			$db->Execute('update btCoreAggregator set agID = ? where bID = ?', array($nr->getAggregatorID(), $ni->bID));
		}

		protected function setupForm() {
			$aggregator = false;
			$activeSources = array();
			if ($this->agID) {
				$aggregator = Aggregator::getByID($this->agID);
				$activeSources = $aggregator->getConfiguredAggregatorDataSources();
			}
			$availableSources = AggregatorDataSource::getList();
			$this->set('availableSources', $availableSources);
			$this->set('activeSources', $activeSources);
			$this->set('aggregator', $aggregator);
		}

		public function getAggregatorObject() {
			if (!isset($this->aggregator)) {
				// i don't know why this->cnvid isn't sticky in some cases, leading us to query
				// every damn time
				$db = Loader::db();
				$agID = $db->GetOne('select agID from btCoreAggregator where bID = ?', array($this->bID));
				$this->aggregator = Aggregator::getByID($agID);
			}
			return $this->aggregator;
		}

		public function add() {
			$this->setupForm();
		}

		public function edit() {
			$this->setupForm();
		}

		public function save($args) {
			$db = Loader::db();
			$agID = $db->GetOne('select agID from btCoreAggregator where bID = ?', array($this->bID));
			if (!$agID) {
				$ag = Aggregator::add();
				$task = 'add';
			} else {
				$ag = Aggregator::getByID($agID);
				$task = 'edit';
			}

			if ($task == 'add' || $_POST['rescanAggregatorItems']) {
				$ag->clearConfiguredAggregatorDataSources();
				$sources = $this->post('agsID');
				foreach($sources as $key => $agsID) {
					$key = (string) $key; // because PHP is stupid
					if ($key != '_ags_') {
						$ags = AggregatorDataSource::getByID($agsID);
						$ags->setOptionFormKey($key);
						$post = $ags->getOptionFormRequestData();
						$agc = $ags->configure($ag, $post);	
					}
				}
				$ag->generateAggregatorItems();
			}


			$itemsPerPage = intval($args['itemsPerPage']);
			$values = array(
				'agID' => $ag->getAggregatorID(),
				'itemsPerPage' => $itemsPerPage
			);
			parent::save($values);

		}

		public function on_page_view() {
			if ($this->agID) {
				$aggregator = Aggregator::getByID($this->agID);
				if (is_object($aggregator)) {
					$this->addHeaderItem(Loader::helper('html')->css('ccm.aggregator.css'));
					$this->addFooterItem(Loader::helper('html')->javascript('ccm.aggregator.js'));
				}
			}
		}

		public function delete() {
			parent::delete();
			if ($this->agID) {
				$aggregator = Aggregator::getByID($this->agID);
				if (is_object($aggregator)) {
					$aggregator->delete();
				}
			}
		}

		public function view() {
			if ($this->agID) {
				$aggregator = Aggregator::getByID($this->agID);
				if (is_object($aggregator)) {					
					$list = new AggregatorItemList($aggregator);
					$list->sortByDateDescending();
					$list->setItemsPerPage($this->itemsPerPage);
					$this->set('aggregator', $aggregator);
					$this->set('itemList', $list);
				}
			}
		}

	}

