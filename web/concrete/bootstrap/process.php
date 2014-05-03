<?
	defined('C5_EXECUTE') or die("Access Denied.");
    use \Concrete\Core\Page\Style\CustomStyleRule;
    use \Concrete\Core\Page\Style\CustomStylePreset;

	# Filename: _process.php
	# Author: Andrew Embler (andrew@concrete5.org)
	# -------------------
	# _process.php is included at the top of the dispatcher and basically
	# checks to see if a any submits are taking place. If they are, then
	# _process makes sure that they're handled correctly


	// if we don't have a valid token we die
	$valt = Loader::helper('validation/token');
	$token = '&' . $valt->getParameter();

	// If the user has checked out something for editing, we'll increment the lastedit variable within the database
	$u = new User();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$u->refreshCollectionEdit($c);
	}

	$securityHelper = Loader::helper('security');

	if (isset($_REQUEST['btask']) && $_REQUEST['btask'] && $valt->validate()) {

		// these are tasks dealing with blocks (moving up, down, removing)

		switch ($_REQUEST['btask']) {
			/*
			case 'ajax_do_arrange':
				if ($cp->canEditPageContents()) {
					$nvc = $c->getVersionToModify();
					return false;
					// handle dragging gathering items
					$nvc->processArrangement($_POST['area'], $_POST['block'], $_POST['blocks']);

					if (!is_object($r)) {
						$r = new stdClass;
						$r->error = false;
					}

				} else {
					$r = new stdClass;
					$r->error = true;
					$r->message = t('Access Denied');
				}

				print Loader::helper('json')->encode($r);
				exit;
				break;
				*/
			case 'remove':
				$a = Area::get($c, $_REQUEST['arHandle']);
				if (is_object($a)) {
					$ax = $a;
					$cx = $c;
					if ($a->isGlobalArea()) {
						$ax = STACKS_AREA_NAME;
						$cx = Stack::getByName($_REQUEST['arHandle']);
					}

					$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
					$p = new Permissions($b); // might be block-level, or it might be area level
					// we're removing a particular block of content
					if ($p->canDeleteBlock()){
						$nvc = $cx->getVersionToModify();

						if ($a->isGlobalArea()) {
							$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
							$xvc->relateVersionEdits($nvc);
						}

						$b->loadNewCollection($nvc);

						$b->deleteBlock();
						$nvc->rescanDisplayOrder($_REQUEST['arHandle']);

						if (isset($_POST['isAjax'])) {
							exit;
						}

						$cID = $securityHelper->sanitizeInt($_GET['cID']);

						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit' . $step);
						exit;
					}
				}
				break;
			case 'update_block_css':
				$a = Area::get($c, $_REQUEST['arHandle']);
				if (is_object($a)) {
					$ax = $a;
					$cx = $c;
					if ($a->isGlobalArea()) {
						$ax = STACKS_AREA_NAME;
						$cx = Stack::getByName($_REQUEST['arHandle']);
					}

					$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
					$p = new Permissions($b);
					if ($p->canEditBlockDesign()){

						$updateAll = false;
						$scrapbookHelper=Loader::helper('concrete/scrapbook');
						$globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage();
						if ($globalScrapbookC->getCollectionID() == $c->getCollectionID()) {
							$updateAll = true;
						}

						

						$nvc = $cx->getVersionToModify();
						if ($a->isGlobalArea()) {
							$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
							$xvc->relateVersionEdits($nvc);
						}
						$b->loadNewCollection($nvc);

						//if this block is being changed, make sure it's a new version of the block.
						if ($b->isAlias() ) {
							$nb = $b->duplicate($nvc);
							$b->deleteBlock();
							$b = $nb;
						}

						if ($_POST['reset_css']) {
							$b->resetBlockCustomStyle($updateAll);
						} else {

							if ($_POST['cspID'] > 0 && ($_POST['cspPresetAction'] == 'update_existing_preset')) {
								$csp = CustomStylePreset::getByID($_POST['cspID']);
								$csr = $csp->getCustomStylePresetRuleObject();
								// we update the csr in case anything has been changed
								$csr->update($_POST['css_id'], $_POST['css_class_name'], $_POST['css_custom'], $_POST);
								$b->setBlockCustomStyle($csr, $updateAll);
							} else {
								$csr = CustomStyleRule::add($_POST['css_id'], $_POST['css_class_name'], $_POST['css_custom'], $_POST);
								$b->setBlockCustomStyle($csr, $updateAll);
							}

							if ($_POST['cspPresetAction'] == 'create_new_preset') {
								CustomStylePreset::add($_POST['cspName'], $csr);
							}
						}

						$cID = $securityHelper->sanitizeInt($_GET['cID']);

						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit' . $step);
					}
				}
				break;

			case 'mc_alias':
				$a = Area::get($c, $_GET['arHandle']);
				if (is_object($a)) {
					$b = Block::getByID($_GET['bID'], $c, $a);
					$p = new Permissions($b);
					if ($p->canAdminBlock() && $c->isMasterCollection()) {
						if (is_array($_POST['cIDs'])) {
							foreach($_POST['cIDs'] as $cID) {
								$nc = Page::getByID($cID);
								if (!$b->isAlias($nc)) {
									$b->alias($nc);
								}
							}
						}
						// now remove any items that WERE checked and now aren't
						if (is_array($_POST['checkedCIDs'])) {
							foreach($_POST['checkedCIDs'] as $cID) {
								if (!(is_array($_POST['cIDs'])) || (!in_array($cID, $_POST['cIDs']))) {
									$nc = Page::getByID($cID, 'RECENT');
									$nb = Block::getByID($_GET['bID'], $nc, $a);
									if (is_object($nb) && (!$nb->isError())) {
										$nb->deleteBlock();
									}
									$nc->rescanDisplayOrder($_REQUEST['arHandle']);
								}

							}
						}


						$obj = new stdClass;
						$obj->bID = $b->getBlockID();
						$obj->aID = $a->getAreaID();
						$obj->arHandle= $a->getAreaHandle();
						$obj->error = false;

						print Loader::helper('json')->encode($obj);
						exit;
					}
				}
				break;
			case 'update_information':
				$a = Area::get($c, $_GET['arHandle']);
				$ax = $a;
				$cx = $c;
				if ($a->isGlobalArea()) {
					$ax = STACKS_AREA_NAME;
					$cx = Stack::getByName($_REQUEST['arHandle']);
				}
				$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
				$p = new Permissions($b);
				if ($p->canEditBlockCustomTemplate()) {

					$bt = BlockType::getByHandle($b->getBlockTypeHandle());
					if (!$bt->includeAll()) {
						// we make sure to create a new version, if necessary
						$nvc = $cx->getVersionToModify();
					} else {
						$nvc = $cx; // keep the same one
					}
					if ($a->isGlobalArea()) {
						$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
						$xvc->relateVersionEdits($nvc);
					}

					$b = Block::getByID($_REQUEST['bID'], $nvc, $ax);
					$ob = $b;
					if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
						$bi = $b->getInstance();
						$ob = Block::getByID($bi->getOriginalBlockID());
						$originalDisplayOrder = $b->getBlockDisplayOrder();
						$ob->setBlockAreaObject($a);
						$nb = $ob->duplicate($nvc);
						$nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
						$b->deleteBlock();
						$b = &$nb;
					} else if ($b->isAlias()) {
						$nb = $ob->duplicate($nvc);
						$b->deleteBlock();
						$b = &$nb;
					}

					$data = $_POST;
					$b->updateBlockInformation($data);
					$b->refreshCacheAll();

					$obj = new stdClass;
					$obj->bID = $b->getBlockID();
					$obj->aID = $a->getAreaID();
					$obj->arHandle= $a->getAreaHandle();
					$obj->error = false;
					$obj->cID = $c->getCollectionID();

					print Loader::helper('json')->encode($obj);
					exit;

					//header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $redirectCID . '&mode=edit' . $step);
					//exit;
				}
				break;
			case 'passthru_stack':
				if (isset($_GET['bID'])) {
					$vn = Loader::helper('validation/numbers');
					if ($vn->integer($_GET['bID'])) {
						$b = Block::getByID($_GET['bID'], Page::getByID($_REQUEST['stackID'], 'ACTIVE'), STACKS_AREA_NAME);
						if (is_object($b)) {
							$p = new Permissions($b);
							if ($p->canViewBlock()) {
								$action = $b->passThruBlock($_REQUEST['method']);
							}
						}
					}
				}
				break;
		}
	}

	if (isset($_GET['atask']) && $_GET['atask'] && $valt->validate()) {
		switch($_GET['atask']) {
			case 'add_stack':
				$a = Area::get($c, $_GET['arHandle']);
				$cx = $c;
				$ax = $a;

				if ($a->isGlobalArea()) {
					$cx = Stack::getByName($_REQUEST['arHandle']);
					$ax = Area::get($cx, STACKS_AREA_NAME);
				}
				$obj = new stdClass;

				$ap = new Permissions($ax);
				$stack = Stack::getByID($_REQUEST['stID']);
				if (is_object($stack)) {
					if ($ap->canAddStackToArea($stack)) {
						// we've already run permissions on the stack at this point, at least for viewing the stack.
						$btx = BlockType::getByHandle(BLOCK_HANDLE_STACK_PROXY);
						$nvc = $cx->getVersionToModify();
						if ($a->isGlobalArea()) {
							$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
							$xvc->relateVersionEdits($nvc);
						}
						$data['stID'] = $stack->getCollectionID();
						$nb = $nvc->addBlock($btx, $ax, $data);

						$obj->aID = $a->getAreaID();
						$obj->arHandle = $a->getAreaHandle();
						$obj->cID = $c->getCollectionID();
						$obj->bID = $nb->getBlockID();
						$obj->error = false;
					} else {
						$obj->error = true;
						$obj->response = array(t('The stack contains invalid block types.'));
					}
				} else {
					$obj->error = true;
					$obj->response = array(t('Invalid stack.'));
				}

				print Loader::helper('json')->encode($obj);
				exit;

				break;
			case 'design':
				$area = Area::get($c, $_GET['arHandle']);
				$ap = new Permissions($area);
				if ($ap->canEditAreaDesign() ) {
					

					$nvc = $c->getVersionToModify();

					if ($_POST['reset_css']) {
						$nvc->resetAreaCustomStyle($area);
					} else {

						if ($_POST['cspID'] > 0 && ($_POST['cspPresetAction'] == 'update_existing_preset')) {
							$csp = CustomStylePreset::getByID($_POST['cspID']);
							$csr = $csp->getCustomStylePresetRuleObject();
							// we update the csr in case anything has been changed
							$csr->update($_POST['css_id'], $_POST['css_class_name'], $_POST['css_custom'], $_POST);
							$nvc->setAreaCustomStyle($area, $csr);
						} else {
							$csr = CustomStyleRule::add($_POST['css_id'], $_POST['css_class_name'], $_POST['css_custom'], $_POST);
							$nvc->setAreaCustomStyle($area, $csr);
						}

						if ($_POST['cspPresetAction'] == 'create_new_preset') {
							CustomStylePreset::add($_POST['cspName'], $csr);
						}
					}

					$cID = $securityHelper->sanitizeInt($_GET['cID']);

					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit' . $step);
					exit;
				}
				break;

		}
	}

	if (isset($_REQUEST['ctask']) && $_REQUEST['ctask'] && $valt->validate()) {

		switch ($_REQUEST['ctask']) {
			case 'remove-alias':
				if ($cp->canDeletePage()) {
					$redir = $c->removeThisAlias();
					if ($_POST['rel'] == 'SITEMAP') {
						$obj = new stdClass;
						$obj->rel = $_REQUEST['rel'];
						$obj->cParentID = $c->getCollectionParentID();
						if ($c->getCollectionPointerOriginalID() != '') {
							$obj->cID = $c->getCollectionPointerOriginalID();
						} else {
							$obj->cID = $c->getCollectionID();
						}
						$obj->display_mode = $_REQUEST['display_mode'];
						$obj->select_mode = $_REQUEST['select_mode'];
						$obj->instance_id = $_REQUEST['instance_id'];
						print Loader::helper('json')->encode($obj);
						exit;
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $redir . $step);
						exit;
					}
				}
				break;
			case 'check-out-add-block':
			case 'check-out':
			case 'check-out-first':
				if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canApprovePageVersions()) {
					// checking out the collection for editing
					$u = new User();
					$u->loadCollectionEdit($c);

					if ($_REQUEST['ctask'] == 'check-out-add-block') {
						setcookie("ccmLoadAddBlockWindow", "1", -1, DIR_REL . '/');
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
						exit;
						break;
					}
				}
				break;

			case 'approve-recent':
				if ($cp->canApprovePageVersions()) {
					$u = new User();
					$pkr = new \Concrete\Core\Workflow\Request\ApprovePageRequest();
					$pkr->setRequestedPage($c);
					$v = CollectionVersion::get($c, "RECENT");
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$u->unloadCollectionEdit($c);
					$response = $pkr->trigger();
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
					exit;
				}
				break;

		}
	}

	if (isset($_REQUEST['ptask']) && $_REQUEST['ptask'] && $valt->validate()) {
		

		// piles !
		switch($_REQUEST['ptask']) {
			case 'delete_content':
				//personal scrapbook
				if ($_REQUEST['pcID'] > 0) {
					$pc = PileContent::get($_REQUEST['pcID']);
					$p = $pc->getPile();
					if ($p->isMyPile()) {
						$pc->delete();
					}
					if ($pcID && ($_REQUEST['sbURL'])) {
						$sbURL = $securityHelper->sanitizeInt($_GET['sbURL']);
						header('Location: ' . BASE_URL . $sbURL);
						exit;
					}
				//global scrapbooks
				}elseif($_REQUEST['bID'] > 0 && $_REQUEST['arHandle']){
					$bID=intval($_REQUEST['bID']);
					$scrapbookHelper=Loader::helper('concrete/scrapbook');
					$globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage();
					$globalScrapbookA = Area::get( $globalScrapbookC, $_REQUEST['arHandle']);
					$block=Block::getById($bID,$globalScrapbookC,$globalScrapbookA);
					if( $block ){  //&& $block->getAreaHandle()=='Global Scrapbook'
						$bp = new Permissions($block);
						if (!$bp->canWrite()) {
							throw new Exception(t('Access to block denied'));
						}else{
							$block->delete(1);
						}
					}
				}
				die;
				break;
		}
	}

	if (isset($_REQUEST['processBlock']) && $_REQUEST['processBlock'] && $valt->validate()) {

		// some admin (or unscrupulous person) is doing something to a block of content on the site
		$edit = ($_REQUEST['enterViewMode']) ? "" : "&mode=edit";

		if ($_POST['update']) {
			// the person is attempting to update some block of content

			$a = Area::get($c, $_GET['arHandle']);
			$ax = $a;
			$cx = $c;
			if ($a->isGlobalArea()) {
				$ax = STACKS_AREA_NAME;
				$cx = Stack::getByName($_REQUEST['arHandle']);
			}
			$b = Block::getByID($_REQUEST['bID'], $cx, $ax);
			$p = new Permissions($b);

			if ($p->canWrite()) {

				$bi = $b->getInstance();
				if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
					$_b = Block::getByID($bi->getOriginalBlockID());
					$bi = $_b->getInstance(); // for validation
				}
				$e = $bi->validate($_POST);
				$obj = new stdClass;
				$obj->aID = $a->getAreaID();
				$obj->arHandle = $a->getAreaHandle();
				$obj->cID = $c->getCollectionID();

				if ((!is_object($e)) || (($e instanceof \Concrete\Core\Error\Error) && (!$e->has()))) {
					$bt = BlockType::getByHandle($b->getBlockTypeHandle());
					if (!$bt->includeAll()) {
						// we make sure to create a new version, if necessary
						$nvc = $cx->getVersionToModify();
					} else {
						$nvc = $cx; // keep the same one
					}

					if ($a->isGlobalArea()) {
						$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
						$xvc->relateVersionEdits($nvc);
					}

					$ob = $b;
					// replace the block with the version of the block in the later version (if applicable)
					$b = Block::getByID($_REQUEST['bID'], $nvc, $ax);

					if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
						// if we're editing a scrapbook display block, we add a new block in this position for the real block type
						// set the block to the display order
						// delete the scrapbook display block, and save the data
						/*
						$originalDisplayOrder = $b->getBlockDisplayOrder();
						$btx = BlockType::getByHandle($_b->getBlockTypeHandle());
						$nb = $nvc->addBlock($btx, $ax, array());
						$nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
						$b->deleteBlock();
						$b = &$nb;
						*/

						$originalDisplayOrder = $b->getBlockDisplayOrder();
						$cnt = $b->getController();
						$ob = Block::getByID($cnt->getOriginalBlockID());
						$ob->loadNewCollection($nvc);
						if (!is_object($ax)) {
							$ax = Area::getOrCreate($cx, $ax);
						}
						$ob->setBlockAreaObject($ax);
						$nb = $ob->duplicate($nvc);
						$nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
						$b->deleteBlock();
						$b = &$nb;

					} else if ($b->isAlias()) {

						// then this means that the block we're updating is an alias. If you update an alias, you're actually going
						// to duplicate the original block, and update the newly created block. If you update an original, your changes
						// propagate to the aliases
						$nb = $ob->duplicate($nvc);
						$b->deleteBlock();
						$b = &$nb;

					}

					// we can update the block that we're submitting
					$b->update($_POST);
					$obj->error = false;
					if (!$obj->cID) {
						$obj->cID = $nvc->getCollectionID();
					}
					$obj->bID = $b->getBlockID();
				} else {
					$obj->error = true;
					$obj->errors = $e->getList();
				}

				print Loader::helper('json')->encode($obj);
				exit;
			}

		} else if ($_REQUEST['add'] || $_REQUEST['_add']) {
			// the persion is attempting to add a block of content of some kind
			$a = Area::get($c, $_REQUEST['arHandle']);
			if (is_object($a)) {
				$ax = $a;
				$cx = $c;
				if ($a->isGlobalArea()) {
					$cx = Stack::getByName($_REQUEST['arHandle']);
					$ax = Area::get($cx, STACKS_AREA_NAME);
				}
				$ap = new Permissions($ax);
				if ($_REQUEST['btask'] == 'alias_existing_block') {
					if (is_array($_REQUEST['pcID'])) {
						

						// we're taking an existing block and aliasing it to here
						foreach($_REQUEST['pcID'] as $pcID) {
							$pc = PileContent::get($pcID);
							$p = $pc->getPile();
							if ($p->isMyPile()) {
								if ($_REQUEST['deletePileContents']) {
									$pc->delete();
								}
							}
							if ($pc->getItemType() == "BLOCK") {
								$bID = $pc->getItemID();
								$b = Block::getByID($bID);
								$b->setBlockAreaObject($ax);
								$bt = BlockType::getByHandle($b->getBlockTypeHandle());
								if ($ap->canAddBlock($bt)) {
									$btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
									$nvc = $cx->getVersionToModify();
									if ($a->isGlobalArea()) {
										$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
										$xvc->relateVersionEdits($nvc);
									}
									$data['bOriginalID'] = $bID;
									$nb = $nvc->addBlock($btx, $ax, $data);
									$nb->refreshCache();
								}
							}
						}
					} else if (isset($_REQUEST['bID'])) {

						$b = Block::getByID($_REQUEST['bID']);
						$b->setBlockAreaObject($ax);
						$bt = BlockType::getByHandle($b->getBlockTypeHandle());
						if ($ap->canAddBlock($bt)) {
							$btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
							$nvc = $cx->getVersionToModify();
							if ($a->isGlobalArea()) {
								$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
								$xvc->relateVersionEdits($nvc);
							}
							$data['bOriginalID'] = $_REQUEST['bID'];
							$nb = $nvc->addBlock($btx, $ax, $data);
							$nb->refreshCache();
						}
					}

					$obj = new stdClass;
					if (is_object($nb)) {
						$obj->aID = $a->getAreaID();
						$obj->arHandle = $a->getAreaHandle();
						$obj->cID = $c->getCollectionID();
						$obj->bID = $nb->getBlockID();
						$obj->error = false;
					} else {
						$e = Loader::helper('validation/error');
						$e->add(t('Invalid block.'));
						$obj->error = true;
						$obj->response = $e->getList();
					}
					print Loader::helper('json')->encode($obj);
					exit;
				} else {
					/*
					$bt = BlockType::getByID($_REQUEST['btID']);
					if ($ap->canAddBlock($bt)) {
						$data = $_POST;
						$data['uID'] = $u->getUserID();

						$class = $bt->getBlockTypeClass();
						$bi = new $class($bt);
						$e = $bi->validate($data);
						$obj = new stdClass;
						$obj->aID = $a->getAreaID();
						$obj->arHandle = $a->getAreaHandle();
						$obj->cID = $c->getCollectionID();

						if ((!is_object($e)) || (($e instanceof ValidationErrorHelper) && (!$e->has()))) {

							if (!$bt->includeAll()) {
								$nvc = $cx->getVersionToModify();
								$nb = $nvc->addBlock($bt, $ax, $data);
							} else {
								// if we apply to all, then we don't worry about a new version of the page
								$nb = $cx->addBlock($bt, $ax, $data);
							}

							if ($a->isGlobalArea() && $nvc instanceof Collection) {
								$xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
								$xvc->relateVersionEdits($nvc);
							}

							$obj->error = false;
							$obj->btID = $nb->getBlockTypeID();
							$obj->bID = $nb->getBlockID();

						} else {

							$obj->error = true;
							$obj->errors = $e->getList();

						}

						print Loader::helper('json')->encode($obj);
						exit;
					}
					*/
				}
			}

		}
	}

	if (isset($_POST['processCollection']) && $_POST['processCollection'] && $valt->validate()) {
		if ($_POST['update_external']) {
			$parent = Page::getByID($c->getCollectionParentID());
			$parentP = new Permissions($parent);
			if ($parentP->canAddExternalLink()) {
				$ncID = $c->updateCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink'], $_POST['cExternalLinkNewWindow']);
				Redirect::to('/dashboard/sitemap');
			}
		} else if ($_POST['add']) {
			// adding a collection to a collection
			

			$ct = PageType::getByID($_POST['ptID']);
			if ($cp->canAddSubpage($ct)) {
				// the $c below identifies that we're adding a collection _to_ that particular collection object
				//$newCollectionID = $ct->addCollection($c);

				$dt = Loader::helper('form/date_time');
				$dh = Loader::helper('date');

				$data = $_POST;
				$data['cvIsApproved'] = 0;
				$data['cDatePublic'] = $dh->getSystemDateTime($dt->translate('cDatePublic'));

				$nc = $c->add($ct, $data);

				if (is_object($nc)) {

					
					$attributes = $ct->getAvailableAttributeKeys();
					if (is_array($attributes)) {
						foreach($attributes as $ak) {
							$ak->saveAttributeForm($nc);
						}
					}


					if ($_POST['rel'] == 'SITEMAP') {
						$u = new User();
						if ($cp->canApprovePageVersions() && SITEMAP_APPROVE_IMMEDIATELY) {
							$pkr = new ApprovePagePageWorkflowRequest();
							$pkr->setRequestedPage($nc);
							$v = CollectionVersion::get($nc, "RECENT");
							$pkr->setRequestedVersionID($v->getVersionID());
							$pkr->setRequesterUserID($u->getUserID());
							$response = $pkr->trigger();
						}
						$u->unloadCollectionEdit();

						if ($_POST['mode'] == 'explore' ) {
							header('Location: ' . BASE_URL . View::url('/dashboard/sitemap/explore', $c->getCollectionID()));
							exit;
						} else if ($_POST['mode'] == 'search') {
							header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $nc->getCollectionID() . '&mode=edit&ctask=check-out-first' . $step . $token);
							exit;
						} else {
							Redirect::to('/dashboard/sitemap');
						}
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $nc->getCollectionID() . '&mode=edit&ctask=check-out-first' . $step . $token);
						exit;
					}
				}
			}
		} else if ($_POST['add_external']) {
			// adding a collection to a collection
			
			if ($cp->canAddExternalLink()) {
				$ncID = $c->addCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink'], $_POST['cExternalLinkNewWindow']);
				Redirect::to('/dashboard/sitemap');
			}
		}
	}

