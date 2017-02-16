<?php
namespace Concrete\Core\Csv;

use Concrete\Core\Csv\Strategy\EntryListStrategy;
use Concrete\Core\Csv\Strategy\UserResultsStrategy;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Port\Csv\CsvWriter;

class CsvServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Construct CSV from an EntryList object
        $this->app->bind('helper/csv/entry_list', function ($app, array $parameters) {
            $entryListStrategy = new EntryListStrategy($parameters[0]);
            $csvWriter = new CsvWriter(',');
            $csvService = new Csv($csvWriter, $entryListStrategy, $parameters[1]);

            return $csvService;
        });

        // Construct CSV from a UserList object
        $this->app->bind('helper/csv/user_list', function ($app, array $parameters) {
            $userList = new UserResultsStrategy($parameters[0]);
            $csvWriter = new CsvWriter(',');
            $csvService = new Csv($csvWriter, $userList, $parameters[1]);

            return $csvService;
        });
    }
}
