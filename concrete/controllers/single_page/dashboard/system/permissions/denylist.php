<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\IpAccessControlService;
use Punic\Comparer;

class Denylist extends DashboardPageController
{
    /**
     * @var \Doctrine\ORM\EntityRepository|null
     */
    private $categoryRepository;

    public function view()
    {
        if ($this->getCategory() !== null) {
            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->action('configure'),
                302
            );
        }
        $this->addHeaderItem(<<<'EOT'
<style>
tr.ccm_ip-access-control-category {
    cursor: pointer;
}
</style>
EOT
        );
        $categories = $this->getCategoryRepository()->findAll();
        $cmp = new Comparer();
        usort($categories, static function (IpAccessControlCategory $a, IpAccessControlCategory $b) use ($cmp): int {
            return $cmp->compare($a->getDisplayName(), $b->getDisplayName());
        });
        $this->set('categories', $categories);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getCategoryRepository()
    {
        if ($this->categoryRepository === null) {
            $this->categoryRepository = $this->entityManager->getRepository(IpAccessControlCategory::class);
        }

        return $this->categoryRepository;
    }

    /**
     * @param mixed $id
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlCategory|null
     */
    protected function getCategory($id = null)
    {
        if ((string) $id !== '') {
            return $this->entityManager->find(IpAccessControlCategory::class, $id);
        }
        $categories = $this->getCategoryRepository()->findBy([], null, 2);
        if (count($categories) === 1) {
            return $categories[0];
        }

        return null;
    }

    /**
     * @param mixed $id
     *
     * @return \Concrete\Core\Permission\IpAccessControlService|null
     */
    protected function getService($id = null)
    {
        $category = $this->getCategory($id);
        if ($category === null) {
            return null;
        }

        return $this->app->make(IpAccessControlService::class, ['category' => $category]);
    }
}
