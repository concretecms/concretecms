<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Page\Theme\AvailableVariablesUpdater;
use Concrete\Core\Page\Theme\Theme;

class FixThemeValuesCommand extends Command
{
    protected $canRunAsRoot = true;

    protected $description = 'Fix the theme customizable values.';

    protected $signature = <<<'EOT'
c5:theme:values:fix
    {theme? : the handle of the theme to be fixed - if not specified we'll fix all the installed themes}
    {--s|simulate : Simulate changes}
    {--a|add : Add missing variables}
    {--u|update : Update existing variables}
    {--e|remove-invalid : Remove invalid variables}
    {--d|remove-duplicates : Remove duplicated variables}
    {--r|remove-unused : Remove no-more used variables}
EOT
    ;

    public function handle(AvailableVariablesUpdater $updater)
    {
        $themeHandle = $this->argument('theme');
        $flags = 0
            | ($this->option('remove-invalid') ? AvailableVariablesUpdater::FLAG_REMOVE_INVALID : 0)
            | ($this->option('remove-duplicates') ? AvailableVariablesUpdater::FLAG_REMOVE_DUPLICATED : 0)
            | ($this->option('remove-unused') ? AvailableVariablesUpdater::FLAG_REMOVE_UNUSED : 0)
            | ($this->option('add') ? AvailableVariablesUpdater::FLAG_ADD : 0)
            | ($this->option('update') ? AvailableVariablesUpdater::FLAG_UPDATE : 0)
        ;
        if ($flags === 0) {
            $this->output->error('Please specify at least one operation to be performed.');

            return 1;
        }
        if ($this->option('simulate')) {
            $flags |= AvailableVariablesUpdater::FLAG_SIMULATE;
        }
        if ($themeHandle === null) {
            $fixResults = $updater->fixThemes($flags);
            if (!$this->output->isQuiet()) {
                foreach ($fixResults as $themeHandle => $fixResult) {
                    $this->writeResult($themeHandle, $fixResult);
                }
            }
        } else {
            $theme = Theme::getByHandle($themeHandle);
            if ($theme === null) {
                $error = "Invalid theme handle: {$themeHandle}\n";
                $installedThemes = Theme::getInstalledHandles();
                if ($installedThemes === []) {
                    $error .= 'No installed themes found.';
                } else {
                    $error .= "Available themes are:\n- " . implode("\n- ", $installedThemes);
                }
                $this->output->error($error);

                return -1;
            }
            $fixResult = $updater->fixTheme($theme, $flags);
            if (!$this->output->isQuiet()) {
                $this->writeResult($theme->getThemeHandle(), $fixResult);
            }
        }

        return 0;
    }

    private function writeResult($themeHandle, AvailableVariablesUpdater\Result $fixResult)
    {
        $this->output->writeln("## Theme {$themeHandle}");
        $this->output->writeln((string) $fixResult);
    }
}
