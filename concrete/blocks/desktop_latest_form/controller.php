<?php

namespace Concrete\Block\DesktopLatestForm;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Shows the latest form submission.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Latest Form');
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [Features::DESKTOP];
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return void
     */
    public function view()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $r = $db->executeQuery('select * from btFormAnswerSet order by created desc limit 1');
        $row = $r->fetchAssociative();

        $legacyDateCreated = $row === false ? null : strtotime($row['created']);
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = app(EntityManagerInterface::class);
        $forms = $entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findExpressForms()
        ;

        $ids = [];
        foreach ($forms as $form) {
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
            $formDateCreated = app('date')->toDateTime($result->getDateCreated())->getTimestamp();
        }

        if ($legacyDateCreated !== null && $legacyDateCreated > $formDateCreated) {
            if (is_array($row) && isset($row['questionSetId'])) {
                $r = $db->executeQuery('select * from btForm where questionSetId = ?', [$row['questionSetId']]);
                $row2 = $r->fetchAssociative();
                if (is_array($row2) && isset($row2['bID'])) {
                    $this->set('formName', $row2['surveyName']);
                    $this->set('date', app('date')->formatDateTime(strtotime($row['created'])));
                    $this->set('link', app(ResolverManagerInterface::class)->resolve(['/dashboard/reports/forms/legacy']) . '?qsid=' . $row['questionSetId']);
                }
            }
        } elseif (is_object($result)) {
            $entity = $result->getEntity();
            $this->set('formName', $entity->getEntityDisplayName());
            $this->set('date', app('date')->formatDateTime($result->getDateCreated()));
            $this->set('link', app(ResolverManagerInterface::class)->resolve(['/dashboard/reports/forms/view_entry', $result->getID()]));
        }
    }
}
