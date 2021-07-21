<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Theme\Command\CreateCustomSkinCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Command\DeleteCustomSkinCommand;
use Concrete\Core\Page\Theme\Command\SkinCommandValidator;
use Concrete\Core\Page\Theme\Command\UpdateCustomSkinCommand;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\CustomizerVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\WebFont\WebFontCollectionFactory;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomizeSkin extends BackendInterfaceController
{
    protected $viewPath = '/panels/theme/customize';
    protected $controllerActionPath = '/panels/theme/customize';

    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/pages/themes');
        $checker = new Checker($page);
        return $checker->canViewPage();
    }

    public function view($pThemeID, $skinIdentifier, $previewPageID)
    {
        $this->requireAsset('ace');
        $previewPage = Page::getByID($previewPageID);
        $checker = new Checker($previewPage);
        if ($checker->canViewPage()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {
                    $styleList = $theme->getThemeCustomizableStyleList($skin);
                    $adapterFactory = $this->app->make(AdapterFactory::class);
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $customizerVariableCollectionFactory = $this->app->make(CustomizerVariableCollectionFactory::class);
                    $adapter = $adapterFactory->createFromTheme($theme);
                    $variableCollection = $variableCollectionFactory->createVariableCollectionFromSkin($adapter, $skin);
                    $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
                    $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
                    $this->set('styleList', $groupedStyleValueList);
                    $this->set('styles', $customizerVariableCollectionFactory->createFromStyleValueList($valueList));
                    $this->set('skin', $skin);
                    $this->set('pThemeID', $pThemeID);
                    $this->set('skinIdentifier', $skinIdentifier);
                    $this->set('previewPage', $previewPage);
                    return;
                }
            }
        }
        throw new \RuntimeException(t('Invalid theme ID, preview page or skin identifier.'));
    }

    /**
     * Controller method run when a preset skin is saved as a new skin, with or without customizations.
     * Preset skins cannot be modified, they can only be saved as custom skins.
     *
     * @param $pThemeID
     * @param $skinIdentifier
     * @return mixed
     */
    public function create($pThemeID, $skinIdentifier)
    {
        if ($this->app->make('token')->validate()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {
                    $styles = $this->request->request->get('styles');
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $styleValueList = $styleValueListFactory->createFromRequestArray(
                        $theme->getThemeCustomizableStyleList($skin),
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
                        $command->setPresetSkinStartingPoint($skinIdentifier);
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
                    $styles = $this->request->request->get('styles');
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $styleValueList = $styleValueListFactory->createFromRequestArray(
                        $theme->getThemeCustomizableStyleList($skin),
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
