<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Page\Theme\Theme;

class ApplyCustomizationsToPageCommandHandler extends ApplyCustomizationsToSiteCommandHandler
{

    /**
     * @param ApplyCustomizationsToPageCommand $command
     */
    public function __invoke(ApplyCustomizationsToSiteCommand $command)
    {
        $db = $this->db;
        $db->delete('CollectionVersionThemeCustomStyles', [
            'cID' => $command->getPage()->getCollectionID(),
            'cvID' => $command->getPage()->getVersionID()
        ]);

        $theme = Theme::getByID($command->getThemeID());
        list($scvlID, $sccRecordID) = $this->populateData($theme, $command);

        $db->insert(
            'CollectionVersionThemeCustomStyles',
            [
                'cID' => $command->getPage()->getCollectionID(),
                'cvID' => $command->getPage()->getVersionID(),
                'pThemeID' => $command->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $command->getPresetStartingPoint(),
                'scvlID' => $scvlID,
            ]
        );
    }


}