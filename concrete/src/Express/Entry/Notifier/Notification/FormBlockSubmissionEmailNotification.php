<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class FormBlockSubmissionEmailNotification extends AbstractFormBlockSubmissionNotification
{
    protected $from;
    protected $replyTo;
    protected $attributeValues;

    protected function getFromEmail(Entry $entry = null)
    {
        if (!isset($this->from)) {
            $config = $this->app->make('config');
            if ($entry && $config->get('concrete.email.form_block.'. $entry->getEntity()->getHandle() .'.address') && strstr($config->get('concrete.email.form_block.'. $entry->getEntity()->getHandle() .'.address'), '@')) {
                $this->from = $config->get('concrete.email.form_block.'. $entry->getEntity()->getHandle() .'.address');
            }
            else{
                if ($config->get('concrete.email.form_block.address') && strstr($config->get('concrete.email.form_block.address'), '@')) {
                $this->from = $config->get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = $this->app->make(UserInfoRepository::class)->getByID(USER_SUPER_ID);
                $this->from = $adminUserInfo->getUserEmail();
            }
            }
            
        }

        return $this->from;
    }

    protected function getReplyToEmail(Entry $entry)
    {
        $entityManager = $this->app->make(EntityManager::class);
        $replyToEmailAddress = $this->getFromEmail($entry);
        $email = false;
        if ($this->blockController->replyToEmailControlID) {
            $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                ->findOneById($this->blockController->replyToEmailControlID);
            if (is_object($control)) {
                foreach ($this->getAttributeValues($entry) as $attribute) {
                    if ($attribute->getAttributeKey()->getAttributeKeyID() == $control->getAttributeKey()->getAttributeKeyID()) {
                        $email = $attribute->getValue();
                    }
                }
                if ($email) {
                    $replyToEmailAddress = $email;
                }
            }
        }

        return $replyToEmailAddress;
    }

    protected function getToEmail()
    {
        return $this->blockController->recipientEmail;
    }

    protected function getAttributeValues(Entry $entry)
    {
        $entity = $entry->getEntity();
        if (!isset($this->attributeValues)) {
            $this->attributeValues = $entity->getAttributeKeyCategory()->getAttributeValues($entry);
        }

        return $this->attributeValues;
    }

    protected function getAssociations(Entry $entry)
    {
        $associations = [];
        // Have to do this so Doctrine gets converted to an object properly.
        foreach ($entry->getAssociations() as $association) {
            $associations[] = $association;
        }
        return $associations;
    }

    /**
     * @param ExpressValue[] $attributeValues
     */
    public function setAttributeValues($attributeValues)
    {
        $this->attributeValues = $attributeValues;
    }

    protected function getFormName(Entry $entry)
    {
        $entity = $entry->getEntity();
        $formName = $entity->getName();

        return $formName;
    }

    public function notify(Entry $entry, $updateType)
    {
        $files = [];
        if ($this->blockController->notifyMeOnSubmission) {
            $mh = $this->app->make('mail');
            $mh->to($this->getToEmail());
            $mh->from($this->getFromEmail($entry));
            $mh->replyto($this->getReplyToEmail($entry));
            $mh->addParameter('entity', $entry->getEntity());
            $mh->addParameter('formName', $this->getFormName($entry));
            $mh->addParameter("dataSaveEnabled", $this->blockController->storeFormSubmission);
            if (!$this->blockController->storeFormSubmission) {
                //if save submitted data is not active we send also files as attachments in email becuase it will be removed after entry remove
                foreach ($this->getAttributeValues($entry) as $attributeValue) {
                    if ($attributeValue->getAttributeTypeObject()->getAttributeTypeHandle() == "image_file") {
                        $file = $attributeValue->getValue();
                        if ($file) {
                            $files[] = $file;
                            $mh->addAttachment($file);
                        }
                    }
                }
            }
            $mh->addParameter('attributes', $this->getAttributeValues($entry));
            $mh->addParameter('associations', $this->getAssociations($entry));
            $mh->load('block_express_form_submission');
            if (empty($mh->getSubject())) {
                $mh->setSubject(t('Website Form Submission – %s', $this->getFormName($entry)));
            }
            $mh->sendMail();
            //we have to delete the files as they are created automatically and are not attached to the entry save
            if (!$this->blockController->storeFormSubmission) {
                foreach ($files as $file) {
                    $file->delete();
                }
            }
        }
    }
}
