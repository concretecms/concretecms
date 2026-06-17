<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20241115093300 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->output(t('Updating table: %s', 'Pages'));
        $this->connection->executeStatement(
            <<<'EOT'
UPDATE
    Pages AS ExternalLinkPages
    INNER JOIN Pages AS ParentPages ON ExternalLinkPages.cParentID = ParentPages.cID
SET
    ExternalLinkPages.siteTreeID = ParentPages.siteTreeID
WHERE
    -- Apply to external links only
    COALESCE(ExternalLinkPages.cPointerExternalLink, '') <> ''
    -- Apply only to external links with wrong site tree
    AND ExternalLinkPages.siteTreeID <> ParentPages.siteTreeID
    -- This shouldn't be needed, but it doesn't hurt
    AND COALESCE(ParentPages.siteTreeID, 0) > 0
EOT
        );
    }
}
