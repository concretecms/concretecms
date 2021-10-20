<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollectionFactory;
use Concrete\Core\StyleCustomizer\Style\PresetFontsFileStyle;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\StyleValueListFactory;
use Concrete\Core\StyleCustomizer\Style\Value\PresetFontsFileValue;
use Doctrine\ORM\EntityManager;

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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param StyleValueListFactory $styleValueListFactory
     * @param NormalizedVariableCollectionFactory $variableCollectionFactory
     */
    public function __construct(
        StyleValueListFactory $styleValueListFactory,
        NormalizedVariableCollectionFactory $variableCollectionFactory,
        EntityManager $entityManager
    ) {
        $this->styleValueListFactory = $styleValueListFactory;
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ApplyCustomizationsToSiteCommand $command)
    {
        $db = $this->entityManager->getConnection();
        $db->delete('PageThemeCustomStyles', ['pThemeID' => $command->getThemeID()]);

        $theme = Theme::getByID($command->getThemeID());
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