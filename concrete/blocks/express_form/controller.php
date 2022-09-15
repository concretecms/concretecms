<?php

namespace Concrete\Block\ExpressForm;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\Context\AttributeTypeSettingsContext;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator;
use Concrete\Core\Express\Controller\ControllerInterface;
use Concrete\Core\Express\Entry\Notifier\Notification\FormBlockSubmissionEmailNotification;
use Concrete\Core\Express\Entry\Notifier\Notification\FormBlockSubmissionNotification;
use Concrete\Core\Express\Entry\Notifier\NotificationProviderInterface;
use Concrete\Core\Express\Form\Context\FrontendFormContext;
use Concrete\Core\Express\Form\Control\Type\EntityPropertyType;
use Concrete\Core\Express\Form\Processor\ProcessorInterface;
use Concrete\Core\Express\Form\Validator\Routine\CaptchaRoutine;
use Concrete\Core\Express\Generator\EntityHandleGenerator;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\FileProviderInterface;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Form\Context\ContextFactory;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Support\Facade\Express;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Validator\String\EmailValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Permission\Checker;

class Controller extends BlockController implements NotificationProviderInterface, UsesFeatureInterface
{
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 700;
    protected $btCacheBlockOutput = false;
    protected $btTable = 'btExpressForm';
    protected $btExportPageColumns = ['redirectCID'];
    protected $btCopyWhenPropagate = true;

    public $notifyMeOnSubmission;
    public $recipientEmail;
    public $replyToEmailControlID;
    public $storeFormSubmission = 1;
    public $exFormID;
    public $addFilesToFolder;

