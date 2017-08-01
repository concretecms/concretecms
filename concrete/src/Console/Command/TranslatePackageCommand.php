<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteTranslationProvider;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Gettext\Translations;
use Package;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TranslatePackageCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
        ->setName('c5:package-translate')
        ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to be translated (or the path to a directory containing a concrete5 package)')
        ->addEnvOption()
        ->addOption('locale', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'List of locale codes to handle')
        ->addOption('contact', 'c', InputOption::VALUE_REQUIRED, 'Contact information to be put in the language files to report bugs to (eg the "Report-Msgid-Bugs-To" gettext header)', '')
        ->addOption('translator', 't', InputOption::VALUE_REQUIRED, 'Translator to be put in the language files (eg the "Last-Translator" gettext header)', '')
        ->addOption('exclude-3rdparty', 'x', InputOption::VALUE_NONE, 'Specify this option to avoid parsing 3rd party folders')
        ->addOption('fill', 'f', InputOption::VALUE_NONE, 'Fill-in already known translations using an online service')
        ->setDescription('Creates or updates translations of a concrete5 package')
        ->setHelp(<<<EOT
If the locale option(s) is not specified, we'll generate/update translations for the currently defined locales for the package.
If currently no locale is defined, we'll generate/update translations for all the currently installed locales of the core of concrete5.
In order to don't generate the locale files but only the master translations file (.pot), specify "--locale=-" (or "-l-")
Examples:
    concrete5 c5:package-translate package_handle
    concrete5 c5:package-translate package_handle --locale=it_IT --locale=de_DE --fill
    concrete5 c5:package-translate package_handle -l it_IT -l de_DE
    concrete5 c5:package-translate package_handle --locale=-
    concrete5 c5:package-translate path/to/a/package/directory -l-
            
Please remark that this command can also parse legacy (pre-5.7) packages.
            
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred
            
More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-package-translate
EOT
            )
            ;
    }
    
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = Application::getFacadeApplication();
        
        $vsh = $this->app->make('helper/validation/strings');
        /* @var \Concrete\Core\Utility\Service\Validation\Strings $vsh */
        $fh = $this->app->make('helper/file');
        /* @var \Concrete\Core\File\Service\File $fh */
        
        // Let's determine the package handle, directory and version
        $packageHandle = null;
        $packageDirectory = null;
        $packageVersion = null;
        $p = $input->getArgument('package');
        if (is_dir($p) || !$vsh->handle($p)) {
            $packageDirectory = @realpath($p);
            if ($packageDirectory === false) {
                throw new Exception("Unable to find the directory '$p'");
            }
            $packageDirectory = str_replace(DIRECTORY_SEPARATOR, '/', $packageDirectory);
            list($packageHandle, $packageVersion) = $this->guessPackageDetails($packageDirectory);
            if ($packageHandle === null) {
                $packageHandle = basename($packageDirectory);
            }
        } else {
            foreach (Package::getAvailablePackages(false) as $pkg) {
                if (strcasecmp($p, $pkg->getPackageHandle()) === 0) {
                    $packageHandle = $pkg->getPackageHandle();
                    $packageDirectory = $pkg->getPackagePath();
                    $packageVersion = $pkg->getPackageVersion();
                    break;
                }
            }
            if ($packageHandle === null) {
                throw new Exception("Unable to find a package with handle '$p'");
            }
        }
        $packageLanguagesDirectory = $packageDirectory . '/' . DIRNAME_LANGUAGES;
        
        // Determine the locales to translate
        $locales = [];
        $localeOption = $input->getOption('locale');
        if (in_array('-', $localeOption, true)) {
            // We don't want any locale
        } elseif (empty($localeOption)) {
            // List the currently package locales
            foreach ($fh->getDirectoryContents($packageLanguagesDirectory) as $item) {
                if (is_file("$packageLanguagesDirectory/$item/LC_MESSAGES/messages.mo") || is_file("$packageLanguagesDirectory/$item/LC_MESSAGES/messages.po")) {
                    $locales[] = $item;
                }
            }
            if (empty($locales)) {
                // Let's use the core locales
                $locales = Localization::getAvailableInterfaceLanguages();
            }
        } else {
            // Normalize the locales (eg from it-it to it_IT)
            foreach ($localeOption as $lo) {
                $chunks = [];
                foreach (explode('_', str_replace('-', '_', $lo)) as $index => $chunk) {
                    if ($index === 0) {
                        // Language (eg zh)
                        $chunks[] = strtolower($chunk);
                    } elseif (strlen($chunk) === 4) {
                        // Script (eg Hans)
                        $chunks[] = ucfirst(strtolower($chunk));
                    } else {
                        // Territory (eg CN)
                        $chunks[] = strtoupper($chunk);
                    }
                }
                $locales[] = implode('_', $chunks);
            }
        }
        
        // Initialize the master translations file (.pot)
        $pot = new Translations();
        $pot->setHeader('Project-Id-Version', "$packageHandle $packageVersion");
        $pot->setLanguage(Localization::BASE_LOCALE);
        $pot->setHeader('Report-Msgid-Bugs-To', $input->getOption('contact'));
        $pot->setHeader('Last-Translator', $input->getOption('translator'));
        
        // Parse the package directory
        $output->writeln('Parsing package contents');
        foreach (\C5TL\Parser::getAllParsers() as $parser) {
            if ($parser->canParseDirectory()) {
                $output->write('- running parser "' . $parser->getParserName() . '"... ');
                $parser->parseDirectory(
                    $packageDirectory,
                    "packages/$packageHandle",
                    $pot,
                    false,
                    $input->getOption('exclude-3rdparty')
                    );
                $output->writeln('<info>done.</info>');
            }
        }
        
        // Save the pot file
        $output->write('Saving .pot file... ');
        if (!is_dir($packageLanguagesDirectory)) {
            @mkdir($packageLanguagesDirectory, 0775, true);
            if (!is_dir($packageLanguagesDirectory)) {
                throw new Exception("Unable to create the directory $packageLanguagesDirectory");
            }
        }
        $potFilename = "$packageLanguagesDirectory/messages.pot";
        if ($pot->toPoFile($potFilename) === false) {
            throw new Exception("Unable to save the .pot file to $potFilename");
        }
        $output->writeln('<info>done.</info>');
        
        $remoteTranslationsProvider = $this->app->make(RemoteTranslationProvider::class);
        /* @var RemoteTranslationProvider $remoteTranslationsProvider */
        // Creating/updating the locale files
        foreach ($locales as $locale) {
            $output->writeln("Working on locale $locale");
            $poDirectory = "$packageLanguagesDirectory/$locale/LC_MESSAGES";
            $po = clone $pot;
            $po->setLanguage($locale);
            if ($input->getOption('fill')) {
                $output->write('- fetching remote translations... ');
                $po = $remoteTranslationsProvider->fillTranslations($po);
                $output->writeln('<info>done.</info>');
            }
            $poFile = "$poDirectory/messages.po";
            $moFile = "$poDirectory/messages.mo";
            if (is_file($poFile)) {
                $output->write('- reading current .po file... ');
                $oldPo = Translations::fromPoFile($poFile);
                $output->writeln('<info>done.</info>');
            } elseif (is_file($moFile)) {
                $output->write('- decompiling current .mo file... ');
                $oldPo = Translations::fromMoFile($poFile);
                $output->writeln('<info>done.</info>');
            } else {
                $oldPo = null;
            }
            if ($oldPo !== null) {
                $output->write('- merging translations... ');
                $po->mergeWith($oldPo, 0);
                $output->writeln('<info>done.</info>');
            }
            $output->write('- saving .po file... ');
            if (!is_dir($poDirectory)) {
                @mkdir($poDirectory, 0775, true);
                if (!is_dir($poDirectory)) {
                    throw new Exception("Unable to create the directory $poDirectory");
                }
            }
            $po->toPoFile($poFile);
            $output->writeln('<info>done.</info>');
            $output->write('- saving .mo file... ');
            $po->toMoFile($moFile);
            $output->writeln('<info>done.</info>');
        }
    }
    
    private function guessPackageDetails($packageDirectory)
    {
        $packageHandle = null;
        $packageVersion = null;
        $controllerFile = $packageDirectory . '/' . FILENAME_CONTROLLER;
        if (!is_file($controllerFile)) {
            throw new Exception("The directory '$packageDirectory' does not seems to contain a valid concrete5 package");
        }
        $fh = $this->app->make('helper/file');
        /* @var \Concrete\Core\File\Service\File $fh */
        $controllerContents = $fh->getContents($controllerFile);
        if ($controllerContents) {
            $allTokens = @token_get_all($controllerContents);
            if ($allTokens) {
                $tokens = array_values(
                    array_filter(
                        $allTokens,
                        function ($token) {
                            $keep = true;
                            if (is_array($token)) {
                                switch ($token[0]) {
                                    case T_DOC_COMMENT:
                                    case T_WHITESPACE:
                                    case T_COMMENT:
                                        $keep = false;
                                        break;
                                }
                            }
                            
                            return $keep;
                        }
                        )
                    );
                // Look for package version
                for ($i = 0; $i < count($tokens) - 2; ++$i) {
                    if (
                        $packageHandle === null
                        && is_array($tokens[$i + 0]) && $tokens[$i + 0][0] === T_VARIABLE && $tokens[$i + 0][1] === '$pkgHandle'
                        && is_string($tokens[$i + 1]) && $tokens[$i + 1] === '='
                        && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING
                        ) {
                            $packageHandle = @eval('return ' . $tokens[$i + 2][1] . ';');
                        }
                        if (
                            $packageVersion === null
                            && is_array($tokens[$i + 0]) && $tokens[$i + 0][0] === T_VARIABLE && $tokens[$i + 0][1] === '$pkgVersion'
                            && is_string($tokens[$i + 1]) && $tokens[$i + 1] === '='
                            && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING
                            ) {
                                $packageVersion = @eval('return ' . $tokens[$i + 2][1] . ';');
                            }
                }
            }
        }
        
        return [$packageHandle, $packageVersion];
    }
}