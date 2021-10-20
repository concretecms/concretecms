<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\PresetFontsFileStyle;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Style\Value\PresetFontsFileValue;

class ApplyCustomizationsToSiteCommandHandler
{

    /**
     * @var StyleValueListFactory
     */
    protected $styleValueListFactory;

    /**
     * @var NormalizedVariableCollectionFactory
     */
    protected $variableCollectionFactory;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @param StyleValueListFactory $styleValueListFactory
     * @param NormalizedVariableCollectionFactory $variableCollectionFactory
     */
    public function __construct(
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory,
        Connection $db
    ) {
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->db = $db;
    }

    protected function populateData($theme, $command)
    {
        $customizer = $theme->getThemeCustomizer();
        $preset = $customizer->getPresetByIdentifier($command->getPresetStartingPoint());
        $styles = $command->getStyles();
        $styleValueList = $this->styleValueListFactory->createFromRequestArray(
            $customizer->getThemeCustomizableStyleList($preset),
            $styles
        );

        // Get the preset font file
        $type = $customizer->getType();
        $presetFile = $type->getPresetType()->getVariablesFile($preset);
        $fileVariableCollection = $type->getVariableNormalizer()->createVariableCollectionFromFile($presetFile);
        $presetFontsFileVariable = $fileVariableCollection->getVariable('preset-fonts-file');
        if ($presetFontsFileVariable) {
            $styleValueList->add(new StyleValue(new PresetFontsFileStyle(), new PresetFontsFileValue($presetFontsFileVariable->getValue())));
        }

        // This is brutal but it's done this way for backward compatibility
        $db = $this->db;
        $db->beginTransaction();
        $db->insert('StyleCustomizerValueLists', []);
        $scvlID = $db->LastInsertId();

        foreach ($styleValueList->getValues() as $value) {
            $db->insert('StyleCustomizerValues', ['value' => serialize($value), 'scvlID' => $scvlID]);
        }
        $db->commit();

        $sccRecordID = 0;
        if ($command->getCustomCss()) {
            $record = new CustomCssRecord();
            $record->setValue($command->getCustomCss());
            $record->save();
            $sccRecordID = $record->getRecordID();
        }

        return [$scvlID, $sccRecordID];
    }

    public function __invoke(ApplyCustomizationsToSiteCommand $command)
    {
        $db = $this->db;
        $db->delete('PageThemeCustomStyles', ['pThemeID' => $command->getThemeID()]);

        $theme = Theme::getByID($command->getThemeID());
        list($scvlID, $sccRecordID) = $this->populateData($theme, $command);

        $db->insert(
            'PageThemeCustomStyles',
            [
                'pThemeID' => $command->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $command->getPresetStartingPoint(),
                'scvlID' => $scvlID,
            ]
        );

        // now we reset all cached css files in this theme
        $sheets = $theme->getThemeCustomizableStyleSheets();
        foreach ($sheets as $s) {
            $s->clearOutputFile();
        }
    }


}