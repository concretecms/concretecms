<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Theme\Command\ApplyCustomizationsToSiteCommand;
use Concrete\Core\Page\Theme\Command\CreateCustomSkinCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Command\DeleteCustomSkinCommand;
use Concrete\Core\Page\Theme\Command\SkinCommandValidator;
use Concrete\Core\Page\Theme\Command\UpdateCustomSkinCommand;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\User\User;

class CustomizePreset extends BackendInterfaceController
{
    protected $viewPath = '/panels/theme/customize';
    protected $controllerActionPath = '/panels/theme/customize';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    protected function loadPreviewPage($previewPageID)
    {
        $previewPage = Page::getByID($previewPageID);
        $checker = new Checker($previewPage);
        if ($checker->canViewPage()) {
            $this->set('previewPage', $previewPage);
        } else {
            throw new \RuntimeException(t('Unable to preview page: %s', $previewPage->getCollectionID()));
        }
    }

    protected function getCustomizer($pThemeID)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $customizer = $theme->getThemeCustomizer();
            if ($customizer) {
                return $customizer;
            } else {
                throw new \RuntimeException(t('Theme %s cannot be customized', $theme->getThemeHandle()));
            }
        } else {
            throw new \RuntimeException(t('Invalid theme: %s', h($pThemeID)));
        }
    }

    public function view($pThemeID, $presetIdentifier, $previewPageID)
    {
        $this->loadPreviewPage($previewPageID);
        $customizer = $this->getCustomizer($pThemeID);
        $preset = $customizer->getPresetByIdentifier($presetIdentifier);
        if ($preset) {
            $styleList = $customizer->getThemeCustomizableStyleList($preset);
            $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
            $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
            $customizerVariableCollectionFactory = $this->app->make(CustomizerVariableCollectionFactory::class);
            $variableCollection = $variableCollectionFactory->createFromPreset($customizer, $preset);
            $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
            $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
            $this->set('styleList', $groupedStyleValueList);
            $this->set('styles', $customizerVariableCollectionFactory->createFromStyleValueList($valueList));
            $this->set('preset', $preset);
            $this->set('pThemeID', $pThemeID);
            $this->set('presetIdentifier', $presetIdentifier);
            $this->set('customizer', $customizer);
            $this->requireAsset('ace');
        } else {
            throw new \RuntimeException(t('Invalid preset: %s', h($presetIdentifier)));
        }
    }

    public function viewSkin($pThemeID, $skinIdentifier, $previewPageID)
    {
        $this->loadPreviewPage($previewPageID);
        $customizer = $this->getCustomizer($pThemeID);
        $skin = $customizer->getTheme()->getSkinByIdentifier($skinIdentifier);
        if ($skin) {
            /**
             * @var $skin CustomSkin
             */
            $preset = $customizer->getPresetByIdentifier($skin->getPresetStartingPoint());
            $styleList = $customizer->getThemeCustomizableStyleList($preset);
            $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
            $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
            $customizerVariableCollectionFactory = $this->app->make(CustomizerVariableCollectionFactory::class);
            $variableCollection = $variableCollectionFactory->createFromCustomSkin($skin);
            $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
            $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
            $this->set('styleList', $groupedStyleValueList);
            $this->set('styles', $customizerVariableCollectionFactory->createFromStyleValueList($valueList));
            $this->set('preset', $preset);
            $this->set('pThemeID', $pThemeID);
            $this->set('skinIdentifier', $skinIdentifier);
            $this->requireAsset('ace');
        } else {
            throw new \RuntimeException(t('Invalid skin: %s', h($skinIdentifier)));
        }
    }

    /**
     * Controller method run when a preset skin is saved as a new skin, with or without customizations.
     * Preset skins cannot be modified, they can only be saved as custom skins.
     *
     * @param $pThemeID
     * @param $skinIdentifier
     * @return mixed
     */
    public function createSkin($pThemeID, $presetIdentifier)
    {
        if ($this->app->make('token')->validate()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $customizer = $theme->getThemeCustomizer();
                $preset = $customizer->getPresetByIdentifier($presetIdentifier);
                if ($preset) {
                    $styles = $this->request->request->get('styles');
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $styleValueList = $styleValueListFactory->createFromRequestArray(
                        $customizer->getThemeCustomizableStyleList($preset),
                        $styles
                    );
                    $collection = $variableCollectionFactory->createFromStyleValueList($styleValueList);

                    $responseFactory = $this->app->make(ResponseFactory::class);
                    $validator = $this->app->make(SkinCommandValidator::class);

                    $error = $validator->validate($this->request->request->get('skinName'), $theme);
                    if ($error->has()) {
                        return $responseFactory->json($error);
                    } else {
                        $u = $this->app->make(User::class);
                        $command = new CreateCustomSkinCommand();
                        $command->setAuthorID($u->getUserID());
                        $command->setThemeID($theme->getThemeID());
                        $command->setPresetStartingPoint($presetIdentifier);
                        $command->setSkinName($this->request->request->get('skinName'));
                        $command->setCustomCss($this->request->request->get('customCss'));
                        $command->setVariableCollection($collection);

                        $skin = $this->app->executeCommand($command);

                        return $responseFactory->json(
                            [
                                'created' => true,
                                'skin' => $skin
                            ]
                        );
                    }
                }
            }
            throw new \RuntimeException(t('Invalid theme ID or skin identifier.'));
        }
        throw new UserMessageException(t('Access Denied'));
    }

    /**
     * Controller method run when a preset is used with the legacy customizer or other customizer that does not
     * suppport skins.
     *
     * @param $pThemeID
     * @param $skinIdentifier
     * @return mixed
     */
    public function saveGlobalStyles($pThemeID, $presetIdentifier)
    {
        if ($this->app->make('token')->validate()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $customizer = $theme->getThemeCustomizer();
                $preset = $customizer->getPresetByIdentifier($presetIdentifier);
                if ($preset) {

                    $command = new ApplyCustomizationsToSiteCommand();
                    $command->setThemeID($theme->getThemeID());
                    $command->setPresetStartingPoint($presetIdentifier);
                    if ($this->request->request->has('customCss')) {
                        $command->setCustomCss($this->request->request->get('customCss'));
                    }
                    $command->setStyles($this->request->request->get('styles'));
                    $this->app->executeCommand($command);

                    $responseFactory = $this->app->make(ResponseFactory::class);
                    return $responseFactory->json(['applied' => true]);
                }
            }
            throw new \RuntimeException(t('Invalid theme ID or skin identifier.'));
        }
        throw new UserMessageException(t('Access Denied'));
    }



    /**
     * Controller method run when custom skins are saved. Custom skins can have custom variables, then be updated
     * and tweaked and re-saved as necessary.
     *
     * @param $pThemeID
     * @param $skinIdentifier
     * @return mixed
     */
    public function save($pThemeID, $skinIdentifier)
    {
        if ($this->app->make('token')->validate()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {
                    $customizer = $theme->getThemeCustomizer();
                    $preset = $customizer->getPresetByIdentifier($skin->getPresetStartingPoint());
                    $styles = $this->request->request->get('styles');
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $styleValueList = $styleValueListFactory->createFromRequestArray(
                        $customizer->getThemeCustomizableStyleList($preset),
                        $styles
                    );
                    $collection = $variableCollectionFactory->createFromStyleValueList($styleValueList);

                    $responseFactory = $this->app->make(ResponseFactory::class);

                    $command = new UpdateCustomSkinCommand();
                    $command->setCustomSkin($skin);
                    $command->setVariableCollection($collection);
                    $command->setCustomCss($this->request->request->get('customCss'));

                    $skin = $this->app->executeCommand($command);

                    return $responseFactory->json(
                        [
                            'updated' => true,
                            'skin' => $skin
                        ]
                    );
                }
            }
            throw new \RuntimeException(t('Invalid theme ID or skin identifier.'));
        }
        throw new UserMessageException(t('Access Denied'));
    }

    /**
     * Method run when deleting a theme.
     *
     * @param $pThemeID
     * @param $skinIdentifier
     * @return mixed
     */
    public function delete($pThemeID, $skinIdentifier)
    {
        if ($this->app->make('token')->validate()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {

                    $responseFactory = $this->app->make(ResponseFactory::class);
                    $command = new DeleteCustomSkinCommand();
                    $command->setCustomSkin($skin);
                    $this->app->executeCommand($command);

                    return $responseFactory->json(
                        [
                            'delete' => true,
                            'skin' => $skin
                        ]
                    );
                }
            }
            throw new \RuntimeException(t('Invalid theme ID or skin identifier.'));
        }
        throw new UserMessageException(t('Access Denied'));
    }


}
