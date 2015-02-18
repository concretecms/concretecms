<?php
namespace Concrete\Block\CoreGathering;
use Loader;
use \Concrete\Core\Gathering\Gathering;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

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
			$pagetype = PageType::getByID($this->ptID);
			if (is_object($pagetype) && $this->enablePostingFromGathering) {
				$ccp = new Permissions($pagetype);
				if ($ccp->canEditPageTypeInComposer()) {

					$ct = PageType::getByID($this->post('ptComposerPageTypeID'));
					$availablePageTypes = $pagetype->getComposerPageTypeObjects();

					if (!is_object($ct) && count($availablePageTypes) == 1) {
						$ct = $availablePageTypes[0];
					}

					$c = Page::getCurrentPage();
					$e = $pagetype->validatePublishRequest($ct, $c);
					$r = new PageTypePublishResponse($e);
					if (!$e->has()) {
						$d = $pagetype->createDraft($ct);
						$d->setPageDraftTargetParentPageID($c->getCollectionID());
						$d->saveForm();
						$d->publish();
						$nc = Page::getByID($d->getCollectionID(), 'RECENT');
						$link = Loader::helper('navigation')->getLinkToCollection($nc, true);
						$r->setRedirectURL($link);
					}
					$r->outputJSON();
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
				$ptID = 0;
				if ($args['enablePostingFromGathering']) {
					$values['enablePostingFromGathering'] = 1;
					if ($args['ptID']) {
						$ptID = $args['ptID'];
					}
				} else {
					$values['enablePostingFromGathering'] = 0;
				}
				$values['ptID'] = $ptID;	
			} else {
				$values['ptID'] = $this->ptID;	
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

        public function registerViewAssets()
        {
            $this->requireAsset('core/gathering');
        }

		public function view() {
			if ($this->gaID) {
				$gathering = Gathering::getByID($this->gaID);
				if (is_object($gathering)) {
					Loader::helper('overlay')->init(false);
					if ($this->enablePostingFromGathering && $this->ptID) {
						$pt = PageType::getByID($this->ptID);
						Loader::helper('concrete/composer')->addAssetsToRequest($pt, $this);
						$p = new Permissions($pt);
						if ($p->canEditPageTypeInComposer()) {
							$this->set('pagetype', $pt);
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

