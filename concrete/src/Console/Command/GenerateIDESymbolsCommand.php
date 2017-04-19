<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;
use Concrete\Core\Support\Symbol\ClassSymbol\MethodSymbol\MethodSymbol;
use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Exception;
use Core;

class GenerateIDESymbolsCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:ide-symbols')
            ->setDescription('Generate IDE symbols')
            ->addEnvOption()
            ->addArgument('generate-what', InputArgument::IS_ARRAY, 'Elements to generate [all|ide-classes|phpstorm]', ['all'])
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-ide-symbols
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        $what = $input->getArgument('generate-what');
        $p = array_search('ide-classes', $what);
        if ($p !== false || in_array('all', $what)) {
            if ($p !== false) {
                unset($what[$p]);
            }
            $output->write('Generating fake PHP classes to help IDE... ');
            if (!Core::make('app')->isInstalled()) {
                $output->writeln('<error>failed: concrete5 is not installed.</error>');
                $rc = static::RETURN_CODE_ON_FAILURE;
            } else {
                $this->generateIDEClasses();
                $output->writeln('<info>done.</info>');
            }
        }
        $p = array_search('phpstorm', $what);
        if ($p !== false || in_array('all', $what)) {
            if ($p !== false) {
                unset($what[$p]);
            }
            $output->write('Generating PHP metadata for PHPStorm... ');
            $this->generatePHPStorm();
            $output->writeln('<info>done.</info>');
        }
        $p = array_search('all', $what);
        if ($p !== false) {
            unset($what[$p]);
        }
        if (!empty($what)) {
            throw new Exception('Unrecognized arguments: ' . implode(', ', $what));
        }

        return $rc;
    }

    protected function generatePHPStorm()
    {
        $metadataGenerator = new \Concrete\Core\Support\Symbol\MetadataGenerator();
        $metadata = $metadataGenerator->render();
        $filename = DIR_BASE . '/concrete/src/Support/.phpstorm.meta.php';
        if (file_put_contents($filename, $metadata) === false) {
            throw new Exception('Error writing to file "' . $filename . '"');
        }
    }

    protected function generateIDEClasses()
    {
        $generator = new \Concrete\Core\Support\Symbol\SymbolGenerator();
        $symbols = $generator->render(
            "\n",
            '    ',
            function (ClassSymbol $class, MethodSymbol $method) {
                if ($class->isFacade()) {
                    return true;
                }

                return false;
            }
        );
        $filename = DIR_BASE . '/concrete/src/Support/__IDE_SYMBOLS__.php';
        if (file_put_contents($filename, $symbols) === false) {
            throw new Exception('Error writing to file "' . $filename . '"');
        }
    }
}
