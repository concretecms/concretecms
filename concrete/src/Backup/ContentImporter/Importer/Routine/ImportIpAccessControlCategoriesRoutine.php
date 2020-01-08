<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class ImportIpAccessControlCategoriesRoutine extends AbstractRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'ipaccesscontrolcategories';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::import()
     */
    public function import(SimpleXMLElement $sx)
    {
        if (isset($sx->ipaccesscontrolcategories) && isset($sx->ipaccesscontrolcategories->ipaccesscontrolcategory)) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(IpAccessControlCategory::class);
            $categories = [];
            foreach ($sx->ipaccesscontrolcategories->ipaccesscontrolcategory as $xIpAccessControlCategory) {
                $handle = (string) $xIpAccessControlCategory['handle'];
                if ($handle !== '' && $repo->findOneBy(['handle' => $handle]) === null) {
                    $enabled = (string) $xIpAccessControlCategory['enabled'];
                    $category = new IpAccessControlCategory();
                    $category
                        ->setHandle($handle)
                        ->setName($xIpAccessControlCategory['name'])
                        ->setEnabled($enabled === '' ? true : $enabled)
                        ->setMaxEvents($xIpAccessControlCategory['max-events'])
                        ->setTimeWindow($xIpAccessControlCategory['time-window'])
                        ->setBanDuration($xIpAccessControlCategory['ban-duration'])
                        ->setSiteSpecific($xIpAccessControlCategory['site-specific'])
                        ->setPackage(static::getPackageObject($xIpAccessControlCategory['package']))
                    ;
                    $logChannelHandle = (string) $xIpAccessControlCategory['logChannelHandle'];
                    if ($logChannelHandle !== '') {
                        $category->setLogChannelHandle($logChannelHandle);
                    }
                    $em->persist($category);
                    $categories[] = $category;
                }
            }
            if (!empty($categories)) {
                $em->flush($categories);
            }
        }
    }
}
