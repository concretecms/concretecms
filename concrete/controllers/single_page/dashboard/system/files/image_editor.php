<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Entity\File\Image\Editor;
use Concrete\Core\ImageEditor\EditorServiceProvider;
use Concrete\Core\Page\Controller\DashboardPageController;

class ImageEditor extends DashboardPageController
{
    /** @var EditorServiceProvider */
    protected $editorService;

    public function on_start()
    {
        parent::on_start();

        $this->editorService = $this->app->make(EditorServiceProvider::class);
    }

    public function view()
    {
        if ($this->request->getMethod() === "POST") {
            if ($this->token->validate("save_editor_settings")) {
                $editor = $this->editorService->getEditorByHandle($this->request->request->get("activeEditor"));

                if ($editor instanceof Editor) {
                    $this->editorService->setActiveEditor($editor);
                }

                $this->set('success', t("The default editor has been successfully changed."));
            } else {
                $this->error->add("Invalid Token.");
            }
        }

        $this->set('editorList', $this->editorService->getEditorList());
        $this->set('activeEditor', $this->editorService->getActiveEditor());
    }
}