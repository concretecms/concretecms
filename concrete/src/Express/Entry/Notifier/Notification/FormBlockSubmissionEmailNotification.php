<?php
namespace Concrete\Core\Express\Entry\Notifier\Notification;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class FormBlockSubmissionEmailNotification extends AbstractFormBlockSubmissionNotification
{

    public function notify(Entry $entry, $updateType)
    {
        $config = $this->app->make('config');
        $entityManager = $this->app->make(EntityManager::class);
        $mh = $this->app->make('mail');

        $entity = $entry->getEntity();
        $values = $entity->getAttributeKeyCategory()->getAttributeValues($entry);

        if ($this->blockController->notifyMeOnSubmission) {
            if ($config->get('concrete.email.form_block.address') && strstr($config->get('concrete.email.form_block.address'), '@')) {
                $formFromEmailAddress = $config->get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = $this->app->make(UserInfoRepository::class)->getByID(USER_SUPER_ID);
                $formFromEmailAddress = $adminUserInfo->getUserEmail();
            }

            $replyToEmailAddress = $formFromEmailAddress;
            $email = false;
            if ($this->blockController->replyToEmailControlID) {
                $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                    ->findOneById($this->blockController->replyToEmailControlID);
                if (is_object($control)) {
                    foreach($values as $attribute) {
                        if ($attribute->getAttributeKey()->getAttributeKeyID() == $control->getAttributeKey()->getAttributeKeyID()) {
                            $email = $attribute->getValue();
                        }
                    }

                    if ($email) {
                        $replyToEmailAddress = $email;
                    }
                }
            }

            $formName = $entity->getName();

            $mh->to($this->blockController->recipientEmail);
            $mh->from($formFromEmailAddress);
            $mh->replyto($replyToEmailAddress);
            $mh->addParameter('entity', $entity);
            $mh->addParameter('formName', $formName);
            $mh->addParameter('attributes', $values);
            $mh->load('block_express_form_submission');
            $mh->setSubject(t('Website Form Submission – %s', $formName));
            $mh->sendMail();
        }

    }


}