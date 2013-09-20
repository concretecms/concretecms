<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Block_CoreGathering extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreGathering';
		protected $btSupportsInlineEdit = true;

		public function getBlockTypeDescription() {
			return t("Displays pages and data in list or grids.");
		}
		
		public function getBlockTypeName() {
			return t("Gathering");
		}

		public function duplicate($newBID) {
			$ni = parent::duplicate($newBID);
			$ag = Gathering::getByID($this->gaID);
			$nr = $ag->duplicate();
			$db = Loader::db();
			$db->Execute('update btCoreGathering set gaID = ? where bID = ?', array($nr->getGatheringID(), $ni->bID));
		}

		protected function setupForm() {
			$gathering = false;
			$activeSources = array();
			if ($this->gaID) {
				$gathering = Gathering::getByID($this->gaID);
				$activeSources = $gathering->getConfiguredGatheringDataSources();
			}
			$availableSources = GatheringDataSource::getList();
			$this->set('availableSources', $availableSources);
			$this->set('activeSources', $activeSources);
			$this->set('gathering', $gathering);
		}

		public function getGatheringObject() {
			if (!isset($this->gathering)) {
				// i don't know why this->cnvid isn't sticky in some cases, leading us to query
				// every damn time
				$db = Loader::db();
				$agID = $db->GetOne('select gaID from btCoreGathering where bID = ?', array($this->bID));
				$this->gathering = Gathering::getByID($agID);
			}
			return $this->gathering;
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
			if (is_object($composer) && $this->enablePostingFromGathering) {
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
			$agID = $db->GetOne('select gaID from btCoreGathering where bID = ?', array($this->bID));
			if (!$agID) {
				$ag = Gathering::add();
				$task = 'add';
			} else {
				$ag = Gathering::getByID($agID);
				$task = 'edit';
			}

			$tab = $args['tab'];
			if (!is_array($tab)) {
				$tab = array();
			}
			if ($task == 'add' || in_array('sources', $tab)) {
				$ag->clearConfiguredGatheringDataSources();
				$sources = $this->post('gasID');
				foreach($sources as $key => $gasID) {
					$key = (string) $key; // because PHP is stupid
					if ($key != '_gas_') {
						$ags = GatheringDataSource::getByID($gasID);
						$ags->setOptionFormKey($key);
						$post = $ags->getOptionFormRequestData();
						$agc = $ags->configure($ag, $post);	
					}
				}
				$ag->generateGatheringItems();
			}


			$itemsPerPage = intval($args['itemsPerPage']);
			$values = array(
				'gaID' => $ag->getGatheringID()
			);

			if (in_array('output', $tab)) {
				$values['itemsPerPage'] = $itemsPerPage;
			} else {
				$values['itemsPerPage'] = $this->itemsPerPage;
			}

			if (in_array('posting', $tab)) {
				$cmpID = 0;
				if ($args['enablePostingFromGathering']) {
					$values['enablePostingFromGathering'] = 1;
					if ($args['cmpID']) {
						$cmpID = $args['cmpID'];
					}
				} else {
					$values['enablePostingFromGathering'] = 0;
				}
				$values['cmpID'] = $cmpID;	
			} else {
				$values['cmpID'] = $this->cmpID;	
				$values['enablePostingFromGathering'] = $this->enablePostingFromGathering;	
			}
			parent::save($values);

		}

		public function delete() {
			parent::delete();
			if ($this->gaID) {
				$gathering = Gathering::getByID($this->gaID);
				if (is_object($gathering)) {
					$gathering->delete();
				}
			}
		}
		public function view() {
			if ($this->gaID) {
				$gathering = Gathering::getByID($this->gaID);
				if (is_object($gathering)) {	
					Request::get()->requireAsset('core/gathering');
					Loader::helper('overlay')->init(false);
					if ($this->enablePostingFromGathering && $this->cmpID) {
						$cmp = Composer::getByID($this->cmpID);
						Loader::helper('composer')->addAssetsToRequest($cmp, $this);
						$p = new Permissions($cmp);
						if ($p->canAccessComposer()) {
							$this->set('composer', $cmp);
						}
					}		
					$list = new GatheringItemList($gathering);
					$list->sortByDateDescending();
					$list->setItemsPerPage($this->itemsPerPage);
					$this->set('gathering', $gathering);
					$this->set('itemList', $list);
				}
			}
		}

	}

