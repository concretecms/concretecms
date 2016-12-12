<?php
namespace Concrete\Core\Logging\Query;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Database;
use Config;
use Core;
use Doctrine\DBAL\Logging\DebugStack;

class Logger
{

    protected $excludedPaths = array(
        '/dashboard/system/optimization/query_log*',
        '/tools/required/*'
    );

    public function clearQueryLog()
    {
        $db = Database::get();
        $db->query('truncate table SystemDatabaseQueryLog');
    }

    public function shouldLogQueries(Request $request)
    {
        $th = Core::make('helper/text');
        foreach($this->excludedPaths as $path) {
            if ($th->fnmatch($path, $request->getPath())) {
                return false;
            }
        }
        return true;
    }

    /**
     * @\Doctrine\DBAL\Logging\DebugStack[] $loggers
     */
    public function write($loggers)
    {
        $db = Database::get();
        foreach($loggers as $stack) {
            if (isset($stack->queries)) {
                foreach($stack->queries as $query) {
                    $params = '';
                    if (is_array($query['params'])) {
                        $params = implode(',', $query['params']);
                    }
                    $db->insert('SystemDatabaseQueryLog', array(
                        'query' => $query['sql'],
                        'params' => $params,
                        'executionMS' => $query['executionMS']
                        )
                    );
                }
            }
        }
    }

    public static function getTotalLogged()
    {
        $db = Database::get();
        return $db->query('select count(*) from SystemDatabaseQueryLog')->fetchColumn();
    }

    public static function getParametersForQuery($query)
    {
        $db = Database::get();
        return $db->GetCol('select params from SystemDatabaseQueryLog where query = ? order by params asc', array($query));
    }
}
