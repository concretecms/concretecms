<?
	defined('C5_EXECUTE') or die("Access Denied.");
	# Filename: _process.php
	# Author: Andrew Embler (andrew@bluepavo.com)
	# -------------------
	# _process.php is included at the top of the dispatcher and basically
	# checks to see if a any submits are taking place. If they are, then
	# _process makes sure that they're handled correctly
	
	
	// Modification for step editing
	$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
	
	// if we don't have a valid token we die
	$valt = Loader::helper('validation/token');
	$token = '&' . $valt->getParameter();
	
	// If the user has checked out something for editing, we'll increment the lastedit variable within the database
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$u = new User();
		$u->refreshCollectionEdit($c);
	}

	$securityHelper = Loader::helper('security');

	if ($_REQUEST['btask'] && $valt->validate()) {
	
		// these are tasks dealing with blocks (moving up, down, removing)
		
		switch ($_REQUEST['btask']) {
			case 'ajax_do_arrange': /* called via ajax */
				if ($cp->canEditPageContents()) {
					$nvc = $c->getVersionToModify();
					$doProcessArrangement = true;
					if (PERMISSIONS_MODEL == 'advanced') {
						// first, we check to see if we have permissions to edit the area contents for the source area.
						$arHandle = Area::getAreaHandleFromID($_POST['sourceBlockAreaID']);
						$ar = Area::getOrCreate($nvc, $arHandle);
						$ap = new Permissions($ar);
						if (!$ap->canEditAreaContents()) {
							$r = new stdClass;
							$r->error = true;
							$doProcessArrangement = false;
							$r->message = t('You may not arrange the contents of area %s.', $arHandle);
						} else {
							// now we get further in. We check to see if we're dealing with both a source AND a destination area.
							// if so, we check the area permissions for the destination area.
							if ($_POST['sourceBlockAreaID'] != $_POST['destinationBlockAreaID']) {
								$destAreaHandle = Area::getAreaHandleFromID($_POST['destinationBlockAreaID']);
								$destArea = Area::getOrCreate($nvc, $destAreaHandle);
								$destAP = new Permissions($destArea);
								if (!$destAP->canEditAreaContents()) {
									$r = new stdClass;
									$r->error = true;
									$doProcessArrangement = false;
									$r->message = t('You may not arrange the contents of area %s.', $destAreaHandle);
								} else {
									// we're not done yet. Now we have to check to see whether this user has permission to add
									// a block of this type to the destination area.
									$b = Block::getByID($_REQUEST['sourceBlockID'], $nvc, $arHandle);
									$bt = $b->getBlockTypeObject();
									if (!$destAP->canAddBlock($bt)) {
										$doProcessArrangement = false;
										$r = new stdClass;
										$r->error = true;
										$r->message = t('You may not add %s to area %s.', t($bt->getBlockTypeName()), $destAreaHandle);
									}
								}
							}							
						}

						// now, if we get down here and $doProcessArrangement is still set to true, we perform the arrangement
						// it will be set to true if we're in simple permissions mode, or if we've passed all the checks
					}

					if ($doProcessArrangement) {
						$nvc->processArrangement($_POST['area']);
					}

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
						
						Loader::model('custom_style');						
						
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
			case 'update_composer_settings':
				$a = Area::get($c, $_GET['arHandle']);
				$b = Block::getByID($_GET['bID'], $c, $a);
				$p = new Permissions($b);
				// we're updating the groups for a particular block
				if ($p->canAdminBlock() && $c->isMasterCollection()) {
					
					$nvc = $c->getVersionToModify();
					$b->loadNewCollection($nvc);

					$data = $_POST;					
					$b->updateBlockComposerSettings($data);
					$b->refreshCacheAll();
					
					$obj = new stdClass;
					$obj->bID = $b->getBlockID();
					$obj->aID = $a->getAreaID();
					$obj->arHandle= $a->getAreaHandle();
					$obj->error = false;
					
					print Loader::helper('json')->encode($obj);
					exit;
					
					//header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $redirectCID . '&mode=edit' . $step);
					//exit;
				}
				break;
			case 'passthru':
				if (isset($_GET['bID']) && isset($_GET['arHandle'])) {
					$vn = Loader::helper('validation/numbers');
					if ($vn->integer($_GET['bID'])) {
						$b = Block::getByID($_GET['bID']);
						if (is_object($b)) {
							$a = Area::get($c, $_GET['arHandle']);
							$b = Block::getByID($_GET['bID'], $c, $a);
							// basically, we hand off the current request to the block
							// which handles permissions and everything
							$p = new Permissions($b);
							if ($p->canViewBlock()) {
								if (!is_object($b)) {
									exit;
								}
								$action = $b->passThruBlock($_REQUEST['method']);
							}
						}
					}
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
	
	if ($_GET['atask'] && $valt->validate()) {
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
					Loader::model('custom_style');				
					
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
			case 'layout':
				$area = Area::get($c, $_GET['arHandle']);
				$ap = new Permissions($area);
				if ($ap->canAddLayoutToArea() ) {
					Loader::model('custom_style');				
					
					$nvc = $c->getVersionToModify();
					
					//Loader::model('layout'); 
					$originalLayoutID = intval($_REQUEST['originalLayoutID']);
					$layoutID = intval($_REQUEST['layoutID']); 
					$params = array('type'=>'table',
									'rows'=>intval($_REQUEST['layout_rows']),
									'columns'=>intval($_REQUEST['layout_columns']),  
									'locked'=>intval($_REQUEST['locked']),  
									'spacing'=>intval($_REQUEST['spacing']),  
									'layoutID'=>$layoutID );					
					
					
					//Save Existing layout preset 
					
					$lpID = intval($_REQUEST['lpID']);
					if($lpID && $_POST['layoutPresetAction']=='update_existing_preset'){
						$layoutPreset = LayoutPreset::getByID($lpID);
						if($layoutPreset) $layout = $layoutPreset->getLayoutObject();
						if(!$layout || !intval($layout->layoutID)) throw new Exception(t('Layout preset not found'));
						$layoutID = intval($layout->layoutID);
						if($layoutID!=$originalLayoutID) $updateLayoutId=1;
					} 
					
					//is this an existing layout?  
					if($layoutID){  
						//security check: make sure this layout belongs to this area & collection
						$db = Loader::db();
						$vals = array( $layoutID, $area->getAreaHandle(), intval($nvc->cID), intval($c->getVersionID()), intval($nvc->getVersionID())  ); 
						$layoutExistsInArea = intval($db->getOne('SELECT count(*) FROM CollectionVersionAreaLayouts WHERE layoutID=? AND arHandle=? AND cID=? AND cvID IN (?,?)',$vals))?1:0;
						$validLayout = ($layoutExistsInArea)?1:0;

						if(!$layout) $layout = Layout::getById($layoutID); 
						
						if( is_object($layout) && (in_array($_POST['layoutPresetAction'],array('update_existing_preset','create_new_preset')) || $validLayout) ){ 
							
							$layout->fill( $params );
							
							// if there's no unique layout record for this collection version, and it's not a preset, then treat this as a new record 
							// bypassed if editing a preset or creating a new one 
							if( (!$layoutPreset && !$layout->isUniqueToCollectionVersion($nvc)) || $_POST['layoutPresetAction']=='create_new_preset' || $_POST['layoutPresetAction']=='save_as_custom'){ 
								$updateLayoutId=1;
								$layout->layoutID=0;
							} 
							
							$layout->save( $nvc ); 
							//if($oldLayoutId) $nvc->updateAreaLayoutId($area, $oldLayoutId, $layout->layoutID);  
						}else{
							//this may be triggered when there are two quick requests to the server, and the change has already been saved
							$skipLayoutSave=1;
							//throw new Exception(t('Access Denied: Invalid Layout'));
						}
						
					}else{ //new layout  
						$layout = new Layout( $params );   
						$position = ( $_REQUEST['add_to_position']=='top' ) ? 'top' : 'bottom';  
						$layout->save( $nvc ); 
						//$nvc->addAreaLayout($area, $layout, $position);  
					} 
					
					//are we adding a new layout to an area, or updating an existing one? 
					$cvalID=intval($_REQUEST['cvalID']);
					if($skipLayoutSave){
						//see above
					}elseif( $cvalID ){
						//get the cval of the record that corresponds to this version & area 
						if (isset($_REQUEST['areaNameNumber']) && intval($_REQUEST['areaNameNumber'])){
							//$vals = array( $nvc->getCollectionID(), $nvc->getVersionID(), $_GET['arHandle'], intval($originalLayoutID) );
							//$cvalID = intval($db->getOne('SELECT cvalID FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND arHandle=? AND layoutID=? ',$vals));
							$vals = array( $nvc->getCollectionID(), $nvc->getVersionID(), $_GET['arHandle'], intval($_REQUEST['areaNameNumber']) );
							$cvalID = intval($db->getOne('SELECT cvalID FROM CollectionVersionAreaLayouts WHERE cID=? AND cvID=? AND arHandle=? AND areaNameNumber=? ',$vals));	
						if($updateLayoutId) $nvc->updateAreaLayoutId( $cvalID, $layout->layoutID);  
						}
					}else{  
						$nvc->addAreaLayout($area, $layout, $position);  
					} 					
					
					if ( $_POST['layoutPresetAction']=='create_new_preset' ) { 
						$newPresetName = (strlen($_POST['layoutPresetNameAlt']))?$_POST['layoutPresetNameAlt']:$_POST['layoutPresetName'];
						if(strlen(trim($newPresetName))) LayoutPreset::add(trim($newPresetName), $layout);
					}	

					$cID = $securityHelper->sanitizeInt($_GET['cID']);

					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit' . $step);
					exit;
				}				
				break;					
		}
	}
	
	if ($_REQUEST['ctask'] && $valt->validate()) {
		
		switch ($_REQUEST['ctask']) {
			case 'delete':
				if ($cp->canDeletePage() && $c->getCollectionID() != '1' && (!$c->isMasterCollection())) {
					$children = $c->getNumChildren();
					if ($children == 0 || $u->isSuperUser()) {
						$obj = new stdClass;

						if ($c->isExternalLink()) {
							$c->delete();
						} else { 
							$obj->refreshCID = $c->getCollectionID();
							$pkr = new DeletePagePageWorkflowRequest();
							$pkr->setRequestedPage($c);
							$pkr->setRequesterUserID($u->getUserID());
							$u->unloadCollectionEdit($c);
							$response = $pkr->trigger();
							$obj->deferred = true;
							if ($response instanceof WorkflowProgressResponse) {
								// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
								$obj->refreshCID = $c->getCollectionParentID();
								$obj->deferred = false;
							}
						}
					}
					$cParentID = $c->getCollectionParentID();
					$obj->rel = $_REQUEST['rel'];
					$obj->cParentID = $cParentID;
					$obj->cID = $c->getCollectionID();
					$obj->display_mode = $_REQUEST['display_mode'];
					$obj->select_mode = $_REQUEST['select_mode'];
					$obj->instance_id = $_REQUEST['instance_id'];
					print Loader::helper('json')->encode($obj);
					exit;

				}
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
			case 'check-out':
			case 'check-out-first':
				if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canApprovePageVersions()) {
					// checking out the collection for editing
					$u = new User();
					$u->loadCollectionEdit($c);
					
					/*
					if ($_GET['ctask'] == 'check-out-first') {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?ctask=first_run&cID=' . $c->getCollectionID() . $step);
						exit;
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
						exit;
					}
					*/
				}
				break;
			case 'check-in':
				if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canApprovePageVersions()) {

					$v = CollectionVersion::get($c, "RECENT");
					
					$v->setComment($_REQUEST['comments']);
					if ($_REQUEST['approve'] == 'APPROVE' && $cp->canApprovePageVersions()) {
						$pkr = new ApprovePagePageWorkflowRequest();
						$pkr->setRequestedPage($c);
						$pkr->setRequestedVersionID($v->getVersionID());
						$pkr->setRequesterUserID($u->getUserID());
						$u->unloadCollectionEdit($c);
						$response = $pkr->trigger();
					} 

					if ($_REQUEST['approve'] == 'DISCARD' && $v->canDiscard()) {
						$v->discard();
					} else {
						$v->removeNewStatus();
					}

					$u->unloadCollectionEdit();

					
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
					exit;
				}
				break;
			case 'approve-recent':
				if ($cp->canApprovePageVersions()) {
					$u = new User();
					$pkr = new ApprovePagePageWorkflowRequest();
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
	
	if ($_REQUEST['ptask'] && $valt->validate()) {
		Loader::model('pile');

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
	
	if ($_REQUEST['processBlock'] && $valt->validate()) {
		
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

				if ((!is_object($e)) || (($e instanceof ValidationErrorHelper) && (!$e->has()))) {
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
					$obj->response = $e->getList();
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
						Loader::model('pile');

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
							$obj->bID = $nb->getBlockID();
							
						} else {
							
							$obj->error = true;
							$obj->response = $e->getList();
						
						}

						print Loader::helper('json')->encode($obj);
						exit;
					}
				}
			}
		
		}	
	}

	if ($_POST['processCollection'] && $valt->validate()) { 

		if ($_POST['update_theme']) { 
		
			$pl = false;
			if ($_POST['plID']) { 
				$pl = PageTheme::getByID($_POST['plID']);
			}
			
			if ($cp->canEditPageTheme($pl) || $cp->canEditPageType()) {
				$nvc = $c->getVersionToModify();				
				$data = array();
				if (is_object($pl)) { 
					$nvc->setTheme($pl);
				}

				if (!$c->isGeneratedCollection()) {
				
					if ($_POST['ctID'] && $cp->canEditPageType()) {
						// now we have to check to see if you're allowed to update this page to this page type.
						// We do this by checking to see whether the PARENT page allows you to add this page type here.
						// if this is the home page then we assume you are good
						
						if ($c->getCollectionID() > 1) {
							Loader::model('collection_types');
							$parentC = Page::getByID($c->getCollectionParentID());
							$parentCP = new Permissions($parentC);
							$ct = CollectionType::getByID($_POST['ctID']);
						}
						
						if ($c->getCollectionID() == 1 || $parentCP->canAddSubCollection($ct)) {
							$data['ctID'] = $_POST['ctID'];
							$nvc->update($data);
						}						
					}
				
				}

				if ($_POST['rel'] == 'SITEMAP' && ($cp->canApprovePageVersions())) {
					$pkr = new ApprovePagePageWorkflowRequest();
					$u = new User();
					$pkr->setRequestedPage($c);
					$v = CollectionVersion::get($c, "RECENT");
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$u->unloadCollectionEdit($c);
					$response = $pkr->trigger();
					$obj = new stdClass;
					$obj->rel = $_POST['rel'];
					$obj->name = $v->getVersionName();
					$obj->cID = $c->getCollectionID();
					print Loader::helper('json')->encode($obj);
					exit;
				} else {
					$cID = $securityHelper->sanitizeInt($_GET['cID']);
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&mode=edit' . $step);
					exit;
				}
			}		
		} else if ($_POST['update_speed_settings']) {
			// updating a collection
			if ($cp->canEditPageSpeedSettings()) {
				
				$data = array();
				$data['cCacheFullPageContent'] = $_POST['cCacheFullPageContent'];
				$data['cCacheFullPageContentLifetimeCustom'] = $_POST['cCacheFullPageContentLifetimeCustom'];
				$data['cCacheFullPageContentOverrideLifetime'] = $_POST['cCacheFullPageContentOverrideLifetime'];				

				$c->update($data);
				
				$obj = new stdClass;
				$obj->name = $c->getCollectionName();
				$obj->cID = $c->getCollectionID();
				print Loader::helper('json')->encode($obj);
				exit;
			}	
		} else if ($_POST['update_metadata']) { 
			// updating a collection
			if ($cp->canEditPageProperties()) {
				$nvc = $c->getVersionToModify();
				
				$data = array();
				$pk = PermissionKey::getByHandle('edit_page_properties');
				$pk->setPermissionObject($c);
				$asl = $pk->getMyAssignment();
				if ($asl->allowEditName()) { 
					$data['cName'] = $_POST['cName'];
				}
				if ($asl->allowEditDescription()) { 
					$data['cDescription'] = $_POST['cDescription'];
				}
				if ($asl->allowEditPaths()) { 
					$data['cHandle'] = $_POST['cHandle'];
					$data['ppURL'] = array();
					foreach ($_POST as $key=>$value) {
						if (strpos($key, 'ppURL-') === 0) {
							$subkey = substr($key, 6);
							$data['ppURL'][$subkey] = $value;
						}
					}
				}
				if ($asl->allowEditDateTime()) { 
					$dt = Loader::helper('form/date_time');
					$dh = Loader::helper('date');
					$data['cDatePublic'] = $dh->getSystemDateTime($dt->translate('cDatePublic'));
				}
				if ($asl->allowEditUserID()) { 
					$data['uID'] = $_POST['uID'];
				}
				
				$nvc->update($data);
				
				// First, we check out the attributes we need to clear.
				$setAttribs = $nvc->getSetCollectionAttributes();
				$processedAttributes = array();
				$selectedAKIDs = $_POST['selectedAKIDs'];
				if (!is_array($selectedAKIDs)) {
					$selectedAKIDs = array();					
				}
				foreach($setAttribs as $ak) {
					// do I have the ability to edit this attribute?
					if (in_array($ak->getAttributeKeyID(), $asl->getAttributesAllowedArray())) {
						// Is this item in the selectedAKIDs array? If so then it is being saved
						if (in_array($ak->getAttributeKeyID(), $_POST['selectedAKIDs'])) {
							$ak->saveAttributeForm($nvc);
						} else {
							// it is being removed
							$nvc->clearAttribute($ak);
						}
						$processedAttributes[] = $ak->getAttributeKeyID();
					}					
				}
				$newAttributes = array_diff($selectedAKIDs, $processedAttributes);
				foreach($newAttributes as $akID) {
					if ($akID > 0 && in_array($akID, $asl->getAttributesAllowedArray())) {
						$ak = CollectionAttributeKey::getByID($akID);
						$ak->saveAttributeForm($nvc);
					}
				}

				
				$obj = new stdClass;

				if (($_POST['rel'] == 'SITEMAP' || $_POST['approveImmediately']) && ($cp->canApprovePageVersions())) {
					$pkr = new ApprovePagePageWorkflowRequest();
					$u = new User();
					$pkr->setRequestedPage($c);
					$v = CollectionVersion::get($c, "RECENT");
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$u->unloadCollectionEdit($c);
					$response = $pkr->trigger();
					$obj->rel = $_POST['rel'];
					$obj->name = $v->getVersionName();
				}
				$obj->cID = $c->getCollectionID();
				print Loader::helper('json')->encode($obj);
				exit;
			}	
		} else if ($_POST['update_external']) {
			$parent = Page::getByID($c->getCollectionParentID());
			$parentP = new Permissions($parent);
			if ($parentP->canAddExternalLink()) {
				$ncID = $c->updateCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink'], $_POST['cExternalLinkNewWindow']);						
				header('Location: ' . URL_SITEMAP);
				exit;
			}
		} else if ($_POST['update_permissions']) { 
			// updating a collection
			if ($cp->canEditPagePermissions() && PERMISSIONS_MODEL == 'simple') {

				$obj = new stdClass;
				$c->setPermissionsToManualOverride();

				$pk = PermissionKey::getByHandle('view_page');
				$pk->setPermissionObject($c);
				$pt = $pk->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pk);
				
				if (is_array($_POST['readGID'])) {
					foreach($_POST['readGID'] as $gID) {
						$pa->addListItem(GroupPermissionAccessEntity::getOrCreate(Group::getByID($gID)));
					}
				}				
				$pt->assignPermissionAccess($pa);
				
				$editAccessEntities = array();
				if (is_array($_POST['editGID'])) {
					foreach($_POST['editGID'] as $gID) {
						$editAccessEntities[] = GroupPermissionAccessEntity::getOrCreate(Group::getByID($gID));
					}
				}
				
				$editPermissions = array(
					'view_page_versions',
					'edit_page_properties',
					'edit_page_contents',
					'edit_page_speed_settings',
					'edit_page_theme',
					'edit_page_type',
					'edit_page_permissions',
					'preview_page_as_user',
					'schedule_page_contents_guest_access',
					'delete_page',
					'delete_page_versions',
					'approve_page_versions',
					'add_subpage',
					'move_or_copy_page',
				);
				foreach($editPermissions as $pkHandle) { 
					$pk = PermissionKey::getByHandle($pkHandle);
					$pk->setPermissionObject($c);
					$pt = $pk->getPermissionAssignmentObject();
					$pt->clearPermissionAssignment();
					$pa = PermissionAccess::create($pk);
					foreach($editAccessEntities as $editObj) {
						$pa->addListItem($editObj);
					}
					$pt->assignPermissionAccess($pa);
				}
				
				if ($_POST['rel'] == 'SITEMAP') { 
					$u = new User();
					$u->unloadCollectionEdit();
					$obj->rel = 'SITEMAP';
				} else {
					//header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					//exit;
				}

				$obj->cID = $c->getCollectionID();
				print Loader::helper('json')->encode($obj);
				exit;
			}
		} else if ($_POST['add']) { 
			// adding a collection to a collection
			Loader::model('collection_types');

			$ct = CollectionType::getByID($_POST['ctID']);
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

					Loader::model('collection_attributes');
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
							header('Location: ' . URL_SITEMAP);
							exit;							
						}
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $nc->getCollectionID() . '&mode=edit&ctask=check-out-first' . $step . $token);
						exit;
					}
				}
			}
		} else if ($_POST['add_external']) { 
			// adding a collection to a collection
			Loader::model('collection_types');
			if ($cp->canAddExternalLink()) {
				$ncID = $c->addCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink'], $_POST['cExternalLinkNewWindow']);						
				header('Location: ' . URL_SITEMAP);
				exit;
			}
		}
	}
	
