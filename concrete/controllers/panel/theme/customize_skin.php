<?php
namespace Concrete\Controller\Panel\Theme;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Theme\Command\CreateCustomSkinCommand;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Command\SkinCommandValidator;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Adapter\AdapterFactory;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
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
        $previewPage = Page::getByID($previewPageID);
        $checker = new Checker($previewPage);
        if ($checker->canViewPage()) {
            $theme = Theme::getByID($pThemeID);
            if ($theme) {
                $skin = $theme->getSkinByIdentifier($skinIdentifier);
                if ($skin) {
                    $styleList = $theme->getThemeCustomizableStyleList();
                    $adapterFactory = $this->app->make(AdapterFactory::class);
                    $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                    $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                    $adapter = $adapterFactory->createFromTheme($theme);
                    $variableCollection = $variableCollectionFactory->createVariableCollectionFromSkin($adapter, $skin);
                    $valueList = $styleValueListFactory->createFromVariableCollection($styleList, $variableCollection);
                    $groupedStyleValueList = $valueList->createGroupedStyleValueList($styleList);
                    $this->set('styles', $groupedStyleValueList);
                    $this->set('skins', $skin);
                    $this->set('pThemeID', $pThemeID);
                    $this->set('skinIdentifier', $skinIdentifier);
                    $this->set('previewPage', $previewPage);
                    return;
                }
            }
        }
        throw new \RuntimeException(t('Invalid theme ID, preview page or skin identifier.'));
    }

    public function create($pThemeID, $skinIdentifier)
    {
        $theme = Theme::getByID($pThemeID);
        if ($theme) {
            $skin = $theme->getSkinByIdentifier($skinIdentifier);
            if ($skin) {
                $styles = $this->request->request->get('styles');
                $styleValueListFactory = $this->app->make(StyleValueListFactory::class);
                $variableCollectionFactory = $this->app->make(NormalizedVariableCollectionFactory::class);
                $styleValueList = $styleValueListFactory->createFromRequestArray(
                    $theme->getThemeCustomizableStyleList(),
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


}
