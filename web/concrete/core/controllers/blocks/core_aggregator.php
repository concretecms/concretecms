<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreAggregator extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreAggregator';
		protected $btSupportsInlineEdit = true;

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
			$this->view();
		}

		public function action_post() {
			// happens through ajax
			$composer = Composer::getByID($this->cmpID);
			if (is_object($composer) && $this->enablePostingFromAggregator) {
				$ccp = new Permissions($composer);
				if ($ccp->canAccessComposer()) {

					$ct = CollectionType::getByID($this->post('cmpPageTypeID'));
					$availablePageTypes = $composer->getComposerPageTypeObjects();

					if (!is_object($ct) && count($availablePageTypes) == 1) {
						$ct = $availablePageTypes[0];
					}

					$c = Page::getCurrentPage();
					$e = $composer->validatePublishRequest($ct, $c);
					$r = new ComposerPublishResponse($e);
					if (!$e->has()) {
						$d = $composer->createDraft($ct);
						$d->setComposerDraftTargetParentPageID($c->getCollectionID());
						$d->saveForm();
						$d->publish();
						$nc = Page::getByID($d->getComposerDraftCollectionID(), 'RECENT');
						$link = Loader::helper('navigation')->getLinkToCollection($nc, true);
						$r->setRedirectURL($link);
					}
					print Loader::helper('ajax')->sendResult($r);
				}
			}
			exit;
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

			$tab = $args['tab'];
			if (!is_array($tab)) {
				$tab = array();
			}
			if ($task == 'add' || in_array('sources', $tab)) {
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
				'agID' => $ag->getAggregatorID()
			);

			if (in_array('output', $tab)) {
				$values['itemsPerPage'] = $itemsPerPage;
			} else {
				$values['itemsPerPage'] = $this->itemsPerPage;
			}

			if (in_array('posting', $tab)) {
				$cmpID = 0;
				if ($args['enablePostingFromAggregator']) {
					$values['enablePostingFromAggregator'] = 1;
					if ($args['cmpID']) {
						$cmpID = $args['cmpID'];
					}
				} else {
					$values['enablePostingFromAggregator'] = 0;
				}
				$values['cmpID'] = $cmpID;	
			} else {
				$values['cmpID'] = $this->cmpID;	
				$values['cmpID'] = $this->enablePostingFromAggregator;	
			}

			parent::save($values);

		}

		public function on_page_view() {
			if ($this->agID) {
				$aggregator = Aggregator::getByID($this->agID);
				if (is_object($aggregator)) {
					$this->addHeaderItem(Loader::helper('html')->css('ccm.aggregator.css'));
					$this->addFooterItem(Loader::helper('html')->javascript('ccm.aggregator.js'));
					Loader::helper('overlay')->init(false);
					if ($this->enablePostingFromAggregator) {
						$cmp = Composer::getByID($this->cmpID);
						Loader::helper('composer/form')->addAssetsToRequest($cmp, $this);
					}
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
					if ($this->enablePostingFromAggregator && $this->cmpID) {
						$cmp = Composer::getByID($this->cmpID);
						$p = new Permissions($cmp);
						if ($p->canAccessComposer()) {
							$this->set('composer', $cmp);
						}
					}		
					$list = new AggregatorItemList($aggregator);
					$list->sortByDateDescending();
					$list->setItemsPerPage($this->itemsPerPage);
					$this->set('aggregator', $aggregator);
					$this->set('itemList', $list);
				}
			}
		}

	}

