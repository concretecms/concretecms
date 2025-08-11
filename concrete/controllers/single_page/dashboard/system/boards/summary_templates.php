<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Boards;

use Concrete\Core\Entity\Summary\Category;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RedirectResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Logging\EchoSQLLogger;

class SummaryTemplates extends DashboardPageController
{

    public function view(): void
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $this->set('templates',
            $this->entityManager->getRepository(Template::class)->findAll()
        );
        $this->set('categories', $categories);

        $categoryTemplates = [];
        foreach ($categories as $category) {
            $categoryTemplates[$category->getId()] = [];
            foreach ($category->getTemplates() as $template) {
                $categoryTemplates[$category->getId()][] = $template->getId();
            }
        }
        $this->set('categoryTemplates', $categoryTemplates);
    }

    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $categories = $this->entityManager->getRepository(Category::class)->findAll();
            $templates = $this->entityManager->getRepository(Template::class)->findAll();
            foreach ($categories as $category) {
                $category->getTemplates()->clear();
                $this->entityManager->persist($category);
            }
            foreach ($templates as $template) {
                $template->getCategories()->clear();
                $this->entityManager->persist($template);
            }

            $this->entityManager->flush();

            foreach ($this->request->request->get('category_template') as $categoryId => $templates) {
                $category = $this->entityManager->find(Category::class, $categoryId);
                if ($category) {
                    $templateObjects = [];
                    foreach ($templates as $templateId) {
                        $template = $this->entityManager->find(Template::class, $templateId);
                        if ($template) {
                            $templateObjects[] = $template;
                        }
                    }
                    foreach ($templateObjects as $templateObject) {
                        $templateObject->getCategories()->add($category);
                        $category->getTemplates()->add($templateObject);
                        $this->entityManager->persist($templateObject);
                        $this->entityManager->persist($category);
                    }
                }
            }

            $this->entityManager->flush();
            $this->flash('success', t('Available templates saved.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }
}
