<?php

namespace Concrete\Block\ExpressForm;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\Context\AttributeTypeSettingsContext;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Control\TextControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Permission\Checker;

class Controller extends BlockController implements NotificationProviderInterface
{
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 700;
    protected $btCacheBlockOutput = false;
    protected $btTable = 'btExpressForm';
    protected $btExportPageColumns = ['redirectCID'];

    public $notifyMeOnSubmission;
    public $recipientEmail;
    public $replyToEmailControlID;
    public $storeFormSubmission = 1;

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

    protected function clearSessionControls()
    {
        $session = $this->app->make('session');
        $session->remove('block.express_form.new');
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

                $validator->validate($form, ProcessorInterface::REQUEST_TYPE_ADD);
                $manager = $controller->getEntryManager($this->request);
                $entry = $manager->createEntry($entity);
                $e = $validator->getErrorList();
                if (isset($e) && !$e->has() && $this->areFormSubmissionsStored()) {
                    $entry = $manager->addEntry($entity);
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
                }
                if (isset($e) && !$e->has()) {
                    $submittedAttributeValues = $manager->getEntryAttributeValuesForm($form, $entry);
                    $notifier = $controller->getNotifier($this);
                    $notifications = $notifier->getNotificationList();
                    array_walk($notifications->getNotifications(), function ($notification) use ($submittedAttributeValues,$key) {
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
                            $r->setTargetUrl($r->getTargetUrl() . '?form_success=1&entry=' . $entry->getID());
                        }
                    }

                    if (!$r) {
                        $url = Url::to($this->request->getCurrentPage(), 'form_success', $this->bID);
                        $r = Redirect::to($url);
                        $r->setTargetUrl($r->getTargetUrl() . '#form' . $this->bID);
                    }

                    return $processor->deliverResponse($entry, ProcessorInterface::REQUEST_TYPE_ADD, $r);
                } else {
                    $this->set('error', $e);
                }
            }
        }
        $this->view();
    }

    protected function loadResultsFolderInformation()
    {
        $folder = ExpressEntryCategory::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
        $this->set('formResultsRootFolderNodeID', $folder->getTreeNodeID());
    }

    public function action_add_control()
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $post = $this->request->request->all();
        $session = $this->app->make('session');
        $controls = $session->get('block.express_form.new');

        if (!is_array($controls)) {
            $controls = [];
        }

        $field = explode('|', $this->request->request->get('type'));
        switch ($field[0]) {
            case 'attribute_key':
                $type = Type::getByID($field[1]);
                if (is_object($type)) {
                    $control = new AttributeKeyControl();
                    $control->setId((new UuidGenerator())->generate($entityManager, $control));
                    $key = new ExpressKey();
                    $key->setAttributeKeyName($post['question']);
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
        }

        $controls[$control->getId()]= $control;
        $session->set('block.express_form.new', $controls);

        return new JsonResponse($control);
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
        $settings = $controller->saveKey($post);
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }

        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);

        return $key;
    }

    public function action_update_control()
    {
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $post = $this->request->request->all();
        $session = $this->app->make('session');

        $sessionControls = $session->get('block.express_form.new');
        if (is_array($sessionControls)) {
            foreach ($sessionControls as $sessionControl) {
                if ($sessionControl->getId() == $this->request->request->get('id')) {
                    $control = $sessionControl;
                    break;
                }
            }
        }

        if (!isset($control)) {
            $control = $entityManager->getRepository(\Concrete\Core\Entity\Express\Control\Control::class)
                ->findOneById($this->request->request->get('id'));
        }

        $field = explode('|', $this->request->request->get('type'));
        switch ($field[0]) {
            case 'attribute_key':
                $type = Type::getByID($field[1]);
                if (is_object($type)) {
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
                    $control->setAttributeKey($key);
                }

                break;
            case 'entity_property':
                /** @var EntityPropertyType $propertyType */
                $type = $this->app->make(EntityPropertyType::class);

                $saver = $control->getControlSaveHandler();
                $control = $saver->saveFromRequest($control, $this->request);

                break;
        }

        if (!is_object($type)) {
            $e = $this->app->make('error');
            $e->add(t('You must choose a valid field type.'));

            return new JsonResponse($e);
        }

        $sessionControls[$control->getId()]= $control;
        $session->set('block.express_form.new', $sessionControls);

        return new JsonResponse($control);
    }
    public function save($data)
    {
        $data['storeFormSubmission'] = isset($data['storeFormSubmission']) ?: 0;
        if (isset($data['exFormID']) && '' != $data['exFormID']) {
            return parent::save($data);
        }

        $requestControls = (array) $this->request->request->get('controlID');
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $session = $this->app->make('session');
        $sessionControls = $session->get('block.express_form.new');

        $name = $data['formName'] ? $data['formName'] : t('Form');

        if (!$this->exFormID) {
            // This is a new submission.
            $c = \Page::getCurrentPage();

            // Create a results node
            $node = ExpressEntryCategory::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
            $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add($name, $node);

            $entity = new Entity();
            $entity->setName($name);
            $entity->setIncludeInPublicList(false);
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
        } else {
            // We check save the order as well as potentially deleting orphaned controls.

            /* @var Form $form */
            $form = $entityManager->getRepository(\Concrete\Core\Entity\Express\Form::class)
                ->findOneById($this->exFormID);

            /* @var FieldSet $fieldSet */
            $fieldSet = $form->getFieldSets()[0];

            $entity = $form->getEntity();
            $entity->setName($name);

            $entityManager->persist($entity);
            $entityManager->flush();

            $nodeId = $entity->getEntityResultsNodeId();
            $node = Node::getByID($nodeId);
            $node->setTreeNodeName($name);
        }

        $attributeKeyCategory = $entity->getAttributeKeyCategory();
        $attributeKeyHandleGenerator = new AttributeKeyHandleGenerator($attributeKeyCategory);

        // First, we get the existing controls, so we can check them
        // to see if controls should be removed later.
        $existingControls = $form->getControls();
        $existingControlIDs = [];
        foreach ($existingControls as $control) {
            $existingControlIDs[] = $control->getId();
        }

        // Now, let's loop through our request controls
        $indexKeys = [];
        $position = 0;

        foreach ($requestControls as $id) {
            if (isset($sessionControls[$id])) {
                $control = $sessionControls[$id];
                if (!in_array($id, $existingControlIDs)) {
                    // Possibility 1: This is a new control.
                    if ($control instanceof AttributeKeyControl) {
                        $key = $control->getAttributeKey();
                        $type = $key->getAttributeType();
                        $settings = $key->getAttributeKeySettings();

                        // We have to merge entities back into the entity manager because they have been
                        // serialized. First type, because if we merge key first type gets screwed
                        $mergedType = $entityManager->merge($type);

                        // Now key, because we need key to set as the primary key for settings.
                        // Note - we rename the objects in order to get spl_object_hash to not screw them up
                        // Ref: https://github.com/concrete5/concrete5/issues/5584#issuecomment-403652601
                        $mergedKey = $entityManager->merge($key);
                        $mergedKey->setAttributeType($mergedType);
                        $mergedKey->setEntity($entity);
                        $mergedKey->setAttributeKeyHandle($attributeKeyHandleGenerator->generate($mergedKey));
                        $entityManager->persist($mergedKey);
                        $entityManager->flush();

                        // Now attribute settings.
                        $settings->setAttributeKey($mergedKey);
                        $mergedSettings = $entityManager->merge($settings);
                        $entityManager->persist($mergedSettings);
                        $entityManager->flush();

                        $control->setAttributeKey($mergedKey);
                        $indexKeys[] = $mergedKey;
                    }

                    $control->setFieldSet($fieldSet);
                    $control->setPosition($position);
                    $entityManager->persist($control);
                    $entityManager->flush();
                } else {
                    // Possibility 2: This is an existing control that has an updated version.
                    foreach ($existingControls as $existingControl) {
                        if ($existingControl->getId() == $id) {
                            if ($control instanceof AttributeKeyControl) {
                                $settings = $control->getAttributeKey()->getAttributeKeySettings();
                                $key = $existingControl->getAttributeKey();
                                $type = $key->getAttributeType();
                                $type = $entityManager->merge($type);

                                // question name
                                $key->setAttributeKeyName($control->getAttributeKey()->getAttributeKeyName());
                                $key->setAttributeKeyHandle($attributeKeyHandleGenerator->generate($key));

                                // Key Type
                                $key = $entityManager->merge($key);
                                $key->setAttributeType($type);

                                $type = $control->getAttributeKey()->getAttributeType();
                                $type = $entityManager->merge($type);
                                $key->setAttributeType($type);
                                $settings = $control->getAttributeKey()->getAttributeKeySettings();
                                $settings->setAttributeKey($key);
                                $settings = $settings->mergeAndPersist($entityManager);

                                // Required
                                $existingControl->setIsRequired($control->isRequired());

                                // Finalize control
                                $existingControl->setAttributeKey($key);

                                $indexKeys[] = $key;
                            } elseif ($control instanceof TextControl) {
                                // Wish we had a better way of doing this that wasn't so hacky.
                                $existingControl->setHeadline($control->getHeadline());
                                $existingControl->setBody($control->getBody());
                            }

                            // save it.
                            $entityManager->persist($existingControl);
                        }
                    }
                }
            } else {
                // Possibility 3: This is an existing control that doesn't have a new version. But we still
                // want to update its position.
                foreach ($existingControls as $control) {
                    if ($control->getId() == $id) {
                        $control->setPosition($position);
                        $entityManager->persist($control);
                    }
                }
            }

            ++$position;
        }

        // Now, we look through all existing controls to see whether they should be removed.
        foreach ($existingControls as $control) {
            // Does this control exist in the request? If not, it gets axed
            if (!is_array($requestControls) || !in_array($control->getId(), $requestControls)) {
                $entityManager->remove($control);
            }
        }

        $entityManager->flush();

        $category = new ExpressCategory($entity, $this->app, $entityManager);
        $indexer = $category->getSearchIndexer();
        foreach ($indexKeys as $key) {
            // The key might not be fully initialized and it might be coming
            // from session and might not have all the right info in it.
            // This is to fix a bug where packaged attribute types weren't being seen
            // as being in a package because the package handle property on the object wasn't set.
            $entityManager->refresh($key->getAttributeType());
            $indexer->updateRepositoryColumns($category, $key);
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

        if (!$data['addFilesToFolder']) {
            $data['addFilesToFolder'] = $existingAddFilesToFolder;
        }

        $data['exFormID'] = $form->getId();

        $this->clearSessionControls();

        parent::save($data);
    }

    public function edit()
    {
        $this->set('formSubmissionConfig', $this->getFormSubmissionConfigValue());
        $this->set('storeFormSubmission', $this->areFormSubmissionsStored());
        $this->loadResultsFolderInformation();
        $this->requireAsset('core/tree');
        $this->clearSessionControls();
        $list = Type::getList();

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
            $this->set('formName', h($entity->getName()));
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
    }

    public function action_get_type_form()
    {
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
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $session = $this->app->make('session');
        $sessionControls = $session->get('block.express_form.new');
        if (is_array($sessionControls)) {
            foreach ($sessionControls as $sessionControl) {
                if ($sessionControl->getID() == $this->request->query->get('control')) {
                    $control = $sessionControl;
                    break;
                }
            }
        }

        if (!isset($control)) {
            $control = $entityManager->getRepository(\Concrete\Core\Entity\Express\Control\Control::class)
                ->findOneById($this->request->query->get('control'));
        }

        if (is_object($control)) {
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
        }

        $this->app->shutdown();
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
