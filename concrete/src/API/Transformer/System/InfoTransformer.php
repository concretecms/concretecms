<?php
namespace Concrete\Core\API\Transformer\System;

use Concrete\Core\System\Info;
use League\Fractal\TransformerAbstract;

class InfoTransformer extends TransformerAbstract
{

    public function transform(Info $info)
    {
        return [
            'version' => $info->getVersionInstalled(),
            'code_version' => $info->getCodeVersion(),
            'db_version' => $info->getDbVersion(),
            'packages' => $info->getPackages(),
            'overrides' => $info->getOverrides(),
            'cache' => $info->getCache(),
            'server' => $info->getServerSoftware(),
            'server_api' => $info->getServerAPI(),
            'php_version' => $info->getPhpVersion(),
            'php_extensions' => $info->getPhpExtensions(),
            'php_settings' => $info->getPhpSettings(),
        ];
    }

}
