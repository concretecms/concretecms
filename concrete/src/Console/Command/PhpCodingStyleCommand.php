<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Support\CodingStyle\PhpFixer;
use Concrete\Core\Support\CodingStyle\PhpFixerOptions;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FixerFileProcessedEvent;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Events\EventDispatcher;

class PhpCodingStyleCommand extends Command
{
    protected $description = 'Check or fix the PHP coding style.';

    public function __construct($name = null)
    {
        $defaultWebRoot = PhpFixerOptions::getDefaultWebRoot();
        $this->signature = <<<EOT
c5:phpcs
{--no-cache : Specify this flag to turn off the coding style cache}
{--webroot={$defaultWebRoot} : Specify the webroot (use - to auto-detect it)}
{--p|php= : The minimum PHP version }
{action : Either "fix" or "check"}
{path*  : The path to one or more files or directories }
EOT
        ;
        parent::__construct($name);
    }

    public function handle(PhpFixer $fixer, EventDispatcher $eventDispatcher)
    {
        class_alias('Symfony\Component\EventDispatcher\GenericEvent', 'PhpCsFixer\Event\Event');
        $action = $this->input->getArgument('action');
        switch ($action) {
            case 'fix':
                $dryRun = false;
                break;
            case 'check':
                $dryRun = true;
                break;
            default:
                throw new RuntimeException(sprintf('Unknown "%s" action: accepted values are "fix" and "check".', $action));
        }
        $splFileInfos = [];
        foreach ($this->input->getArgument('path') as $path) {
            $absolutePath = realpath($path);
            if ($absolutePath === false) {
                throw new RuntimeException(sprintf('The file/directory "%s" could not be found (it is not readable)', $path));
            }
            $splFileInfo = new SplFileInfo($absolutePath);
            if ($dryRun === false && !$splFileInfo->isWritable()) {
                throw new RuntimeException(sprintf('The file/directory "%s" is not writable', $path));
            }
            $splFileInfos[] = $splFileInfo;
        }
        $webroot = (string) $this->input->getOption('webroot');
        if ($webroot !== '') {
            if ($webroot === '-') {
                $webroot = $this->detectWebRoot($splFileInfos[0]->getPathname());
            }
            $fixer->getOptions()->setWebRoot($webroot);
        }
        $fixer->getOptions()
            ->setIsCacheDisabled($this->input->getOption('no-cache'))
            ->setMinimumPhpVersion($this->input->getOption('php'))
        ;
        list($counters, $changes, $errors) = $fixer->fix($this->input, $this->output, $splFileInfos, $dryRun);
        /* @var array $counters */
        /* @var array $changes */
        /* @var \PhpCsFixer\Error\ErrorsManager $errors */
        $this->printChanges($changes, $dryRun);
        $this->printErrors($errors, $dryRun);
        $this->printCountersTable($counters, $dryRun);
        if ($dryRun) {
            return empty($changes) && $errors->isEmpty() ? static::SUCCESS : static::FAILURE;
        }

        return $errors->isEmpty() ? static::SUCCESS : static::FAILURE;
    }

    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;

        $this->setHelp(<<<EOT
Check or fix the PHP coding style.

Return values when checking the coding style:
- {$okExitCode}: the coding style is valid and no error occurred
- {$errExitCode}: some fixes are needed or some error occurred

Return values when applying the coding style:
- {$okExitCode}: no error occurred
- {$errExitCode}: some error occurred
EOT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Console\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->canRunAsRoot = $input->getArgument('action') !== 'fix';

        return parent::initialize($input, $output);
    }

    /**
     * @param array $changes
     * @param bool $dryRun
     */
    protected function printChanges(array $changes, $dryRun)
    {
        if (!$dryRun) {
            return;
        }
        foreach ($changes as $file => $change) {
            $this->output->writeln(sprintf('### CHANGES REQUIRED IN FILE %s ###', $file));
            $this->output->writeln($change['diff']);
        }
    }

    /**
     * @param \PhpCsFixer\Error\ErrorsManager $errors
     * @param bool $dryRun
     */
    protected function printErrors(ErrorsManager $errors, $dryRun)
    {
        foreach ([
            'LINTING ERRORS' => $errors->getInvalidErrors(),
            'FIXER ERRORS' => $errors->getExceptionErrors(),
            'POST-PROCESSING ERRORS' => $errors->getLintErrors(),
        ] as $name => $list) {
            if (empty($list)) {
                continue;
            }
            $this->output->writeln(sprintf('<error>### %s ###</error>', $name));
            foreach ($list as $index => $error) {
                $this->output->writeln(sprintf('<error>%s. File %s</error>', $index + 1, $error->getFilePath()));
                $exception = $error->getSource();
                if ($exception) {
                    $lines = explode("\n", str_replace("\r\n", "\n", trim($exception->getMessage())));
                    foreach ($lines as $line) {
                        $this->output->writeln(sprintf('<error>   %s</error>', $line));
                    }
                }
            }
            $this->output->writeln('');
        }
    }

    /**
     * @param array $counters
     * @param bool $dryRun
     */
    protected function printCountersTable(array $counters, $dryRun)
    {
        $rows = [];
        $headerLength = 0;
        $valueLength = 0;
        ksort($counters);
        foreach ($counters as $state => $count) {
            $count = number_format($count, 0, '.', ',');
            switch ($state) {
                case FixerFileProcessedEvent::STATUS_INVALID:
                    $header = 'Files ignored for syntax errors';
                    break;
                    break;
                case FixerFileProcessedEvent::STATUS_SKIPPED:
                    $header = 'Skipped files';
                    break;
                case FixerFileProcessedEvent::STATUS_NO_CHANGES:
                    $header = $dryRun ? 'Files already fixed' : 'Unchanged files';
                    break;
                case FixerFileProcessedEvent::STATUS_FIXED:
                    $header = $dryRun ? 'Files that should be fixed' : 'Fixed files';
                    break;
                case FixerFileProcessedEvent::STATUS_EXCEPTION:
                    $header = 'Fixer exceptions';
                    break;
                case FixerFileProcessedEvent::STATUS_LINT:
                    $header = 'Linter errors';
                    break;
                case FixerFileProcessedEvent::STATUS_UNKNOWN:
                default:
                    $header = 'unknown';
                    break;
            }
            $rows[] = [$header, $count];
            $headerLength = max($headerLength, strlen($header));
            $valueLength = max($valueLength, strlen($count));
        }
        if (!empty($rows)) {
            $this->output->writeln('Summary:');
            foreach ($rows as $row) {
                $this->output->writeln('- ' . str_pad($row[0], $headerLength, ' ', STR_PAD_RIGHT) . ': ' . str_pad($row[1], $valueLength, ' ', STR_PAD_LEFT));
            }
        }
    }

    /**
     * @param string $startingPoint
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return string
     */
    private function detectWebRoot($startingPoint)
    {
        if (is_file($startingPoint)) {
            $startingPoint = dirname($startingPoint);
        }
        $startingPoint = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $startingPoint), '/');
        if (strpos($startingPoint, '/') === false) {
            throw new UserMessageException('Unable to detect the webroot');
        }
        if (is_file("{$startingPoint}/index.php") && is_file("{$startingPoint}/concrete/dispatcher.php")) {
            return $startingPoint;
        }
        if (preg_match('_^/+$_', $startingPoint) || preg_match('_^\w:/*$_', $startingPoint)) {
            throw new UserMessageException('Unable to detect the webroot');
        }

        return $this->detectWebRoot(dirname($startingPoint));
    }
}
