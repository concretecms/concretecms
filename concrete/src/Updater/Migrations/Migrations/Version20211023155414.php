<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\StyleCustomizer\Style\Legacy\ColorStyle;
use Concrete\Core\StyleCustomizer\Style\FontFamilyStyle;
use Concrete\Core\StyleCustomizer\Style\FontStyleStyle;
use Concrete\Core\StyleCustomizer\Style\FontWeightStyle;
use Concrete\Core\StyleCustomizer\Style\Legacy\ImageStyle;
use Concrete\Core\StyleCustomizer\Style\Legacy\SizeStyle;
use Concrete\Core\StyleCustomizer\Style\PresetFontsFileStyle;
use Concrete\Core\StyleCustomizer\Style\StyleValue;
use Concrete\Core\StyleCustomizer\Style\TextDecorationStyle;
use Concrete\Core\StyleCustomizer\Style\TextTransformStyle;
use Concrete\Core\StyleCustomizer\Style\Legacy\TypeStyle;
use Concrete\Core\StyleCustomizer\Style\Value\BasicValue;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\StyleCustomizer\Style\Value\FontFamilyValue;
use Concrete\Core\StyleCustomizer\Style\Value\FontStyleValue;
use Concrete\Core\StyleCustomizer\Style\Value\FontWeightValue;
use Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Concrete\Core\StyleCustomizer\Style\Value\PresetFontsFileValue;
use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Concrete\Core\StyleCustomizer\Style\Value\TextDecorationValue;
use Concrete\Core\StyleCustomizer\Style\Value\TextTransformValue;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20211023155414 extends AbstractMigration implements RepeatableMigrationInterface
{

    private function cleanDataArray($data)
    {
        // See https://stackoverflow.com/questions/11847751/how-to-convert-cast-object-to-array-without-class-name-prefix-in-php
        // Totally insane.
        $return = [];
        foreach ($data as $k => $v) {
            $k = preg_match('/^\x00(?:.*?)\x00(.+)/', $k, $matches) ? $matches[1] : $k;
            if (is_array($v)) {
                $return[$k] = $this->cleanDataArray($v);
            } else {
                $return[$k] = $v;
            }
        }
        return $return;
    }

    private function objectToArray($obj)
    {
        //only process if it's an object or array being passed to the function
        if (is_object($obj) || is_array($obj)) {
            $ret = (array)$obj;
            foreach ($ret as &$item) {
                //recursively process EACH element regardless of type
                $item = $this->objectToArray($item);
            }
            return $ret;
        } //otherwise (i.e. for scalar values) return without modification
        else {
            return $obj;
        }
    }

    protected function upgradeColorValue(array $legacyValueValueData)
    {
        $value = new ColorValue();
        $value->setRed($legacyValueValueData['r']);
        $value->setGreen($legacyValueValueData['g']);
        $value->setBlue($legacyValueValueData['b']);
        if ($legacyValueValueData['a']) {
            $value->setAlpha($legacyValueValueData['a']);
        } else {
            $value->setAlpha(1);
        }
        return $value;
    }

    protected function upgradeSizeValue(array $legacyValueValueData)
    {
        $value = new SizeValue($legacyValueValueData['size'], $legacyValueValueData['unit']);
        return $value;
    }

    protected function upgradeImageValue(array $legacyValueValueData)
    {
        $value = new ImageValue();
        $value->setImageURL($legacyValueValueData['imageUrl']);
        if (intval($legacyValueValueData['fID'])) {
            $value->setImageFileID(intval($legacyValueValueData['fID']));
        }
        return $value;
    }

    protected function upgradeTypeValue(string $variable, array $legacyValueValueData)
    {
        $value = new TypeValue();
        if (is_array($legacyValueValueData['color'])) {
            $colorStyle = new ColorStyle();
            $colorStyle->setVariable($variable . '-type');
            $colorValue = $this->upgradeColorValue($legacyValueValueData['color']);
            $value->addSubStyleValue(new StyleValue($colorStyle, $colorValue));
        }
        if (is_array($legacyValueValueData['fontSize'])) {
            $sizeStyle = new SizeStyle();
            $sizeStyle->setVariable($variable . '-type');
            $sizeValue = $this->upgradeSizeValue($legacyValueValueData['fontSize']);
            $value->addSubStyleValue(new StyleValue($sizeStyle, $sizeValue));
        }
        if (is_string($legacyValueValueData['fontFamily'])) {
            $fontFamilyStyle = new FontFamilyStyle();
            $fontFamilyStyle->setVariable($variable . '-type-font-family');
            $fontFamilyValue = new FontFamilyValue($legacyValueValueData['fontFamily']);
            $value->addSubStyleValue(new StyleValue($fontFamilyStyle, $fontFamilyValue));
        }
        if (is_string($legacyValueValueData['fontStyle'])) {
            $fontStyleStyle = new FontStyleStyle();
            $fontStyleStyle->setVariable($variable . '-type-font-style');
            $fontStyleValue = new FontStyleValue($legacyValueValueData['fontStyle']);
            $value->addSubStyleValue(new StyleValue($fontStyleStyle, $fontStyleValue));
        }
        if (is_string($legacyValueValueData['textDecoration'])) {
            $textDecorationStyle = new TextDecorationStyle();
            $textDecorationStyle->setVariable($variable . '-type-text-decoration');
            $textDecorationValue = new TextDecorationValue($legacyValueValueData['textDecoration']);
            $value->addSubStyleValue(new StyleValue($textDecorationStyle, $textDecorationValue));
        }
        if (is_string($legacyValueValueData['textTransform'])) {
            $textTransformStyle = new TextTransformStyle();
            $textTransformStyle->setVariable($variable . '-type-text-transform');
            $textTransformValue = new TextTransformValue($legacyValueValueData['textTransform']);
            $value->addSubStyleValue(new StyleValue($textTransformStyle, $textTransformValue));
        }
        if (is_string($legacyValueValueData['fontWeight'])) {
            $fontWeightStyle = new FontWeightStyle();
            $fontWeightStyle->setVariable($variable . '-type-font-weight');
            $fontWeightValue = new FontWeightValue($legacyValueValueData['fontWeight']);
            $value->addSubStyleValue(new StyleValue($fontWeightStyle, $fontWeightValue));
        }


        // line height value - ignored
        // letterspacing value - ignored
        return $value;
    }

    public function upgradeDatabase()
    {
        $this->output(t('Upgrading any existing theme customizer values...'));
        $sm = $this->connection->getSchemaManager();
        if ($sm->tablesExist(['StyleCustomizerValues']) && !$sm->tablesExist(['_StyleCustomizerValues'])) {
            $sm->renameTable('StyleCustomizerValues', '_StyleCustomizerValues');
            $this->output(t('Backing up style customizer values...'));
            $this->refreshDatabaseTables(['StyleCustomizerValues']);
        }

        if ($sm->tablesExist(['_StyleCustomizerValues'])) {
            $this->output(t('Reading style customizer lists from backup location...'));
            $r = $this->connection->executeQuery('select * from _StyleCustomizerValues order by scvID asc');
            $legacyValues = $r->fetchAllAssociative();
            if (count($legacyValues) > 0) {
                $this->output(t('Found %s values', count($legacyValues)));
                foreach ($legacyValues as $legacyValue) {
                    $legacyValueValue = unserialize($legacyValue['value']);
                    // This will turn the legacy value into a value object, but it's not in the format we can use
                    // So let's turn that value object into an array we can work with
                    // Yes, I know this is a huge hack.
                    $legacyValueValueData = $this->cleanDataArray($this->objectToArray($legacyValueValue));
                    if ($legacyValueValue instanceof ColorValue) {
                        $style = new ColorStyle();
                        $upgradedValue = $this->upgradeColorValue($legacyValueValueData);
                        $this->output(t('Color style value %s found. Upgrading...', $legacyValue['scvID']));
                    }
                    if ($legacyValueValue instanceof SizeValue) {
                        $style = new SizeStyle();
                        $upgradedValue = $this->upgradeSizeValue($legacyValueValueData);
                        $this->output(t('Size style value %s found. Upgrading...', $legacyValue['scvID']));
                    }
                    if ($legacyValueValue instanceof ImageValue) {
                        $style = new ImageStyle();
                        $upgradedValue = $this->upgradeImageValue($legacyValueValueData);
                        $this->output(t('Image style value %s found. Upgrading...', $legacyValue['scvID']));
                    }
                    if ($legacyValueValue instanceof TypeValue) {
                        $style = new TypeStyle();
                        $upgradedValue = $this->upgradeTypeValue(
                            $legacyValueValueData['variable'],
                            $legacyValueValueData
                        );
                        $this->output(t('Type style value %s found. Upgrading...', $legacyValue['scvID']));
                    }
                    if ($legacyValueValue instanceof BasicValue) {
                        $style = new PresetFontsFileStyle();
                        $upgradedValue = new PresetFontsFileValue("'" . $legacyValueValueData['value'] . "'");
                        $this->output(t('Preset font file value %s found. Upgrading...', $legacyValue['scvID']));
                    }
                    $style->setVariable($legacyValueValueData['variable']);
                    $styleValue = new StyleValue($style, $upgradedValue);
                    $existingID = $this->connection->fetchOne('select scvID from StyleCustomizerValues where scvID = ?', [$legacyValue['scvID']]);
                    if ($existingID) {
                        $this->output(t('Customizer style ID %s already found in StyleCustomizerValues. Skipping insert...', $legacyValue['scvID']));
                    } else {
                        $this->output(t('Inserting upgraded custom style %s...', $legacyValue['scvID']));
                        $this->connection->insert('StyleCustomizerValues', [
                            'scvID' => $legacyValue['scvID'],
                            'scvlID' => $legacyValue['scvlID'],
                            'value' => serialize($styleValue)
                        ]);
                    }
                }
            } else {
                $this->output(t('No legacy values requiring upgrade were found.'));
            }
        }
    }

}
