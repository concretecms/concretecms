<?php
namespace Concrete\Block\ExpressForm;

use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Renderer;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
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
        return t("Express Form");
    }

    protected function clearSessionControls()
    {
        $session = \Core::make('session');
        $session->remove('block.express_form.new');
    }

    public function add()
    {
        $this->edit();
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
    public function save()
    {
        $requestControls = (array) $this->request->request->get('controlID');
        $entityManager = \Core::make('database/orm')->entityManager();
        $session = \Core::make('session');
        $sessionControls = $session->get('block.express_form.new');

        if (!$this->exFormID) {

            // This is a new submission.
            $c = \Page::getCurrentPage();
            $name = is_object($c) ? $c->getCollectionName() : t('Form');

            // Create a results node
            $node = ExpressEntryCategory::getNodeByName(self::FORM_RESULTS_CATEGORY_NAME);
            $node = \Concrete\Core\Tree\Node\Type\ExpressEntryResults::add($name, $node);

            $entity = new Entity();
            $entity->setName($name);
            $entity->setHandle(sprintf('express_form_%s', $this->block->getBlockID()));
            $entity->setEntityResultsNodeId($node->getTreeNodeID());
            $entityManager->persist($entity);
            $entityManager->flush();

            $form = new Form();
            $form->setName(t('Form'));
            $form->setEntity($entity);
            $entityManager->persist($form);
            $entityManager->flush();

            // Create a Field Set and a Form
            $field_set = new FieldSet();
            $field_set->setForm($form);
            $entityManager->persist($field_set);
            $entityManager->flush();

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

        $data = array(
            'exFormID' => $form->getId()
        );

        $this->clearSessionControls();
        parent::save($data);
    }

    public function edit()
    {
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
            $controls = $form->getControls();
        }
        $this->set('controls', $controls);
        $this->set('types_select', $types_select);
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
    }




}
