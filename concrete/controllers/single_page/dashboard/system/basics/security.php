<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

class Security extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('generatorTag', (bool) $config->get('concrete.misc.generator_tag_display_in_header'));
    }

    public function save()
    {
        if (!$this->token->validate('ccm-perm-sec')) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $config = $this->app->make('config');
            $post = $this->request->request;
            $config->save('concrete.misc.generator_tag_display_in_header', (bool) $post->get('generator_tag_display_in_header'));
        }
        if ($this->error->has()) {
            $this->view();
        } else {
            $this->flash('success', t('Options saved successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/basics/security']),
                302
            );
        }
    }
}
