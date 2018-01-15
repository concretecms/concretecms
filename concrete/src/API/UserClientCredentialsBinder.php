<?php
namespace Concrete\Core\API;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\Router;
use Concrete\Core\Http\Middleware\OAuthMiddleware;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Database\Connection\Connection;
use PortlandLabs\LibertaConnector\Entity\UserClientCredentials;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserClientCredentialsBinder
{

    protected $db;

    public function __construct(Connection $connection)
    {
        $this->db = $connection;
    }

    public function getCredentialsForUser(UserInfo $ui)
    {
        $r = $this->db->fetchAssoc('select * from OAuthServerClients where user_id = ?', [$ui->getUserID()]);
        if ($r) {
            $credentials = new UserClientCredentials(
                $ui->getEntityObject(), $r['client_id'], $r['client_secret']
            );
            return $credentials;
        }
    }
    
    public function bindUser(UserInfo $ui, $clientId, $clientSecret)
    {
        $this->db->beginTransaction();
        $this->db->delete('OAuthServerClients', ['user_id' => $ui->getUserID()]);
        $this->db->insert('OAuthServerClients', [
            'user_id' => $ui->getUserID(),
            'grant_types' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ]);
        $this->db->commit();
    }

}