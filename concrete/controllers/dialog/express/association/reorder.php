<?php
namespace Concrete\Controller\Dialog\Express\Association;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Express\Form\Control\SaveHandler\ManySaveHandlerInterface;

class Reorder extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/express/association/reorder';

    protected function canAccess()
    {
        $entry = $this->getEntry();
        if ($entry) {
            $ep = new \Permissions($entry);
            return $ep->canViewExpressEntries();
        }
        return false;
    }

    protected function getEntry()
    {
        if ($this->request->request->has('entryID')) {
            $entryID = $this->request->request->get('entryID');
        } else {
            $entryID = $this->request->query->get('entryID');
        }
        $em = \Database::connection()->getEntityManager();
        return $em->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($entryID);
    }

    protected function getControl()
    {
        if ($this->request->request->has('controlID')) {
            $controlID = $this->request->request->get('controlID');
        } else {
            $controlID = $this->request->query->get('controlID');
        }
        $em = \Database::connection()->getEntityManager();
        return $em->getRepository('Concrete\Core\Entity\Express\Control\AssociationControl')
            ->findOneById($controlID);
    }


    public function view()
    {
        $entry = $this->getEntry();
        $control = $this->getControl();
        if ($association = $control->getAssociation()) {
            $related = $entry->getAssociations();
            foreach ($related as $relatedAssociation) {
                if ($relatedAssociation->getAssociation()->getID() == $association->getID()) {
                    $this->set('selectedEntries', $relatedAssociation->getSelectedEntries());
                    $this->set('control', $control);
                    $this->set('entry', $entry);
                    $this->set('formatter', $association->getFormatter());
                }
            }
        }
    }

    public function submit()
    {
        $em = \Database::connection()->getEntityManager();
        $selectedEntry = $this->getEntry();
        $control = $this->getControl();
        if ($association = $control->getAssociation()) {
            $i = 0;
            $handler = $association->getSaveHandler();
            /**
             * @var $handler ManySaveHandlerInterface
             */
            $supportsCustomDisplayOrder = false;
            if ($association->getTargetEntity()->supportsCustomDisplayOrder()) {
                $supportsCustomDisplayOrder = true;
            }

            $associatedEntries = $handler->getAssociatedEntriesFromRequest($control, $this->request);
            foreach($associatedEntries as $entry) {
                if ($supportsCustomDisplayOrder) {
                    $entry->setEntryDisplayOrder($i);
                    $em->persist($entry);
                } else {
                    $entryAssociation = $selectedEntry->getAssociation($association);
                    $associationEntry = $entryAssociation->getAssociationEntry($entry);
                    $associationEntry->setDisplayOrder($i);
                    $em->persist($associationEntry);
                }
                $i++;
            }

            $em->flush();
            $this->flash('success', t('Display order saved successfully.'));
            $response = new EditResponse();
            $response->setRedirectURL(\URL::to('/dashboard/express/entries/', 'view_entry', $selectedEntry->getId()));
            $response->outputJSON();
        }
    }

}
