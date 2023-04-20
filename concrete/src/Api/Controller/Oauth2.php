<?php

namespace Concrete\Core\Api\Controller;

/**
 * @OA\Info(title="Concrete CMS API", version="1.0")
 * @OA\SecurityScheme(
 *     securityScheme="clientCredentials",
 *     type="oauth2",
 *     @OA\Flow(
 *         flow="clientCredentials",
 *         tokenUrl="/oauth/2.0/token",
 *         scopes={
 *             "system:info:read": "Read system information",
 *             "sites:read": "Read sites",
 *         }
 *     )

 * )
 * @OA\SecurityScheme(
 *     securityScheme="authorization",
 *     type="oauth2",
 *     @OA\Flow(
 *         authorizationUrl="/oauth/2.0/authorize",
 *         tokenUrl="/oauth/2.0/token",
 *         flow="authorizationCode",
 *         scopes={
 *             "openid": "Remotely authenticate into Concrete",
 *             "account:read": "Read your user object",
 *             "calendars:read": "Read calendars",
 *             "calendar_events:read": "Read calendar events",
 *             "files:read": "Read files as your user",
 *             "files:add": "Add files",
 *             "files:update": "Update files",
 *             "files:delete": "Delete files",
 *             "pages:read": "View site pages",
 *             "pages:add": "Add pages",
 *             "pages:update": "Update pages",
 *             "pages:delete": "Delete pages",
 *             "pages:areas:add_block": "Add blocks to a page area",
 *             "pages:areas:delete_block": "Delete blocks from a page area",
 *             "pages:areas:update_block": "Updates a block in a page area",
 *             "pages:versions:read": "View page versions",
 *             "pages:versions:update": "Update page versions",
 *             "pages:versions:delete": "Delete page versions",
 *             "blocks:read": "View site blocks",
 *             "blocks:update": "Update blocks",
 *             "blocks:delete": "Delete blocks",
 *             "users:read": "Views users in the site",
 *             "users:add": "Add users",
 *             "users:update": "Update users",
 *             "users:delete": "Delete users",
 *             "groups:read": "Views groups in the site",
 *             "groups:add": "Add groups",
 *         }
 *     )
 * )
 */
class Oauth2
{



}
