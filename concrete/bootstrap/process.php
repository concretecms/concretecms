<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Stack\Pile\PileContent;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;

# Filename: _process.php
# Author: Andrew Embler (andrew@concrete5.org)
# -------------------
# _process.php is included at the top of the dispatcher and basically
# checks to see if a any submits are taking place. If they are, then
# _process makes sure that they're handled correctly

// if we don't have a valid token we die

// ATTENTION! This file is legacy and needs to die. We are moving it's various pieces into
// controllers.

/** @var Concrete\Core\Http\ResponseFactory $this */
/** @var Concrete\Core\Http\Request $request */
/** @var Concrete\Core\Page\Collection\Collection $c */
/** @var Concrete\Core\Permission\Checker $cp */
/** @var Concrete\Core\Application\Application $app */

if (!isset($app)) {
    // Just in case this file is currently included in a custom place (that is, not in ResponseFactory::collection());
    $app = Application::getFacadeApplication();
}
if (!isset($request)) {
    // Just in case this file is currently included in a custom place (that is, not in ResponseFactory::collection());
    $request = $app->make(Request::class);
}

$valt = $app->make('helper/validation/token');

// If the user has checked out something for editing, we'll increment the lastedit variable within the database
$u = new User();
if ($request->getMethod() === 'POST') {
    $u->refreshCollectionEdit($c);
}

$getRequest = function ($key, $defaultValue = null) use ($request) {
    return $request->request->has($key) ? $request->request->get($key) : $request->query->get($key, $defaultValue);
};

if ($request->query->get('atask') && $valt->validate()) {
    switch ($request->query->get('atask')) {
        case 'add_stack':
            $a = Area::get($c, $request->query->get('arHandle'));
            $cx = $c;
            $ax = $a;

            if ($a->isGlobalArea()) {
                $cx = Stack::getByName($getRequest('arHandle'));
                $ax = Area::get($cx, STACKS_AREA_NAME);
            }
            $obj = new stdClass();

            $ap = new Permissions($ax);
            $stack = Stack::getByID($getRequest('stID'));
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

                    $db = null;
                    if ($app->make('helper/validation/numbers')->integer($getRequest('dragAreaBlockID'), 1)) {
                        $db = Block::getByID(
                            $getRequest('dragAreaBlockID'),
                            isset($this->pageToModify) ? $this->pageToModify : null,
                            isset($this->areaToModify) ? $this->areaToModify : null
                        );
                        if (is_object($db) && !$db->isError()) {
                            $nb->moveBlockToDisplayOrderPosition($db);
                        }
                    }
                    if (!is_object($db)) {
                        $nb->moveBlockToDisplayOrderPosition(false);
                    }
                } else {
                    $obj->error = true;
                    $obj->response = array(t('The stack contains invalid block types.'));
                }
            } else {
                $obj->error = true;
                $obj->response = array(t('Invalid stack.'));
            }

            echo json_encode($obj);
            exit;

            break;

    }
}

if ($getRequest('ctask') && $valt->validate()) {
    switch ($getRequest('ctask')) {
        case 'check-out-add-block':
        case 'check-out':
        case 'check-out-first':
            if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canApprovePageVersions()) {
                // checking out the collection for editing
                $u->loadCollectionEdit($c);

                if ($getRequest('ctask') == 'check-out-add-block') {
                    setcookie("ccmLoadAddBlockWindow", "1", -1, DIR_REL . '/');
                    header(
                        'Location: ' . Application::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
                    exit;
                    break;
                }
            }
            break;

        case 'approve-recent':
            if ($cp->canApprovePageVersions()) {
                $pkr = new \Concrete\Core\Workflow\Request\ApprovePageRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, "RECENT");
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $u->unloadCollectionEdit($c);
                $pkr->trigger();
                header(
                    'Location: ' . Application::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
                exit;
            }
            break;

        case 'publish-now':
            if ($cp->canApprovePageVersions()) {
                $v = CollectionVersion::get($c, "SCHEDULED");
                $v->approve(false);

                header('Location: ' . Application::getApplicationURL() . '/' . DISPATCHER_FILENAME .
                    '?cID=' . $c->getCollectionID());

                exit;
            }
            break;
    }
}

