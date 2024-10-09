<?php
namespace Concrete\Core\SiteInformation;

use Concrete\Core\Application\Application;
use Concrete\Core\Marketplace\Update\Command\UpdateRemoteDataCommand;
use Concrete\Core\Marketplace\Update\UpdatedField;
use Symfony\Component\HttpFoundation\Request;

final class SiteInformationSaver extends DatabaseConfigSaver
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(array $fieldKeys, Application $app)
    {
        parent::__construct('general', $fieldKeys, $app);
        $this->app = $app;
    }

    public function saveFromRequest(Request $request): void
    {
        parent::saveFromRequest($request);
        $fields = [];
        foreach ($this->fieldKeys as $fieldKey) {
            $fields[] = new UpdatedField($fieldKey, $request->request->get($fieldKey));
        }
        $command = new UpdateRemoteDataCommand($fields);
        $this->app->executeCommand($command);
    }
}
