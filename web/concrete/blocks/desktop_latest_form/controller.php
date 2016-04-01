<?php
namespace Concrete\Block\DesktopLatestForm;

use Package;
use Concrete\Core\Block\BlockController;

    class Controller extends BlockController
    {
        protected $btCacheBlockRecord = true;

        public function getBlockTypeDescription()
        {
            return t("Shows the latest form submission.");
        }

        public function getBlockTypeName()
        {
            return t("Latest Form");
        }

        public function view()
        {
            $db = \Database::connection();
            $r = $db->query('select * from btFormAnswerSet order by created desc limit 1');
            $row = $r->fetch();
print_r($row);
            
            $this->set('link', 'http://www.yahoo.com');
        }


    }
