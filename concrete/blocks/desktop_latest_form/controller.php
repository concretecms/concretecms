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

            $legacyDateCreated = strtotime($row['created']);

            $entityManager = $db->getEntityManager();
            $forms = $entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
                ->findExpressForms();

            $ids = array();
            foreach($forms as $form) {
                $ids[] = $form->getID();
            }

            if (count($ids)) {
                $q = $entityManager->createQuery(
                    'select e from Concrete\Core\Entity\Express\Entry e where e.entity in (:entities) order by e.exEntryDateCreated desc'
                );
                $q->setParameter('entities', $forms);
                $q->setMaxResults(1);
                $result = $q->getOneOrNullResult();
            } else {
                $result = null;
            }

            $formDateCreated = 0;
            if (is_object($result)) {
                $formDateCreated = \Core::make('date')->toDateTime($result->getDateCreated())->getTimestamp();
            }

            if ($legacyDateCreated > $formDateCreated) {
                if (is_array($row) && isset($row['questionSetId'])) {
                    $r = $db->query('select * from btForm where questionSetId = ?', array($row['questionSetId']));
                    $row2 = $r->fetch();
                    if ($row2['bID']) {
                        $this->set('formName', $row2['surveyName']);
                        $this->set('date', \Core::make('date')->formatDateTime(strtotime($row['created'])));
                        $this->set('link', \URL::to('/dashboard/reports/forms/legacy') . '?qsid=' . $row['questionSetId']);
                    }
                }
            } else if (is_object($result)) {
                $entity = $result->getEntity();
                $this->set('formName', $entity->getName());
                $this->set('date', \Core::make('date')->formatDateTime($result->getDateCreated()));
                $this->set('link', \URL::to('/dashboard/reports/forms/view_entry', $result->getID()));
            }

        }


    }
