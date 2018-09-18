<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class FormBlockSubmissionEmailNotification extends AbstractFormBlockSubmissionNotification
{

    protected $from;
    protected $replyTo;
    protected $attributeValues;

    protected function getFromEmail()
    {
        if (!isset($this->from)) {
            $config = $this->app->make('config');
            if ($config->get('concrete.email.form_block.address') && strstr($config->get('concrete.email.form_block.address'), '@')) {
                $this->from = $config->get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = $this->app->make(UserInfoRepository::class)->getByID(USER_SUPER_ID);
                $this->from = $adminUserInfo->getUserEmail();
            }
        }
        return $this->from;
    }

    protected function getReplyToEmail(Entry $entry)
    {
        $entityManager = $this->app->make(EntityManager::class);
        $replyToEmailAddress = $this->getFromEmail();
        $email = false;
        if ($this->blockController->replyToEmailControlID) {
            $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                ->findOneById($this->blockController->replyToEmailControlID);
            if (is_object($control)) {
                foreach($this->getAttributeValues($entry) as $attribute) {
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

    protected function getFormName(Entry $entry)
    {
        $entity = $entry->getEntity();
        $formName = $entity->getName();
        return $formName;
    }

    public function notify(Entry $entry, $updateType)
    {
        if ($this->blockController->notifyMeOnSubmission) {
            $mh = $this->app->make('mail');
            $mh->to($this->getToEmail());
            $mh->from($this->getFromEmail());
            $mh->replyto($this->getReplyToEmail($entry));
            $mh->addParameter('entity', $entry->getEntity());
            $mh->addParameter('formName', $this->getFormName($entry));
            $mh->addParameter('attributes', $this->getAttributeValues($entry));
            $mh->load('block_express_form_submission');
            if (empty($mh->getSubject())) {
                $mh->setSubject(t('Website Form Submission – %s', $this->getFormName($entry)));
            }
            $mh->sendMail();
        }

    }


}