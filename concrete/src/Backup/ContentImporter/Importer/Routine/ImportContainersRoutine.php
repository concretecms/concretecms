<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportContainersRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'containers';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $em = \Database::connection()->getEntityManager();
        if (isset($sx->containers)) {
            foreach ($sx->containers->container as $pt) {
                $pkg = static::getPackageObject($pt['package']);
                $name = (string) $pt['name'];
                $icon = (string) $pt['icon'];
                $handle = (string) $pt['handle'];
                $container = $em->getRepository(Container::class)->findOneByContainerHandle($handle);
                if (!$container) {
                    $container = new Container();
                    $container->setContainerIcon($icon);
                    $container->setContainerHandle($handle);
                    $container->setContainerName($name);
                    if ($pkg) {
                        $container->setPackage($pkg);
                    }
                    $em->persist($container);
                    $em->flush();
                }
            }
        }
    }
}
