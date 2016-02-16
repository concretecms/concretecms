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
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 640;
    protected $btCacheBlockOutput = true;
    protected $btInterfaceHeight = 480;
    protected $btTable = 'btExpressForm';
    protected $entityManager;

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

    public function add()
    {
        $session = \Core::make('session');
        $session->remove('block.express_form.new');
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
            $key = new ExpressKey();
            $key->setAttributeKeyName($post['question']);
            $key->setAttributeKeyHandle(sprintf('form_question_%s', count($controls) + 1));
            $controller = $type->getController();
            $key_type = $controller->saveKey($post);
            if (!is_object($key_type)) {
                $key_type = $controller->getAttributeKeyType();
            }
            $key_type->setAttributeKey($key);
            $key->setAttributeKeyType($key_type);
            $control = new AttributeKeyControl();
            $control->setId((new UuidGenerator())->generate($entityManager, $control));
            $control->setAttributeKey($key);
            if ($post['required']) {
                $control->setIsRequired(true);
            }
        }
        $controls[$control->getId()]= $control;
        $session->set('block.express_form.new', $controls);
        return new JsonResponse($control);
    }

    public function save()
    {
        if (!$this->exFormID) {
            // This is a new submission.
            $session = \Core::make('session');
            $entityManager = \Core::make('database/orm')->entityManager();
            $c = \Page::getCurrentPage();
            $name = is_object($c) ? $c->getCollectionName() : t('Form');

            $entity = new Entity();
            $entity->setName($name);
            $entity->setHandle(sprintf('express_form_%s', $this->block->getBlockID()));
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

            $sessionControls = $session->get('block.express_form.new');
            $requestControls = $this->request->request->get('controlID');
            $i = 0;
            if (is_array($requestControls)) {
                foreach($requestControls as $id) {
                    if (isset($sessionControls[$id])) {
                        $control = $sessionControls[$id];
                        $key = $control->getAttributeKey();
                        $key->setEntity($entity);
                        $control->setAttributeKey($key);
                        $control->setFieldSet($field_set);
                        $control->setPosition($i);
                        $entityManager->persist($key);
                        $entityManager->persist($control);
                        $i++;
                    }
                }
            }

            $entityManager->flush();

            $data = array(
                'exFormID' => $form->getId()
            );
        }

        parent::save($data);
    }

    public function edit()
    {
        $list = Type::getList();
        $types_select = array('' => t('** Select Type'));

        foreach($list as $type) {
            $types_select[$type->getAttributeTypeID()] = $type->getAttributeTypeDisplayName();
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
            $obj->assets = array();
            $ag = ResponseAssetGroup::get();
            foreach ($ag->getAssetsToOutput() as $position => $assets) {
                foreach ($assets as $asset) {
                    if (is_object($asset)) {
                        $obj->assets[$asset->getAssetType()][] = $asset->getAssetURL();
                    }
                }
            }

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
