<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Concrete\Core\Sharing\SocialNetwork\LinkUrlValidator;

class ImportSocialLinksRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'social_links';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->sociallinks)) {
            $validator = app(LinkUrlValidator::class);
            $site = app('site')->getSite();
            foreach ($sx->sociallinks->link as $l) {
                $url = (string) $l['url'];
                if (!$validator->isValid($url)) {
                    continue;
                }
                $sociallink = Link::getByServiceHandle((string) $l['service']);
                if (!is_object($sociallink)) {
                    $sociallink = new \Concrete\Core\Entity\Sharing\SocialNetwork\Link();
                    $sociallink->setURL($url);
                    $sociallink->setSite($site);
                    $sociallink->setServiceHandle((string) $l['service']);
                    $sociallink->save();
                }
            }
        }
    }

}
