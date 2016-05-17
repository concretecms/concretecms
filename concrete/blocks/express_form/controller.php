<?php
namespace Concrete\Block\ExpressForm;

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Entry\Manager;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Express\Form\Validator;
use Concrete\Core\File\FileProviderInterface;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 640;
    protected $btCacheBlockOutput = true;
    protected $btInterfaceHeight = 480;
    protected $btTable = 'btExpressForm';
    protected $entityManager;

    const FORM_RESULTS_CATEGORY_NAME = 'Forms';

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Build simple forms and surveys.");
    }

    public function getBlockTypeName()
    {
        return t("Form");
    }

    protected function clearSessionControls()
    {
        $session = \Core::make('session');
        $session->remove('block.express_form.new');
    }

    public function add()
    {
        $c = \Page::getCurrentPage();
        $this->set('formName', $c->getCollectionName());
        $this->set('submitLabel', t('Submit'));
        $this->set('thankyouMsg', t('Thanks!'));
        $this->edit();
        $this->set('resultsFolder', $this->get('formResultsRootFolderNodeID'));
    }

    public function action_form_success($bID = null)
    {
        if ($this->bID == $bID) {
            $this->set('success', $this->thankyouMsg);
            $this->view();
        }
    }

    public function delete()
    {
        parent::delete();
        $entity = $this->getFormEntity()->getEntity();
        $entityManager = \Core::make('database/orm')->entityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }


    public function action_submit($bID = null)
    {
        if ($this->bID == $bID) {
            $entityManager = \Core::make('database/orm')->entityManager();
            $form = $this->getFormEntity();
            if (is_object($form)) {
                $e = \Core::make('error');
                $validator = new Validator($e, $this->request);
                $validator->validate($form);
                if ($this->displayCaptcha) {
                    $captcha = \Core::make('helper/validation/captcha');
                    if (!$captcha->check()) {
                        $e->add(t('Incorrect captcha code.'));
                    }
                }
                $this->set('error', $e);
            }

            if (isset($e) && !$e->has()) {

                $entity = $form->getEntity();
                $manager = new Manager($entityManager, $this->request);
                $entry = $manager->addEntry($entity);
                $entry = $manager->saveEntryAttributesForm($form, $entry);
                $values = $entity->getAttributeKeyCategory()->getAttributeValues($entry);

                // Check antispam
                $antispam = \Core::make('helper/validation/antispam');
                $submittedData = '';
                foreach($values as $value) {
                    $submittedData .= $value->getAttributeKey()->getAttributeKeyDisplayName() . "\r\n";
                    $submittedData .= $value->getValue('displaySanitized') . "\r\n\r\n";
                }

                if (!$antispam->check($submittedData, 'form_block')) {
                    // Remove the entry and silently fail.
                    $entityManager->refresh($entry);
                    $entityManager->remove($entry);
                    $entityManager->flush();
                    $c = \Page::getCurrentPage();
                    $r = Redirect::page($c);
                    $r->setTargetUrl($r->getTargetUrl() . '#form' . $this->bID);
                    return $r;
                }

                if ($this->addFilesToSet) {
                    $set = Set::getByID($this->addFilesToSet);
                    if (is_object($set)) {
                        foreach($values as $value) {
                            $value = $value->getValueObject();
                            if ($value instanceof FileProviderInterface) {
                                $files = $value->getFileObjects();
                                foreach($files as $file) {
                                    $set->addFileToSet($file);
                                }
                            }
                        }
                    }
                }

                if ($this->notifyMeOnSubmission) {
                    if (\Config::get('concrete.email.form_block.address') && strstr(Config::get('concrete.email.form_block.address'), '@')) {
                        $formFormEmailAddress = \Config::get('concrete.email.form_block.address');
                    } else {
                        $adminUserInfo = \UserInfo::getByID(USER_SUPER_ID);
                        $formFormEmailAddress = $adminUserInfo->getUserEmail();
                    }

                    $replyToEmailAddress = $formFormEmailAddress;
                    if ($this->replyToEmailControlID) {
                        $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                            ->findOneById($this->replyToEmailControlID);
                        if (is_object($control)) {
                            $email = $entry->getAttribute($control->getAttributeKey());
                            if ($email) {
                                $replyToEmailAddress = $email;
                            }
                        }
                    }

                    $mh = \Core::make('helper/mail');
                    $mh->to($this->recipientEmail);
                    $mh->from($formFormEmailAddress);
                    $mh->replyto($replyToEmailAddress);
                    $mh->addParameter('entity', $entity);
                    $mh->addParameter('formName', $this->surveyName);
                    $mh->addParameter('attributes', $values);
                    $mh->load('block_express_form_submission');
                    $mh->setSubject(t('%s Form Submission', $this->surveyName));
                    $mh->sendMail();
                }

                if ($this->redirectCID > 0) {
                    $c = \Page::getByID($this->redirectCID);
                    if (is_object($c) && !$c->isError()) {
                        $r = Redirect::page($c);
                        $r->setTargetUrl($r->getTargetUrl() . '?form_success=1');
                        return $r;
                    }
                }

                $c = \Page::getCurrentPage();
                $url = \URL::to($c);
                $r = Redirect::to($url, 'form_success', $this->bID);
                $r->setTargetUrl($r->getTargetUrl() . '#form' . $this->bID);
                return $r;

            }
        }
        $this->view();
    }

    protected function loadResultsFolderInformation()
    {
        $folder = Category::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
        $this->set('formResultsRootFolderNodeID', $folder->getTreeNodeID());
    }

    public function action_add_control()
    {
        $entityManager = \Core::make('database/orm')->entityManager();
        $post = $this->request->request->all();
        $session = \Core::make('session');
        $controls = $session->get('block.express_form.new');

        if (!is_array($controls)) {
            $controls = array();
        }

        $type = Type::getByID($post['type']);
        if (is_object($type)) {

            $control = new AttributeKeyControl();
            $control->setId((new UuidGenerator())->generate($entityManager, $control));
            $key = new ExpressKey();
            $key->setAttributeKeyName($post['question']);
            $key->setAttributeKeyHandle(sprintf('form_question_%s', count($controls) + 1));
            if ($post['required']) {
                $control->setIsRequired(true);
            }

            $controller = $type->getController();
            $key = $this->saveAttributeKeyType($controller, $key, $post);

            $control->setAttributeKey($key);
            $controls[$control->getId()]= $control;
            $session->set('block.express_form.new', $controls);
            return new JsonResponse($control);
        } else {
            $e = \Core::make('error');
            $e->add(t('You must choose a valid field type.'));
            return new JsonResponse($e);
        }
    }

    protected function saveAttributeKeyType($controller, ExpressKey $key, $post)
    {
        $key_type = $controller->saveKey($post);
        if (!is_object($key_type)) {
            $key_type = $controller->getAttributeKeyType();
        }
        $key_type->setAttributeKey($key);
        $key->setAttributeKeyType($key_type);
        return $key;
    }

    public function action_update_control()
    {
        $entityManager = \Core::make('database/orm')->entityManager();
        $post = $this->request->request->all();
        $type = Type::getByID($post['type']);
        $session = \Core::make('session');

        $sessionControls = $session->get('block.express_form.new');
        if (is_array($sessionControls)) {
            foreach($sessionControls as $sessionControl) {
                if ($sessionControl->getId() == $this->request->request->get('id')) {
                    $control = $sessionControl;
                    break;
                }
            }
        }

        if (!isset($control)) {
            $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                ->findOneById($this->request->request->get('id'));
        }

        if (is_object($type) && $control instanceof AttributeKeyControl) {

            $key = $control->getAttributeKey();
            $key->setAttributeKeyName($post['question']);
            if ($post['requiredEdit']) {
                $control->setIsRequired(true);
            } else {
                $control->setIsRequired(false);
            }
            $controller = $key->getController();
            $key = $this->saveAttributeKeyType($controller, $key, $post);
            $control->setAttributeKey($key);

            $sessionControls[$control->getId()]= $control;
            $session->set('block.express_form.new', $sessionControls);

            return new JsonResponse($control);
        } else {
            $e = \Core::make('error');
            $e->add(t('You must choose a valid field type.'));
            return new JsonResponse($e);
        }
    }
    public function save($data)
    {
        $requestControls = (array) $this->request->request->get('controlID');
        $entityManager = \Core::make('database/orm')->entityManager();
        $session = \Core::make('session');
        $sessionControls = $session->get('block.express_form.new');

        if (!$this->exFormID) {

            // This is a new submission.
            $c = \Page::getCurrentPage();
            $name = $data['formName'] ? $data['formName'] : t('Form');

            // Create a results node
            $node = Category::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
            $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add($name, $node);

            $entity = new Entity();
            $entity->setName($name);
            $entity->setIncludeInPublicList(false);
            $entity->setHandle(sprintf('express_form_%s', $this->block->getBlockID()));
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
            $field_set = new FieldSet();
            $field_set->setForm($form);
            $entityManager->persist($field_set);
            $entityManager->flush();

            $indexer = $entity->getAttributeKeyCategory()->getSearchIndexer();
            if (is_object($indexer)) {
                $indexer->createRepository($entity->getAttributeKeyCategory());
            }

        } else {
            // We check save the order as well as potentially deleting orphaned controls.
            $form = $entityManager->getRepository('Concrete\Core\Entity\Express\Form')
                ->findOneById($this->exFormID);

            /**
             * @var $form Form
             * @var $field_set FieldSet
             */
            $field_set = $form->getFieldSets()[0];
            $entity = $form->getEntity();
        }

        // First, we get the existing controls, so we can check them to see if controls should be removed later.
        $existingControls = $form->getControls();
        $existingControlIDs = array();
        foreach($existingControls as $control) {
            $existingControlIDs[] = $control->getId();
        }

        // Now, let's loop through our request controls
        $indexKeys = array();
        $position = 0;
        foreach($requestControls as $id) {

            if (isset($sessionControls[$id])) {
                $control = $sessionControls[$id];
                if (!in_array($id, $existingControlIDs)) {
                    // Possibility 1: This is a new control.
                    $key = $control->getAttributeKey();
                    $key->setEntity($entity);
                    $control->setAttributeKey($key);
                    $control->setFieldSet($field_set);
                    $entityManager->persist($key);
                    $control->setPosition($position);
                    $entityManager->persist($control);

                    $indexKeys[] = $key;

                } else {
                    // Possibility 2: This is an existing control that has an updated version.
                    foreach($existingControls as $existingControl) {
                        if ($existingControl->getId() == $id) {
                            $key = $existingControl->getAttributeKey();
                            // question name
                            $key->setAttributeKeyName($control->getAttributeKey()->getAttributeKeyName());

                            // attribute type
                            $key_type = $control->getAttributeKey()->getAttributeKeyType();
                            $key_type->setAttributeKey($key);
                            $key->setAttributeKeyType($key_type);

                            // Required
                            $existingControl->setIsRequired($control->isRequired());

                            // save it.
                            $entityManager->persist($existingControl);

                            $indexKeys[] = $key;
                        }
                    }
                }
            } else {
                // Possibility 3: This is an existing control that doesn't have a new version. But we still
                // want to update its position.
                foreach($existingControls as $control) {
                    if ($control->getId() == $id) {
                        $control->setPosition($position);
                        $entityManager->persist($control);
                    }
                }
            }

            $position++;
        }

        // Now, we look through all existing controls to see whether they should be removed.
        foreach($existingControls as $control) {
            // Does this control exist in the request? If not, it gets axed
            if (!is_array($requestControls) || !in_array($control->getId(), $requestControls)) {
                $entityManager->remove($control);
            }
        }

        $entityManager->flush();

        $category = new ExpressCategory($entity, \Core::make('app'), $entityManager);
        $indexer = $category->getSearchIndexer();
        foreach($indexKeys as $key) {
            $indexer->updateRepository($category, $key);
        }

        // Now, we handle the entity results folder.
        $resultsNode = Node::getByID($entity->getEntityResultsNodeId());
        $folder = Node::getByID($data['resultsFolder']);
        if (is_object($folder)) {
            $resultsNode->move($folder);
        }


        $data['exFormID'] = $form->getId();

        $this->clearSessionControls();
        parent::save($data);
    }

    public function edit()
    {
        $this->loadResultsFolderInformation();
        $this->requireAsset('core/tree');
        $this->clearSessionControls();
        $list = Type::getList();
        $types_select = array();
        $types_select[] = ['id' => '', 'displayName' => t('** Choose Type')];

        foreach($list as $type) {
            $types_select[] = ['id' => $type->getAttributeTypeID(), 'displayName' => $type->getAttributeTypeDisplayName()];
        }

        $controls = array();
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
        }
        $this->set('controls', $controls);
        $this->set('types_select', $types_select);
        $tree = ExpressEntryResults::get();
        $this->set('tree', $tree);
    }

    public function action_get_type_form()
    {
        $type = Type::getByID($this->request->request->get('atID'));
        if (is_object($type)) {
            ob_start();
            echo $type->render('type_form');
            $html = ob_get_contents();
            ob_end_clean();

            $obj = new \stdClass();
            $obj->content = $html;
            $obj->assets = $this->getAssetsDefinedDuringOutput();

            return new JsonResponse($obj);
        }
        \Core::make('app')->shutdown();
    }

    protected function getAssetsDefinedDuringOutput()
    {
        $ag = ResponseAssetGroup::get();
        $r = array();
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
        $entityManager = \Core::make('database/orm')->entityManager();
        $session = \Core::make('session');
        $sessionControls = $session->get('block.express_form.new');
        if (is_array($sessionControls)) {
            foreach($sessionControls as $sessionControl) {
                if ($sessionControl->getID() == $this->request->request->get('control')) {
                    $control = $sessionControl;
                    break;
                }
            }
        }

        if (!isset($control)) {
            $control = $entityManager->getRepository('Concrete\Core\Entity\Express\Control\Control')
                ->findOneById($this->request->request->get('control'));
        }

        if (is_object($control) && $control instanceof AttributeKeyControl) {
            $type = $control->getAttributeKey()->getAttributeType();

            ob_start();
            echo $type->render('type_form', $control->getAttributeKey());
            $html = ob_get_contents();
            ob_end_clean();

            $obj = new \stdClass();
            $obj->id = $control->getID();
            $obj->question = $control->getDisplayLabel();
            $obj->isRequired = $control->isRequired();
            $obj->type = $type->getAttributeTypeID();
            $obj->typeContent = $html;
            $obj->assets = $this->getAssetsDefinedDuringOutput();

            return new JsonResponse($obj);
        }
        \Core::make('app')->shutdown();
    }



    protected function getFormEntity()
    {
        $entityManager = \Core::make('database/orm')->entityManager();
        return $entityManager->getRepository('Concrete\Core\Entity\Express\Form')
            ->findOneById($this->exFormID);
    }
    public function view()
    {
        $form = $this->getFormEntity();
        $app = \Core::make('app');
        if (is_object($form)) {
            $renderer = $app->make('Concrete\Core\Express\Form\Renderer');
            $this->set('renderer', $renderer);
            $this->set('expressForm', $form);
        }
        if ($this->displayCaptcha) {
            $this->requireAsset('css', 'core/frontend/captcha');
        }
    }




}
