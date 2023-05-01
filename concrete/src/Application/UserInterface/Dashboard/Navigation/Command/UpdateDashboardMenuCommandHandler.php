<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation\Command;

use Concrete\Core\Application\Application;

class UpdateDashboardMenuCommandHandler
{

    /**]
     * @var Application
     */
    protected $app;


    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke(UpdateDashboardMenuCommand $command)
    {
        $menuId = $command->getMenuId();
        $config = $this->app->make('config/database');
        $config->save('app.dashboard_menu', $menuId);
    }
}
