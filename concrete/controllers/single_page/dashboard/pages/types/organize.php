<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Types;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Type\Type;

class Organize extends DashboardPageController
{
    public function view($typeID = null)
    {
        $siteType = false;
        if ($typeID) {
            $siteType = $this->app->make('site/type')->getByID($typeID);
        }
        if (!$siteType) {
            $siteType = $this->app->make('site/type')->getDefault();
        } else {
            $this->set('siteTypeID', $siteType->getSiteTypeID());
        }
        $this->set('frequent', Type::getFrequentlyUsedList($siteType));
        $this->set('infrequent', Type::getInfrequentlyUsedList($siteType));
    }

    public function submit()
    {
        if ($this->token->validate('submit')) {
            $displayOrder = 0;
            if (is_array($this->post('frequent'))) {
                foreach ($this->post('frequent') as $ptID) {
                    $pt = Type::getByID($ptID);
                    if (is_object($pt)) {
                        $data = array('ptIsFrequentlyAdded' => 1, 'ptDisplayOrder' => $displayOrder);
                        $pt->update($data);
                        ++$displayOrder;
                    }
                }
            }
            if (is_array($this->post('infrequent'))) {
                foreach ($this->post('infrequent') as $ptID) {
                    $pt = Type::getByID($ptID);
                    if (is_object($pt)) {
                        $data = array('ptIsFrequentlyAdded' => 0, 'ptDisplayOrder' => $displayOrder);
                        $pt->update($data);
                        ++$displayOrder;
                    }
                }
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }

        $er = new \Concrete\Core\Application\EditResponse($this->error);
        $er->setMessage(t('Order Saved.'));
        $er->outputJSON();
    }
}