if ($getRequest('ptask') && $valt->validate()) {

    // piles !
    switch ($getRequest('ptask')) {
        case 'delete_content':
            //personal scrapbook
            if ($getRequest('pcID') > 0) {
                $pc = PileContent::get($getRequest('pcID'));
                $p = $pc->getPile();
                if ($p->isMyPile()) {
                    $pc->delete();
                }
                //global scrapbooks
            } elseif ($getRequest('bID') > 0 && $getRequest('arHandle')) {
                $bID = (int) $getRequest('bID');
                $scrapbookHelper = $app->make('helper/concrete/scrapbook');
                $globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage();
                $globalScrapbookA = Area::get($globalScrapbookC, $getRequest('arHandle'));
                $block = Block::getById($bID, $globalScrapbookC, $globalScrapbookA);
                if ($block) { //&& $block->getAreaHandle()=='Global Scrapbook'
                    $bp = new Permissions($block);
                    if (!$bp->canWrite()) {
                        throw new Exception(t('Access to block denied'));
                    } else {
                        $block->delete(1);
                    }
                }
            }
            die;
            break;
    }
}

if ($getRequest('processBlock') && $valt->validate()) {
    if ($getRequest('add') || $getRequest('_add')) {
        // the user is attempting to add a block of content of some kind
        $a = Area::get($c, $getRequest('arHandle'));
        if (is_object($a)) {
            $ax = $a;
            $cx = $c;
            if ($a->isGlobalArea()) {
                $cx = Stack::getByName($getRequest('arHandle'));
                $ax = Area::get($cx, STACKS_AREA_NAME);
            }
            $ap = new Permissions($ax);
            if ($getRequest('btask') == 'alias_existing_block') {
                if (is_array($getRequest('pcID'))) {

                    // we're taking an existing block and aliasing it to here
                    foreach ($getRequest('pcID') as $pcID) {
                        $pc = PileContent::get($pcID);
                        $p = $pc->getPile();
                        if ($p->isMyPile()) {
                            if ($getRequest('deletePileContents')) {
                                $pc->delete();
                            }
                        }
                        if ($pc->getItemType() == "BLOCK") {
                            $bID = $pc->getItemID();
                            $b = Block::getByID($bID);
                            $b->setBlockAreaObject($ax);
                            $bt = BlockType::getByHandle($b->getBlockTypeHandle());
                            if ($ap->canAddBlock($bt)) {

                                $nvc = $cx->getVersionToModify();
                                if ($a->isGlobalArea()) {
                                    $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                                    $xvc->relateVersionEdits($nvc);
                                }

                                if (!$bt->isCopiedWhenPropagated()) {
                                    $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);

                                    $data['bOriginalID'] = $bID;
                                    $nb = $nvc->addBlock($btx, $ax, $data);
                                } else {
                                    $nb = $b->duplicate($nvc);
                                    $nb->move($nvc, $ax);
                                }

                                $nb->refreshCache();
                            }
                        }
                    }
                } else {
                    if ($getRequest('bID')) {
                        $b = Block::getByID($getRequest('bID'));
                        $b->setBlockAreaObject($ax);
                        $bt = BlockType::getByHandle($b->getBlockTypeHandle());

                        if ($ap->canAddBlock($bt)) {

                            $nvc = $cx->getVersionToModify();
                            if ($a->isGlobalArea()) {
                                $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                                $xvc->relateVersionEdits($nvc);
                            }

                            if (!$bt->isCopiedWhenPropagated()) {
                                $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
                                $data['bOriginalID'] = $getRequest('bID');
                                $nb = $nvc->addBlock($btx, $ax, $data);
                            } else {
                                $nb = $b->duplicate($nvc);
                                $nb->move($nvc, $ax);
                            }

                            $nb->refreshCache();
                        }
                    }
                }

                $obj = new stdClass();
                if (is_object($nb)) {
                    if ($app->make('helper/validation/numbers')->integer($getRequest('dragAreaBlockID'), 1)) {
                        $db = Block::getByID(
                            $getRequest('dragAreaBlockID'),
                            $this->pageToModify,
                            $this->areaToModify);
                        if (is_object($db) && !$db->isError()) {
                            $nb->moveBlockToDisplayOrderPosition($db);
                        }
                    }
                    if (!is_object($db)) {
                        $nb->moveBlockToDisplayOrderPosition(false);
                    }
                    $nb->refreshCache();

                    $obj->aID = $a->getAreaID();
                    $obj->arHandle = $a->getAreaHandle();
                    $obj->cID = $c->getCollectionID();
                    $obj->bID = $nb->getBlockID();
                    $obj->error = false;
                } else {
                    $e = $app->make('helper/validation/error');
                    $e->add(t('Invalid block.'));
                    $obj->error = true;
                    $obj->response = $e->getList();
                }
                echo json_encode($obj);
                exit;
            }
        }
    }
}
