<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	
	# Filename: _process.php
	# Author: Andrew Embler (andrew@bluepavo.com)
	# -------------------
	# _process.php is included at the top of the dispatcher and basically
	# checks to see if a any submits are taking place. If they are, then
	# _process makes sure that they're handled correctly
	
	//just trying to prevent duplication of this code
	function processMetaData($nvc){			
		/* update meta data */
		Loader::model('collection_attributes');
		Loader::model('collection_types');		
			
		foreach($_POST['selectedAKIDs'] as $akID) {
			if ($akID > 0) {
				$ak = CollectionAttributeKey::getByID($akID);
				$existingOptions = explode("\n", $ak->getCollectionAttributeKeyValues());
				$submittedValue = $ak->getValueFromPost();
				$otherSubmittedVal=trim($_POST['akID_'.$akID.'_other']); 
				
				//Multi Select List
				if( is_array($submittedValue) ){													
					//add new options to list if allowed
					if( $ak->getAllowOtherValues() ){
						$newValues=array();
						foreach($submittedValue as $val)
							if( !in_array($val,$existingOptions) && strlen(trim($val))  ) 
								$newValues[]=$val; 
						$akValues=join("\n",array_merge($existingOptions,$newValues));
						$ak->updateValues($akValues);
					}							
					$submittedValue=join("\n",$submittedValue);
					
				//Single Select - New Value
				}elseif( strlen($otherSubmittedVal) && 
						 $otherSubmittedVal!=CollectionAttributeKey::getNewValueEmptyFieldTxt() && 
						 $ak->getAllowOtherValues() ){
						 
					$submittedValue=$otherSubmittedVal;
					
					//add the new value to possible values
					if( !in_array($otherSubmittedVal,$existingOptions) ){
						$existingOptions[]=$otherSubmittedVal;
						$akValues=join("\n",$existingOptions);
						$ak->updateValues($akValues);
					}
				}
				if (isset($submittedValue)) {
					$nvc->addAttribute($ak, $submittedValue);
				}
			}
		} 
	}	
	
	// Modification for step editing
	$step = ($_REQUEST['step']) ? '&step=' . $_REQUEST['step'] : '';
	
	// if we don't have a valid token we die
	$valt = Loader::helper('validation/token');
	$token = '&' . $valt->getParameter();
	
	// If the user has checked out something for editing, we'll increment the lastedit variable within the database
	$u = new User();
	$u->refreshCollectionEdit($c);
	if ($_REQUEST['btask'] && $valt->validate()) {
	
		// these are tasks dealing with blocks (moving up, down, removing)
		
		switch ($_REQUEST['btask']) {
			case 'ajax_do_arrange': /* called via ajax */
				if ($cp->canWrite()) {
					$nvc = $c->getVersionToModify();
					$nvc->processArrangement($_POST['area']);
				}
				
				exit;
				
				break;
			case 'remove':
				$a = Area::get($c, $_REQUEST['arHandle']);
				if (is_object($a)) {
					$b = Block::getByID($_REQUEST['bID'], $c, $a);
					$p = new Permissions($b); // might be block-level, or it might be area level
					// we're removing a particular block of content
					if ($p->canDeleteBlock()) {
						$nvc = $c->getVersionToModify();
						$b->loadNewCollection($nvc);
						
						$b->deleteBlock();
						$nvc->rescanDisplayOrder($_REQUEST['arHandle']);
						
						if (isset($_POST['isAjax'])) {
							exit;
						}
						
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
						exit;
					}
				}
				break;
			case 'update_groups':
				$a = Area::get($c, $_GET['arHandle']);
				if (is_object($a)) {
					$b = Block::getByID($_GET['bID'], $c, $a);
					$p = new Permissions($b);
					// we're updating the groups for a particular block
					if ($p->canAdminBlock()) {
						$nvc = $c->getVersionToModify();
						$b->loadNewCollection($nvc);
						if ($c->isMasterCollection()) {
							$b->updateBlockGroups(true);
						} else {
							$b->updateBlockGroups();
						}
						
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
						exit;
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
									$nc = Page::getByID($cID);
									$nb = Block::getByID($_GET['bID'], $nc, $a);
									$nb->deleteBlock();
									$nc->rescanDisplayOrder($_REQUEST['arHandle']);								
								}
								
							}
						}
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
						exit;
					}
				}
				break;
			case 'update_information':
				$a = Area::get($c, $_GET['arHandle']);
				$b = Block::getByID($_GET['bID'], $c, $a);
				$p = new Permissions($b);
				// we're updating the groups for a particular block
				if ($p->canAdminBlock()) {
					
					$nvc = $c->getVersionToModify();
					$b->loadNewCollection($nvc);

					$data = $_POST;					
					$b->updateBlockInformation($data);
					
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					exit;
				}
				break;
			case 'passthru':
				if (isset($_GET['bID']) && isset($_GET['arHandle'])) {
					$a = Area::get($c, $_GET['arHandle']);
					$b = Block::getByID($_GET['bID'], $c, $a);
					// basically, we hand off the current request to the block
					// which handles permissions and everything
					$p = new Permissions($b);
					if ($p->canRead()) {
						$action = $b->passThruBlock($_REQUEST['method']);
					}
				}
				break;
		}
	}
	
	if ($_GET['atask'] && $valt->validate()) {
		switch($_GET['atask']) {
			case 'update':
				if ($cp->canAdminPage()) {
					$area = Area::get($c, $_GET['arHandle']);
					if (is_object($area)) {
						if ($_POST['aRevertToPagePermissions']) {
							$area->revertToPagePermissions();		
						} else {
							$area->update($_POST['attribKey'], $_POST['attribValue']);
						}
					}
					
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					exit;
				}				
				break;
		}
	}
	
	if ($_REQUEST['ctask'] && $valt->validate()) {
		
		switch ($_REQUEST['ctask']) {
			case 'delete':
				if ($cp->canDeleteCollection() && $c->getCollectionID != '1' && (!$c->isMasterCollection())) {
					$children = $c->getNumChildren();
					if ($children == 0 || $cp->canAdminPage()) {
						$c->markPendingAction('DELETE');
						if ($cp->canApproveCollection()) {
							$cParentID = $c->getCollectionParentID();
							$c->approvePendingAction();
							header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cParentID . $step);
							exit;
						}
					}
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=mcd' . $step);
					exit;
				}
			case 'clear_pending_action':
				if ($cp->canApproveCollection() || $u->getUserID() == $c->getPendingActionUserID()) {
					$c->clearPendingAction();
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=mcd' . $step);
					exit;
				}
			case 'approve_pending_action':
				if ($cp->canApproveCollection() && $cp->canWrite() && !$isCheckedOut) {
					$approve = false;
					if ($c->isPendingDelete()) {
						$children = $c->getNumChildren();
						if ($children == 0 || $cp->canAdminPage()) {
							$approve = true;
							$cParentID = $c->getCollectionParentID();
						}
					} else {
						$approve = true;
					}
					
					if ($approve) {
						$c->approvePendingAction();
					}
					
					if ($c->isPendingDelete() && $approve) {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cParentID . $step);
						exit;
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
						exit;
					}
				}
				break;
			case 'remove-alias':
				if ($cp->canWrite()) {
					$redir = $c->removeThisAlias();
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $redir . $step);
					exit;
				}
				break;
			case 'check-out':
			case 'check-out-first':
				if ($cp->canWrite()) {
					// checking out the collection for editing
					$u = new User();
					$u->loadCollectionEdit($c);

					if ($_GET['ctask'] == 'check-out-first') {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?ctask=first_run&cID=' . $c->getCollectionID() . $step);
						exit;
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
						exit;
					}
				}
				break;
			case 'check-in':
				if ($cp->canWrite()) {
					// checking out the collection for editing
					$v = CollectionVersion::get($c, "RECENT");
					
					$v->setComment($_REQUEST['comments']);
					if ($_REQUEST['approve'] == 'APPROVE' && $cp->canApproveCollection()) {
						$v->approve();
					} 
					
					if ($_REQUEST['approve'] == 'DISCARD') {
						$v->discard();
					} else {
						$v->removeNewStatus();
					}
					$u = new User();
					$u->unloadCollectionEdit($c);
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . $step);
					exit;
				}
				break;
			case 'approve-recent':
				if ($cp->canApproveCollection()) {
					// checking out the collection for editing
					$v = CollectionVersion::get($c, "RECENT");
					$v->setComment($_REQUEST['comments']);
					$v->approve();
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
				if ($_REQUEST['pcID'] > 0) {
					$pc = PileContent::get($_REQUEST['pcID']);
					$p = $pc->getPile();
					if ($p->isMyPile()) {
						$pc->delete();
					}
					if ($pcID && ($_REQUEST['sbURL'])) {
						header('Location: ' . BASE_URL . $_GET['sbURL']);
						exit;
					}
				}
				break;
		}
	}
	
	if ($_REQUEST['processBlock'] && $valt->validate()) {

		// some admin (or unscrupulous person) is doing something to a block of content on the site
		$edit = ($_REQUEST['enterViewMode']) ? "" : "&mode=edit";
				
		if ($_POST['update']) {
			// the person is attempting to update some block of content

			$a = Area::get($c, $_GET['arHandle']);
			
			$b = Block::getByID($_REQUEST['bID'], $c, $a);
			$p = new Permissions($b);
			if ($p->canWrite()) {
				$bt = BlockType::getByHandle($b->getBlockTypeHandle());
				if (!$bt->includeAll()) {
					// we make sure to create a new version, if necessary				
					$nvc = $c->getVersionToModify();
				} else {
					$nvc = $c; // keep the same one
				}
				$ob = $b;
				// replace the block with the version of the block in the later version (if applicable)
				$b = Block::getByID($_REQUEST['bID'], $nvc, $a);
				
				
				if ($b->isAlias()) {

					// then this means that the block we're updating is an alias. If you update an alias, you're actually going
					// to duplicate the original block, and update the newly created block. If you update an original, your changes
					// propagate to the aliases
					$nb = $ob->duplicate($nvc);
					$b->deleteBlock();
					$b = &$nb;
					
				}
				
				
				
				// we can update the block that we're submitting
				$b->update($_POST);
					
				if (!$_SESSION['disableRedirect']) {
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_REQUEST['cID'] . $edit . $step);
					exit;
				}
			}
						
		} else if ($_REQUEST['add'] || $_REQUEST['_add']) {
			// the persion is attempting to add a block of content of some kind
			$a = Area::get($c, $_REQUEST['arHandle']);
			if (is_object($a)) {
				$ap = new Permissions($a);	
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
								$b->setBlockAreaObject($a);
								$bt = BlockType::getByHandle($b->getBlockTypeHandle());
								if ($ap->canAddBlock($bt)) {
									if (!$bt->includeAll()) {
										$nvc = $c->getVersionToModify();
										$b->alias($nvc);
									} else {
										$b->alias($c);
									}
								}
							}
						}
					} else if (isset($_REQUEST['bID'])) {
						$b = Block::getByID($_REQUEST['bID']); 
						if($_REQUEST['globalBlock'])
							$b->setBlockAreaObject($a);
						$bt = BlockType::getByHandle($b->getBlockTypeHandle());						
						if ($ap->canAddBlock($bt)) {
							if (!$bt->includeAll()) {
								$nvc = $c->getVersionToModify();
								$b->alias($nvc);
							} else {
								$b->alias($c);
							}
						}					
					}
					if ($_REQUEST['isAjax']) {
						exit;
					}
					
					if (!$_SESSION['disableRedirect']) {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_REQUEST['cID'] . $edit . $step);
						exit;				
					}
				} else { 

					$bt = BlockType::getByID($_REQUEST['btID']);			
					if ($ap->canAddBlock($bt)) {
						$data = $_POST;
						$data['uID'] = $u->getUserID();
						if (!$bt->includeAll()) {
							$nvc = $c->getVersionToModify();
							$nb = $nvc->addBlock($bt, $a, $data);
						} else {
							// if we apply to all, then we don't worry about a new version of the page
							$nb = $c->addBlock($bt, $a, $data);
						}
						
					
						if (!$_SESSION['disableRedirect']) {
							header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_REQUEST['cID'] . $edit . $step);
							exit;
						}
					}
				}
			}
		
		}	
	}

	if ($_POST['processCollection'] && $valt->validate()) { 

	
		/*
		if ($_POST['ctask'] == 'copy') {
			if ($cp->canWrite()) {
				if ($_POST['cParentID']) {
					Loader::model('collection_types');
					$ct = CollectionType::getByID($c->getCollectionTypeID());
					$nc = Page::getByID($_REQUEST['cParentID']);
					$ncp = new Permissions($nc);
					
					if ($ncp->canAddSubCollection($ct) && $c->canMoveCopyTo($nc)) {
						if ($_POST['copyAll'] && $ncp->canAdminPage()) {
							$nc2 = $c->duplicateAll($nc); // new collection is passed back
						} else {
							$nc2 = $c->duplicate($nc);
						}
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $nc2->getCollectionID() . '&ctask=mcd' . $step);
						exit;			
					}
				}
			}	
		} else if ($_POST['ctask'] == 'alias') {
			// moving a collection to a new location
			if ($cp->canWrite()) {
				if ($_POST['cParentID']) {
					Loader::model('collection_types');

					$ct = CollectionType::getByID($c->getCollectionTypeID());
					$nc = Page::getByID($_REQUEST['cParentID']);
					$ncp = new Permissions($nc);

					User::unloadCollectionEdit($c);
					if ($ncp->canAddSubCollection($ct) && $c->canMoveCopyTo($nc)) {
						$ncID = $c->addCollectionAlias($nc);						
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $ncID . $step);
						exit;			
					}
				}
			}		
		
		} else if ($_POST['ctask'] == 'move') {
			// moving a collection to a new location
			if ($cp->canWrite()) {
				if ($_POST['cParentID']) {
					Loader::model('collection_types');

					$ct = CollectionType::getByID($c->getCollectionTypeID());
					$nc = Page::getByID($_REQUEST['cParentID']);
					$ncp = new Permissions($nc);
					if ($ncp->canAddSubCollection($ct) && $c->canMoveCopyTo($nc)) {
						$c->markPendingAction('MOVE', $nc);						
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&ctask=mcd' . $step);
						exit;			
					}
				}
			}	
		} else */
		
		if ($_POST['update_theme']) { 
			if ($cp->canAdminPage()) {
				$nvc = $c->getVersionToModify();
				
				$data = array();
				$pl = PageTheme::getByID($_POST['plID']);
				$c->setTheme($pl);
				
				if (!$c->isGeneratedCollection()) {
				
					if ($_POST['ctID']) {
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
				
				if ($_POST['rel'] == 'SITEMAP') { 
					header('Location: ' . URL_SITEMAP);
					exit;
				} else {
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					exit;
				}
				
			}		
		} else if ($_POST['update_metadata']) { 
			// updating a collection
			if ($cp->canWrite()) {
				$nvc = $c->getVersionToModify();
				
				$data = array();
				$data['cName'] = $_POST['cName'];
				$data['cDescription'] = $_POST['cDescription'];
				$data['cHandle'] = $_POST['cHandle'];

				$data['ppURL'] = array();
				foreach ($_POST as $key=>$value) {
					if (strpos($key, 'ppURL-') === 0) {
						$subkey = substr($key, 6);
						$data['ppURL'][$subkey] = $value;
					}
				}
				
				$dt = Loader::helper('form/date_time');
				$data['cDatePublic'] = $dt->translate('cDatePublic');
				if ($cp->canAdminPage()) {
					$data['uID'] = $_POST['uID'];
				}
				
				$nvc->update($data);
				$nvc->clearCollectionAttributes();			
				
				processMetaData($nvc);
				
				if ($_POST['rel'] == 'SITEMAP') { 
					if ($cp->canApproveCollection()) {
						$v = CollectionVersion::get($c, "RECENT");
						$v->approve();
						$u = new User();
						$u->unloadCollectionEdit($c);
					}
					
					header('Location: ' . URL_SITEMAP);
					exit;
				} else {
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					exit;
				}
			}	
		} else if ($_POST['update_external']) {
			if ($cp->canWrite()) {
				$ncID = $c->updateCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink']);						
				header('Location: ' . URL_SITEMAP);
				exit;
			}
		} else if ($_POST['update_permissions']) { 
			// updating a collection
			if ($cp->canAdminPage()) {
				if (PERMISSIONS_MODEL == 'simple') {
					$args['cInheritPermissionsFrom'] = 'OVERRIDE';
					$args['cOverrideTemplatePermissions'] = 1;

					if (is_array($_POST['readGID'])) {
						foreach($_POST['readGID'] as $gID) {
							$args['collectionRead'][] = 'gID:' . $gID;
						}
					}				

					$args['collectionWrite'] = array();
					if (is_array($_POST['editGID'])) {
						foreach($_POST['editGID'] as $gID) {
							$args['collectionReadVersions'][] = 'gID:' . $gID;
							$args['collectionWrite'][] = 'gID:' . $gID;
							$args['collectionAdmin'][] = 'gID:' . $gID;
							$args['collectionDelete'][] = 'gID:' . $gID;
						}
					}				
					$c->updatePermissions($args);
				} else {
					$c->updatePermissions();
				}
				if ($_POST['rel'] == 'SITEMAP') { 
					$u = new User();
					$u->unloadCollectionEdit($c);

					header('Location: ' . URL_SITEMAP);
					exit;
				} else {
					header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $_GET['cID'] . '&mode=edit' . $step);
					exit;
				}
			}	
		} else if ($_POST['add']) { 
			// adding a collection to a collection
			Loader::model('collection_types');

			$ct = CollectionType::getByID($_POST['ctID']);
			if ($cp->canAddSubContent($ct)) {		
				// the $c below identifies that we're adding a collection _to_ that particular collection object
				//$newCollectionID = $ct->addCollection($c);				
				
				$data = $_POST;
				$data['cvIsApproved'] = 0;
				$dt = Loader::helper('form/date_time');
				$data['cDatePublic'] = $dt->translate('cDatePublic');				
				
				$nc = $c->add($ct, $data);
				
				$nvc = $nc->getVersionToModify();
				processMetaData($nvc); 				
				
				if (is_object($nc)) {
					if ($_POST['rel'] == 'SITEMAP') { 
						if ($cp->canApproveCollection()) {
							$v = CollectionVersion::get($nc, "RECENT");
							$v->approve();
							$u = new User();
							$u->unloadCollectionEdit($nc);
						}
						header('Location: ' . URL_SITEMAP);
						exit;
					} else {
						header('Location: ' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $nc->getCollectionID() . '&mode=edit&ctask=check-out-first' . $step . $token);
						exit;
					}
				}
			}
		} else if ($_POST['add_external']) { 
			// adding a collection to a collection
			Loader::model('collection_types');
			if ($cp->canWrite()) {
				$ncID = $c->addCollectionAliasExternal($_POST['cName'], $_POST['cExternalLink']);						
				header('Location: ' . URL_SITEMAP);
				exit;
			}
		}
	}
?>