    const FORM_RESULTS_CATEGORY_NAME = 'Forms';

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Build simple forms and surveys.');
    }

    public function getBlockTypeName()
    {
        return t('Form');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::FORMS
        ];
    }

    public function action_add_new_form()
    {
        $token = app('token');
        if ($token->validate('add_new_form')) {
            $session = $this->app->make('session');
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $formName = $this->request->request->get('formName') ?? null;
            if ($formName) {
                $existingEntity = $entityManager->getRepository(Entity::class)
                    ->findOneByName($formName);
                if ($existingEntity) {
                    throw new UserMessageException(
                        t('An entity with the name "%s" already exists. Please choose another', h($formName))
                    );
                }
            } else {
                throw new UserMessageException(t('You must specify a valid name for your form.'));
            }

            $node = ExpressEntryCategory::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
            $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add($formName, $node);

            $entity = new Entity();
            $entity->setName($formName);
            $entity->setIncludeInPublicList(false);
            $entity->setIsPublished(false);
            $generator = new EntityHandleGenerator($entityManager);
            $entity->setHandle($generator->generate($entity));
            $entity->setEntityResultsNodeId($node->getTreeNodeID());
            $entityManager->persist($entity);
            $entityManager->flush();

            $form = new Form();
            $form->setName(t('Form'));
            $form->setEntity($entity);
            $entity->setDefaultViewForm($form);
            $entity->setDefaultEditForm($form);
            $entityManager->persist($form);
            $entityManager->flush();

            // Create a Field Set and a Form
            $fieldSet = new FieldSet();
            $fieldSet->setForm($form);
            $entityManager->persist($fieldSet);
            $entityManager->flush();

            $session->set('block.express_form.new', $form->getId());

            return new JsonResponse($form);
        } else {
            throw new \Exception($token->getErrorMessage());
        }
    }

    public function action_update_control_order()
    {
        $token = app('token');
        if ($token->validate('update_control_order')) {
            $fieldSet = $this->getFormFieldSet();
            $controlIds = $this->request->request->get('controls') ?? [];
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $position = 0;
            foreach ($controlIds as $controlId) {
                $control = $entityManager->find(Control::class, $controlId);
                if ($control && $control->getFieldSet()->getId() === $fieldSet->getId()) {
                    $control->setPosition($position);
                    $entityManager->persist($control);
                }
                $position++;
            }
            $entityManager->flush();
            $entityManager->refresh($fieldSet);
            return new JsonResponse($fieldSet);
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
    }

    public function action_delete_control()
    {
        $token = app('token');
        if ($token->validate('delete_control')) {
            $fieldSet = $this->getFormFieldSet();
            $controlId = $this->request->request->get('control') ?? null;
            $entityManager = $this->app->make(EntityManagerInterface::class);

            if ($controlId) {
                $control = $entityManager->find(Control::class, $controlId);
                if ($control && $control->getFieldSet()->getId() === $fieldSet->getId()) {
                    if ($control instanceof AttributeKeyControl) {
                        $key = $control->getAttributeKey();
                        $entityManager->remove($key);
                    }
                    $entityManager->remove($control);
                }
            }
            $entityManager->flush();
            $entityManager->refresh($fieldSet);
            return new JsonResponse($fieldSet);
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
    }

    protected function getFormFieldSet(): FieldSet
    {
        $session = $this->app->make('session');
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $formId = null;
        if (isset($this->exFormID)) {
            $formId = $this->exFormID;
        } else {
            $formId = $session->get('block.express_form.new');
        }

        if ($formId) {
            $form = $entityManager->find(Form::class, $formId);
            if ($form) {
                $fieldSet = $form->getFieldSets()[0];
                if ($fieldSet) {
                    return $fieldSet;
                }
            }
        }

        throw new UserMessageException(t('Unable to retrieve active field set from session.'));
    }

    public function action_add_control()
    {
        $token = app('token');
        if ($token->validate('add_control')) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $post = $this->request->request->all();
            $fieldSet = $this->getFormFieldSet();
            $entity = $fieldSet->getForm()->getEntity();

            $attributeKeyCategory = $entity->getAttributeKeyCategory();
            $attributeKeyHandleGenerator = new AttributeKeyHandleGenerator($attributeKeyCategory);

            $field = explode('|', $this->request->request->get('type'));
            switch ($field[0]) {
                case 'attribute_key':
                    $type = Type::getByID($field[1]);
                    if (is_object($type)) {
                        $control = new AttributeKeyControl();
                        $control->setId((new UuidGenerator())->generate($entityManager, $control));
                        $key = new ExpressKey();
                        $key->setAttributeKeyName($post['question']);
                        $key->setAttributeKeyHandle($attributeKeyHandleGenerator->generate($key));
                        $key->setEntity($entity);
                        if ($post['required']) {
                            $control->setIsRequired(true);
                        }
                        $key->setAttributeType($type);
                        if (!$post['question']) {
                            $e = $this->app->make('error');
                            $e->add(t('You must give this question a name.'));

                            return new JsonResponse($e);
                        }
                        $controller = $type->getController();
                        $key = $this->saveAttributeKeySettings($controller, $key, $post);
                        $entityManager->persist($key);
                        $entityManager->flush();
                        $control->setAttributeKey($key);
                    }
                    break;
                case 'entity_property':
                    /** @var EntityPropertyType $propertyType */
                    $propertyType = $this->app->make(EntityPropertyType::class);

                    $control = $propertyType->createControlByIdentifier($field[1]);
                    $control->setId((new UuidGenerator())->generate($entityManager, $control));
                    $saver = $control->getControlSaveHandler();
                    $control = $saver->saveFromRequest($control, $this->request);
                    break;
            }

            if (!isset($control)) {
                $e = $this->app->make('error');
                $e->add(t('You must choose a valid field type.'));

                return new JsonResponse($e);
            } else {
                $totalControls = $fieldSet->getControls()->count();
                $control->setFieldSet($fieldSet);
                $control->setPosition(
                    $totalControls
                ); // If there are 2 controls, they have are zero-indexed, so the new one has to be at 2.
                $entityManager->persist($control);
                $entityManager->flush();

                return new JsonResponse($control);
            }
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
    }

    public function add()
    {
        $this->set('formName', $this->request->getCurrentPage()->getCollectionName());
        $this->set('submitLabel', t('Submit'));
        $this->set('thankyouMsg', t('Thanks!'));
        $this->edit();
        $this->set('resultsFolder', $this->get('formResultsRootFolderNodeID'));
        $this->set('addFilesToFolder', (new Filesystem())->getRootFolder());
        $this->set('storeFormSubmission', $this->areFormSubmissionsStored());
        $this->set('formSubmissionConfig', $this->getFormSubmissionConfigValue());
        $this->set('displayCaptcha', 1);
        $this->set('exFormID', null);
        $this->set('redirectCID', null);
        $this->set('notifyMeOnSubmission', false);
        $this->set('recipientEmail', '');
        $this->set('addFilesToSet', false);
        $this->set('replyToEmailControlID', null);
    }

    public function action_update_control()
    {
        $token = app('token');
        if ($token->validate('update_control')) {
            $fieldSet = $this->getFormFieldSet();
            $post = $this->request->request->all();
            $controlId = $post['id'] ?? null;
            $entityManager = $this->app->make(EntityManagerInterface::class);

            if ($controlId) {
                $control = $entityManager->find(Control::class, $controlId);
                if ($control && $control->getFieldSet()->getId() === $fieldSet->getId()) {
                    if ($control instanceof AttributeKeyControl) {
                        $key = $control->getAttributeKey();
                        $key->setAttributeKeyName($post['question']);

                        if (!$post['question']) {
                            $e = $this->app->make('error');
                            $e->add(t('You must give this question a name.'));
                            return new JsonResponse($e);
                        }

                        if ($post['requiredEdit']) {
                            $control->setIsRequired(true);
                        } else {
                            $control->setIsRequired(false);
                        }

                        $controller = $key->getController();
                        $key = $this->saveAttributeKeySettings($controller, $key, $post);
                        $entityManager->persist($key);
                        $entityManager->flush();
                        $control->setAttributeKey($key);
                        $control->setAttributeKey($key);
                    } else {
                        if ($control instanceof TextControl) {
                            /** @var EntityPropertyType $propertyType */
                            $type = $this->app->make(EntityPropertyType::class);

                            $saver = $control->getControlSaveHandler();
                            $control = $saver->saveFromRequest($control, $this->request);
                        }
                    }

                    $entityManager->persist($control);
                    $entityManager->flush();
                    return new JsonResponse($control);
                }
            }
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
    }

    public function save($data)
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);

        // Make sure our data goes through correctly.
        $data['storeFormSubmission'] = isset($data['storeFormSubmission']) ?: 0;

        // Now, let's handle saving the form entity ID against the form block db record
        $entity = false;

        // Add mode
        if (isset($data['exFormID']) && $data['exFormID'] !== '') {
            return parent::save($data);
        } else {
            $fieldSet = $this->getFormFieldSet();
            if ($fieldSet) {
                $form = $fieldSet->getForm();
                $entity = $form->getEntity();
                $data['exFormID'] = $form->getId();
            }

            if ($this->exFormID === null) {
                // Add block - totally new form. We have to publish it before it is live.
                $entity->setIsPublished(true);
                $entityManager->persist($entity);
                $entityManager->flush();
            }
        }

        if (!$entity) {
            throw new \Exception(t('Invalid Express object selected.'));
        }

        // Now, we handle the entity results folder.
        $resultsNode = Node::getByID($entity->getEntityResultsNodeId());
        $folder = Node::getByID($data['resultsFolder']);
        if (is_object($folder)) {
            $resultsNode->move($folder);
        }

        // File manager folder
        $addFilesToFolderFromPost = $data['addFilesToFolder'];
        $existingAddFilesToFolder = $this->addFilesToFolder;
        unset($data['addFilesToFolder']);

        if ($addFilesToFolderFromPost && $addFilesToFolderFromPost != $existingAddFilesToFolder) {
            $filesystem = new Filesystem();
            $addFilesToFolder = $filesystem->getFolder($addFilesToFolderFromPost);
            $fp = new Checker($addFilesToFolder);
            if ($fp->canSearchFiles()) {
                $data['addFilesToFolder'] = $addFilesToFolderFromPost;
            }
        }

        if (!isset($data['addFilesToFolder'])) {
            $data['addFilesToFolder'] = $existingAddFilesToFolder;
        }

        $session = $this->app->make('session');
        $session->remove('block.express_form.new');

        return parent::save($data);
    }

    public function view()
    {
        $form = $this->getFormEntity();
        if ($form) {
            $entity = $form->getEntity();
            if ($entity) {
                $express = $this->app->make('express');
                $controller = $express->getEntityController($entity);
                $factory = new ContextFactory($controller);
                $context = $factory->getContext(new FrontendFormContext());
                $renderer = new \Concrete\Core\Express\Form\Renderer(
                    $context,
                    $form
                );
                if (is_object($form)) {
                    $this->set('expressForm', $form);
                }
                if ($this->displayCaptcha) {
                    $this->set('captcha', $this->app->make('helper/validation/captcha'));
                    $this->requireAsset('css', 'core/frontend/captcha');
                }
                $this->requireAsset('css', 'core/frontend/errors');
                $this->set('renderer', $renderer);
            }
        }
        if (!isset($renderer)) {
            $page = $this->block->getBlockCollectionObject();
            $this->app->make('log')
                ->warning(t('Form block on page %s (ID: %s) could not be loaded. Its express object or express form no longer exists.', $page->getCollectionName(), $page->getCollectionID()));
        }
    }

    protected function areFormSubmissionsStored()
    {
        $config = $this->getFormSubmissionConfigValue();
        if ($config === true) {
            return true;
        }
        if ($config === 'auto') {
            return $this->storeFormSubmission; // let the block decide.
        }

        // else config is false.
        return false;
    }

    protected function getFormSubmissionConfigValue()
    {
        $config = $this->app->make('config');
        return $config->get('concrete.form.store_form_submissions');
    }

    public function getNotifications()
    {
        $notifications = [new FormBlockSubmissionEmailNotification($this->app, $this)];
        //if we don't save data we must not use this notifier because entry is already not saved
        if ($this->areFormSubmissionsStored()) {
            array_unshift($notifications, new FormBlockSubmissionNotification($this->app, $this));
        }

        return $notifications;
    }

    public function action_form_success($bID = null)
    {
        if ($this->bID == $bID) {
            $this->set('success', $this->thankyouMsg);
        }

        $this->view();
    }

    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        if (!empty($args['recipientEmail'])) {
            $inputtedEmails = array_map('trim', explode(',', $args['recipientEmail']));
            $validator = new EmailValidator();
            foreach($inputtedEmails as $email) {
                if (!$validator->isValid($email)) {
                    $e->add(t('Email address for recipient "%s" is invalid', $email));
                }
            }
        }

        return $e;
    }

    public function duplicate_clipboard($newBID)
    {
        $newBlockRecord = parent::duplicate($newBID);

        if ($newBlockRecord !== null && is_object($form = $this->getFormEntity())) {
            $entity = $form->getEntity();
            if (!$entity->getIncludeInPublicList()) {
                $controls = [];
                $cloner = $this->app->make(\Concrete\Core\Express\Entity\Cloner::class);
                $newEntity = $cloner->cloneEntity($entity, $controls);
                $newBlockRecord->exFormID = $newEntity->getDefaultEditForm()->getId();
                $newBlockRecord->Save();
            }
        }

        return $newBlockRecord;
    }

    public function delete()
    {
        parent::delete();

        $form = $this->getFormEntity();
        if (is_object($form)) {
            $entity = $form->getEntity();
            $entityManager = $this->app->make(EntityManagerInterface::class);
            // Important – are other blocks in the system using this form? If so, we don't want to delete it!
            $db = $entityManager->getConnection();
            $r = $db->fetchColumn('select count(bID) from btExpressForm where bID <> ? and exFormID = ?', [$this->bID, $this->exFormID]);
            if (0 == $r && !$entity->getIncludeInPublicList()) {
                $entityManager->remove($entity);
                $entityManager->flush();
            }
        }
    }

    public function action_submit($bID = null)
    {
        if ($this->bID == $bID) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $form = $this->getFormEntity();

            if (is_object($form)) {
                $express = $this->app->make('express');
                $entity = $form->getEntity();

                /** @var ControllerInterface $controller */
                $controller = $express->getEntityController($entity);
                $processor = $controller->getFormProcessor();
                $validator = $processor->getValidator($this->request);
                if ($this->displayCaptcha) {
                    $validator->addRoutine(
                        new CaptchaRoutine(
                        $this->app->make('helper/validation/captcha')
                    )
                    );
                }

                $validator->validate($form);
                $manager = $controller->getEntryManager($this->request);
                $e = $validator->getErrorList();
                if ($e->has()) {
                    $this->set('error', $e);
                    $this->view();
                    return false;
                }


                if ($this->areFormSubmissionsStored()) {
                    $entry = $manager->addEntry($entity, $this->app->make('site')->getSite());
                    $entry = $manager->saveEntryAttributesForm($form, $entry);
                    $values = $entity->getAttributeKeyCategory()->getAttributeValues($entry);
                    // Check antispam
                    $antispam = $this->app->make('helper/validation/antispam');
                    $submittedData = '';
                    foreach ($values as $value) {
                        $submittedData .= $value->getAttributeKey()->getAttributeKeyDisplayName('text') . ":\r\n";
                        $submittedData .= $value->getPlainTextValue() . "\r\n\r\n";
                    }

                    if (!$antispam->check($submittedData, 'form_block')) {
                        // Remove the entry and silently fail.
                        $entityManager->refresh($entry);
                        $entityManager->remove($entry);
                        $entityManager->flush();

                        $r = Redirect::page($this->request->getCurrentPage());
                        $r->setTargetUrl($r->getTargetUrl() . '#form' . $this->bID);

                        return $r;
                    }

                    // Handle file based items
                    $set = null;
                    $folder = null;
                    $filesystem = new Filesystem();
                    $rootFolder = $filesystem->getRootFolder();
                    if ($this->addFilesToSet) {
                        $set = Set::getByID($this->addFilesToSet);
                    }
                    if ($this->addFilesToFolder) {
                        $folder = $filesystem->getFolder($this->addFilesToFolder);
                    }
                    $entityManager->refresh($entry);
                    foreach ($values as $value) {
                        $value = $value->getValueObject();
                        if ($value instanceof FileProviderInterface) {
                            $files = $value->getFileObjects();
                            foreach ($files as $file) {
                                if ($set) {
                                    $set->addFileToSet($file);
                                }
                                if ($folder && $folder->getTreeNodeID() != $rootFolder->getTreeNodeID()) {
                                    $fileNode = $file->getFileNodeObject();
                                    if ($fileNode) {
                                        $fileNode->move($folder);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $entry = $manager->createEntry($entity);
                }
                $submittedAttributeValues = $manager->getEntryAttributeValuesForm($form, $entry);
                $notifier = $controller->getNotifier($this);
                $notifications = $notifier->getNotificationList();
                array_walk($notifications->getNotifications(), function ($notification) use ($submittedAttributeValues) {
                    if (method_exists($notification, "setAttributeValues")) {
                        $notification->setAttributeValues($submittedAttributeValues);
                    }
                });
                $notifier->sendNotifications($notifications, $entry, ProcessorInterface::REQUEST_TYPE_ADD);
                $r = null;
                if ($this->redirectCID > 0) {
                    $c = Page::getByID($this->redirectCID);
                    if (is_object($c) && !$c->isError()) {
                        $r = Redirect::page($c);
                        $target = strpos($r->getTargetUrl(),"?") === false ? $r->getTargetUrl()."?" : $r->getTargetUrl()."&";
                        $r->setTargetUrl($target . 'form_success=1&entry=' . $entry->getID());
                    }
                }

                if (!$r) {
                    $url = Url::to($this->request->getCurrentPage(), 'form_success', $this->bID);
                    $r = Redirect::to($url);
                    $r->setTargetUrl($r->getTargetUrl() . '#form' . $this->bID);
                }

                return $processor->deliverResponse($entry, ProcessorInterface::REQUEST_TYPE_ADD, $r);
            }
        }
        $this->view();
    }

    protected function loadResultsFolderInformation()
    {
        $folder = ExpressEntryCategory::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
        $this->set('formResultsRootFolderNodeID', $folder->getTreeNodeID());
    }

    /**
     * @param \Concrete\Core\Attribute\Controller $controller
     * @param ExpressKey $key
     * @param array $post
     *
     * @return ExpressKey
     */
    protected function saveAttributeKeySettings($controller, ExpressKey $key, $post)
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $settings = $controller->saveKey($post);
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }

        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);
        $entityManager->persist($settings);
        return $key;
    }

    public function edit()
    {
        $this->set('formSubmissionConfig', $this->getFormSubmissionConfigValue());
        $this->set('storeFormSubmission', $this->areFormSubmissionsStored());
        $this->loadResultsFolderInformation();
        $list = Type::getList("express");

        $attribute_fields = [];

        foreach ($list as $type) {
            $attribute_fields[] = ['id' => 'attribute_key|' . $type->getAttributeTypeID(), 'displayName' => $type->getAttributeTypeDisplayName()];
        }

        $select = [];
        $select[0] = new \stdClass();
        $select[0]->label = t('Input Field Types');
        $select[0]->fields = $attribute_fields;

        $other_fields = [];
        $other_fields[] = ['id' => 'entity_property|text', 'displayName' => t('Display Text')];

        $select[1] = new \stdClass();
        $select[1]->label = t('Other Fields');
        $select[1]->fields = $other_fields;

        $controls = [];
        $form = $this->getFormEntity();
        if (is_object($form)) {
            $entity = $form->getEntity();
            $controls = $form->getControls();
            $this->set('formName', $entity->getName());
            $this->set('submitLabel', $this->submitLabel);
            $node = Node::getByID($entity->getEntityResultsNodeId());
            if (is_object($node)) {
                $folder = $node->getTreeNodeParentObject();
                $this->set('resultsFolder', $folder->getTreeNodeID());
            }
            $this->set('formEntity', $form);
            $this->set('expressEntity', $form->getEntity());
        }
        $this->set('controls', $controls);
        $this->set('types_select', $select);
        $tree = ExpressEntryResults::get();
        $this->set('tree', $tree);
        $addFilesToFolder = null;

        if ($this->addFilesToFolder) {
            $filesystem = new Filesystem();
            $addFilesToFolder = $filesystem->getFolder($this->addFilesToFolder);
            $fp = new Checker($addFilesToFolder);
            if ($fp->canSearchFiles()) {
                $this->set('addFilesToFolder', $addFilesToFolder);
            }
        }

        $this->set('entities', Express::getEntities());
        $this->set('notifyMeOnSubmission', (bool) $this->notifyMeOnSubmission);
    }

    public function action_get_type_form()
    {
        $token = app('token');
        if ($token->validate('get_type_form')) {
            $field = explode('|', $this->request->request->get('id'));
            if ('attribute_key' == $field[0]) {
                $type = Type::getByID($field[1]);
                if (is_object($type)) {
                    ob_start();
                    echo $type->render(new AttributeTypeSettingsContext());
                    $html = ob_get_contents();
                    ob_end_clean();

                    $obj = new \stdClass();
                    $obj->content = $html;
                    $obj->showControlRequired = true;
                    $obj->showControlName = true;
                    $obj->assets = $this->getAssetsDefinedDuringOutput();
                }
            } elseif ('entity_property' == $field[0]) {
                $obj = new \stdClass();
                switch ($field[1]) {
                    case 'text':
                        $controller = new TextOptions();
                        ob_start();
                        echo $controller->render();
                        $html = ob_get_contents();
                        ob_end_clean();

                        $obj = new \stdClass();
                        $obj->content = $html;
                        $obj->showControlRequired = false;
                        $obj->showControlName = false;
                        $obj->assets = $this->getAssetsDefinedDuringOutput();
                        break;
                }
            }

            if (isset($obj)) {
                return new JsonResponse($obj);
            }
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
        $this->app->shutdown();

    }

    /**
     * @return array [
     *  Key: string, asset type
     *  Value: string, the URL of the asset
     * ]
     */
    protected function getAssetsDefinedDuringOutput()
    {
        $ag = ResponseAssetGroup::get();
        $r = [];
        foreach ($ag->getAssetsToOutput() as $position => $assets) {
            foreach ($assets as $asset) {
                if (is_object($asset)) {
                    $r[$asset->getAssetType()][] = $asset->getAssetURL();
                }
            }
        }

        return $r;
    }

    public function action_get_control()
    {
        $token = app('token');
        if ($token->validate('get_control')) {
            $entityManager = $this->app->make(EntityManagerInterface::class);
            $control = $entityManager->getRepository(Control::class)
                ->findOneById($this->request->query->get('control'));

            if (is_object($control)) {
                /**
                 * @var $control Control
                 */
                $controlForm = $control->getFieldSet()->getForm();
                if (isset($this->exFormID)) {
                    $exFormID = $this->exFormID;
                } else {
                    $fieldSet = $this->getFormFieldSet();
                    if ($fieldSet) {
                        $exFormID = $fieldSet->getForm()->getId();
                    }
                }

                if ($controlForm && $controlForm->getId() == $exFormID) {
                    $obj = new \stdClass();

                    if ($control instanceof AttributeKeyControl) {
                        $type = $control->getAttributeKey()->getAttributeType();
                        ob_start();
                        echo $type->render(new AttributeTypeSettingsContext(), $control->getAttributeKey());
                        $html = ob_get_contents();
                        ob_end_clean();

                        $obj->question = $control->getDisplayLabel();
                        $obj->isRequired = $control->isRequired();
                        $obj->showControlRequired = true;
                        $obj->showControlName = true;
                        $obj->type = 'attribute_key|' . $type->getAttributeTypeID();
                        $obj->typeDisplayName = $type->getAttributeTypeDisplayName();
                    } else {
                        $controller = $control->getControlOptionsController();
                        ob_start();
                        echo $controller->render();
                        $html = ob_get_contents();
                        ob_end_clean();

                        $obj->showControlRequired = false;
                        $obj->showControlName = false;
                        $obj->type = 'entity_property|text';
                    }

                    $obj->id = $control->getID();
                    $obj->assets = $this->getAssetsDefinedDuringOutput();
                    $obj->typeContent = $html;

                    return new JsonResponse($obj);
                } else {
                    throw new \Exception(t('Express Control is not contained within the form attached to this block.'));
                }
            } else {
                throw new \Exception(t('Invalid Express object control ID.'));
            }
        } else {
            throw new \Exception(t($token->getErrorMessage()));
        }
    }

    /**
     * @return \Concrete\Core\Entity\Express\Form|null
     */
    protected function getFormEntity()
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(\Concrete\Core\Entity\Express\Form::class)
            ->findOneById($this->exFormID);
    }
}
