<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Attributes;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\PendingType;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Controller\DashboardPageController;

class Types extends DashboardPageController
{
    public function add_attribute_type()
    {
        $pat = PendingType::getByHandle($this->post('atHandle'));
        $pat = Type::add($pat->getAttributeTypeHandle(), $pat->getAttributeTypeName());
        $this->redirect('dashboard/system/attributes/types', 'saved', 'attribute_type_added');
    }

    public function save_attribute_type_associations()
    {
        if (!$this->token->validate('save_attribute_type_associations')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            return;
        }

        $manager = \ORM::entityManager();
        $list = Category::getList();
        foreach ($list as $cat) {
            $cat->clearAttributeKeyCategoryTypes();
            if (is_array($this->post($cat->getAttributeKeyCategoryHandle()))) {
                foreach ($this->post($cat->getAttributeKeyCategoryHandle()) as $id) {
                    $type = Type::getByID($id);
                    $cat->getAttributeTypes()->add($type);
                }
            }
            $this->entityManager->persist($cat);
        }
        $this->entityManager->flush();
        $this->redirect('dashboard/system/attributes/types', 'saved', 'associations_updated');
    }

    public function saved($mode = false)
    {
        if ($mode != false) {
            switch ($mode) {
                case 'associations_updated':
                    $this->set('message', t('Attribute Type Associations saved.'));
                    break;
                case 'attribute_type_added':
                    $this->set('message', t('Attribute Type added.'));
                    break;
            }
        }
    }
}
