<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages;
use Concrete\Core\Area\Area;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Feed;
use Concrete\Core\Page\Type\Type;
use Core;

class Feeds extends DashboardPageController
{

    public function view()
    {
        $this->set('feeds', Feed::getList());
    }

    public function feed_updated()
    {
        $this->set('success', t("Feed Updated."));
        $this->view();
    }

    public function feed_deleted()
    {
        $this->set('success', t("Feed Deleted."));
        $this->view();
    }

    public function feed_added()
    {
        $this->set('success', t("Feed Added."));
        $this->view();
    }

    protected function validatePageRequest($token)
    {
        if (!$this->token->validate($token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        $sec = Core::make('helper/security');
        $vs = Core::make('helper/validation/strings');
        $handle = $sec->sanitizeString($this->request->request->get('pfHandle'));
        $title = $sec->sanitizeString($this->request->request->get('pfTitle'));
        $description = $sec->sanitizeString($this->request->request->get('pfDescription'));

        if (!$title) {
            $this->error->add(t('You must specify a valid title.'));
        }
        if (!$description) {
            $this->error->add(t('You must specify a valid description.'));
        }

        if (!$vs->handle($handle)) {
            $this->error->add(t('A valid handle must contain no punctuation or spaces.'));
        }
    }

    protected function loadFeedFromRequest(Feed $pf = null)
    {
        if (!$pf) {
            $pf = new Feed();
        }

        $pf->setTitle($this->post('pfTitle'));
        $pf->setDescription($this->post('pfDescription'));
        $pf->setHandle($this->post('pfHandle'));
        $pf->setPageTypeID($this->post('ptID'));
        $pf->setParentID(intval($this->post('cParentID')));
        $pf->setIncludeAllDescendents($this->post('pfIncludeAllDescendents'));
        $pf->setDisplayAliases($this->post('pfDisplayAliases'));
        $pf->setDisplayFeaturedOnly($this->post('pfDisplayFeaturedOnly'));
        if ($this->post('pfContentToDisplay') == 'A') {
            $pf->displayAreaContent($this->post('pfAreaHandleToDisplay'));
        } else {
            $pf->displayShortDescriptionContent();
        }

        return $pf;
    }

    public function add_feed()
    {
        $this->validatePageRequest('add_feed');
        if (!$this->error->has()) {
            $pf = $this->loadFeedFromRequest();
            $pf->save();
            $this->redirect('/dashboard/pages/feeds', 'feed_added');
        }
        $this->add();
    }

    public function delete_feed()
    {
        $pfID = $this->request->request->get('pfID');
        if (Core::make("helper/validation/numbers")->integer($pfID)) {
            if ($pfID > 0) {
                $feed = Feed::getByID($pfID);
            }
        }

        if (!is_object($feed)) {
            $this->error->add(t('Invalid feed.'));
        }
        if (!$this->token->validate('delete_feed')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $feed->delete();
            $this->redirect('/dashboard/pages/feeds', 'feed_deleted');
        }

        $this->edit($pfID);
    }

    public function edit_feed($pfID = null)
    {
        $this->validatePageRequest('edit_feed');
        $this->edit($pfID);
        $pf = Feed::getByID($pfID);
        if (!$this->error->has()) {
            $pf = $this->loadFeedFromRequest($pf);
            $pf->save();
            $this->redirect('/dashboard/pages/feeds', 'feed_updated');
        }
    }

    public function add()
    {
        $pageTypes = array('' => t('** No Filtering'));
        $types = Type::getList();
        foreach($types as $type) {
            $pageTypes[$type->getPageTypeID()] = $type->getPageTypeDisplayName();
        }
        $this->set('pageTypes', $pageTypes);

        $areas = Area::getHandleList();
        $select = array();
        foreach($areas as $handle) {
            $select[$handle] = $handle;
        }
        $this->set('areas', $select);

    }

    public function edit($pfID = null)
    {
        if (Core::make("helper/validation/numbers")->integer($pfID)) {
            if ($pfID > 0) {
                $feed = Feed::getByID($pfID);
            }
        }

        if (!is_object($feed)) {
            $this->redirect('/dashboard/pages/feeds');
        }
        $this->feed = $feed;

        $this->set('feed', $feed);
        $this->add();
    }

}
