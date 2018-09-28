<?php
namespace Concrete\Core\Page;

use Concrete\Core\Area\Area;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\UserInfo;

class Exporter implements ItemInterface
{

    /**
     * @param Page $mixed
     * @param \SimpleXMLElement $element
     */
    public function export($mixed, \SimpleXMLElement $element)
    {
        $app = Facade::getFacadeApplication();
        $pageNode = $element;
        $p = $pageNode->addChild('page');
        $p->addAttribute('name', $app->make('helper/text')->entities($mixed->getCollectionName()));
        $p->addAttribute('path', $mixed->getCollectionPath());
        $p->addAttribute('public-date', $mixed->getCollectionDatePUblic());
        $p->addAttribute('filename', $mixed->getCollectionFilename());
        $p->addAttribute('pagetype', $mixed->getPageTypeHandle());
        $template = Template::getByID($mixed->getPageTemplateID());
        if (is_object($template)) {
            $p->addAttribute('template', $template->getPageTemplateHandle());
        }
        $ui = UserInfo::getByID($mixed->getCollectionUserID());
        if (!is_object($ui)) {
            $ui = UserInfo::getByID(USER_SUPER_ID);
        }
        $p->addAttribute('user', $ui->getUserName());
        $p->addAttribute('description', $app->make('helper/text')->entities($mixed->getCollectionDescription()));
        $p->addAttribute('package', $mixed->getPackageHandle());
        if ($mixed->getCollectionParentID() == 0) {
            if ($mixed->getSiteTreeID() == 0) {
                $p->addAttribute('global', 'true');
            } else {
                $p->addAttribute('root', 'true');
            }
        }

        $attribs = $mixed->getSetCollectionAttributes();
        if (count($attribs) > 0) {
            $attributes = $p->addChild('attributes');
            foreach ($attribs as $ak) {
                $av = $mixed->getAttributeValueObject($ak);
                $cnt = $ak->getController();
                $cnt->setAttributeValue($av);
                $akx = $attributes->addChild('attributekey');
                $akx->addAttribute('handle', $ak->getAttributeKeyHandle());
                $cnt->exportValue($akx);
            }
        }

        $db = \Database::connection();
        $r = $db->executeQuery('select arHandle from Areas where cID = ? and arIsGlobal = 0 and arParentID = 0', [$mixed->getCollectionID()]);
        while ($row = $r->FetchRow()) {
            $ax = Area::get($mixed, $row['arHandle']);
            $ax->export($p, $mixed);
        }
    }

}