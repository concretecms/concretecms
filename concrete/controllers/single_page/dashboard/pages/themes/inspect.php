<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Themes;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Theme\Theme;
use Loader;
use PageTheme;
use Package;
use PageTemplate;
use Exception;

class Inspect extends DashboardPageController
{
    protected $helpers = array('html');

    public function on_before_render()
    {
        parent::on_before_render();
        $this->set('pageTitle', t('Page Templates in Theme'));
    }

    // grab all the page types from within a theme
    public function view($pThemeID = null, $message = false)
    {
        if (!$pThemeID) {
            $this->redirect('/dashboard/pages/themes/');
        }

        $v = Loader::helper('validation/error');
        $pt = PageTheme::getByID($pThemeID);
        if (is_object($pt)) {
            $files = $pt->getFilesInTheme();
            $this->set('files', $files);
            $this->set('pThemeID', $pThemeID);
            $this->set('pageTheme', $pt);
        } else {
            $v->add('Invalid Theme');
        }

        switch ($message) {
            case 'install':
                $this->set(
                    'message',
                    t(
                        "Theme installed. You may automatically create page templates from template files contained in your theme using the form below."
                    )
                );
                break;
            case 'activate':
                $this->set(
                    'message',
                    t(
                        "Theme activated. You may automatically create page templates from template files contained in your theme using the form below."
                    )
                );
                break;
        }

        if ($v->has()) {
            $this->set('error', $v);
        }

        $this->set('disableThirdLevelNav', true);
    }

    public function activate_files($pThemeID)
    {
        try {
            $pt = PageTheme::getByID($pThemeID);
            $txt = Loader::helper('text');
            if (!is_array($this->post('pageTemplates'))) {
                throw new Exception(t("You must specify at least one template to create."));
            }

            $pkg = false;
            $pkgHandle = $pt->getPackageHandle();
            if ($pkgHandle) {
                $pkg = Package::getByHandle($pkgHandle);
            }

            foreach ($this->post('pageTemplates') as $pTemplateHandle) {
                $pTemplateName = $txt->unhandle($pTemplateHandle);
                $pTemplateIcon = $pTemplateHandle . '.png';
                if (!file_exists(DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $pTemplateIcon)) {
                    $pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON;
                }
                $ct = PageTemplate::add($pTemplateHandle, $pTemplateName, $pTemplateIcon, $pkg);
            }
            $this->set('success', t('Files in the theme were activated successfully.'));
        } catch (Exception $e) {
            $this->set('error', $e);
        }
        $this->view($pThemeID);
    }
}
