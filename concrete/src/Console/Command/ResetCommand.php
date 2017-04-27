<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Database;
use Core;
use Exception;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ResetCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:reset')
            ->setDescription('Reset the concrete5 installation, deleting files and emptying the database')
            ->addEnvOption()
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the reset')
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-reset
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('force')) {
            if (!$input->isInteractive()) {
                throw new Exception("You have to specify the --force option in order to run this command");
            }
            $confirmQuestion = new ConfirmationQuestion(
                'Are you sure you want to reset this concrete5 installation? ' .
                'This will delete files and empty the database! (y/n)',
                false
            );
            if (!$this->getHelper('question')->ask($input, $output, $confirmQuestion)) {
                throw new Exception("Operation aborted.");
            }
        }
        if (Database::getDefaultConnection()) {
            $output->write("Listing tables... ");
            $cn = Database::get();
            /* @var $cn \Concrete\Core\Database\Connection\Connection */
            $sm = $cn->getSchemaManager();
            $tables = $sm->listTables();
            $output->writeln('<info>done.</info>');
            $tableNames = [];
            foreach ($tables as $table) {
                $tableNames[] = $table->getName();
                foreach ($table->getForeignKeys() as $foreignKey) {
                    $output->write("Dropping foreign key {$table->getName()}.{$foreignKey->getName()}... ");
                    $sm->dropForeignKey($foreignKey, $table);
                    $output->writeln('<info>done.</info>');
                }
            }
            foreach ($tableNames as $tableName) {
                $output->write("Dropping table $tableName... ");
                $sm->dropTable($tableName);
                $output->writeln('<info>done.</info>');
            }
        }
        $fh = Core::make('helper/file');
        /* @var $fh \Concrete\Core\File\Service\File */
        $createEmptyDirs = [
            'application/files' => DIR_FILES_UPLOADED_STANDARD,
        ];
        $createEmptyFiles = [
            'application/files/index.html' => DIR_FILES_UPLOADED_STANDARD . '/index.html',
        ];
        if (is_file(DIR_FILES_UPLOADED_STANDARD . '/.gitignore')) {
            $createEmptyFiles['application/files/.gitignore'] = DIR_FILES_UPLOADED_STANDARD . '/.gitignore';
        }
        $deleteFiles = [
            '.htaccess' => DIR_BASE . '/.htaccess',
            'application/config/app.php' => DIR_CONFIG_SITE . '/app.php',
            'application/config/database.php' => DIR_CONFIG_SITE . '/database.php',
            'application/config/site.php' => DIR_CONFIG_SITE . '/site.php',
            'application/config/site_install.php' => DIR_CONFIG_SITE . '/site_install.php',
            'application/config/site_install_user.php' => DIR_CONFIG_SITE . '/site_install_user.php',
        ];
        foreach ($deleteFiles as $shownName => $fullpath) {
            if (is_file($fullpath)) {
                $output->write("Deleting file $shownName... ");
                if (@unlink($fullpath) === false) {
                    throw new Exception("Failed to delete file $fullpath");
                }
                $output->writeln('<info>done.</info>');
            }
        }
        $deleteDirs = [
            'application/config/generated_overrides' => DIR_CONFIG_SITE . '/generated_overrides',
            'application/config/doctrine' => DIR_CONFIG_SITE . '/doctrine',
            'application/files' => DIR_FILES_UPLOADED_STANDARD,
        ];
        foreach ($deleteDirs as $shownName => $fullpath) {
            if (is_dir($fullpath)) {
                $output->write("Deleting directory $shownName... ");
                if ($fh->removeAll($fullpath, true) === false) {
                    throw new Exception("Failed to delete directory $fullpath");
                }
                $output->writeln('<info>done.</info>');
            }
        }
        foreach ($createEmptyDirs as $shownName => $fullpath) {
            if (!file_exists($fullpath)) {
                $output->write("Creating directory $shownName... ");
                if (@mkdir($fullpath) === false) {
                    throw new Exception("Failed to create directory $fullpath");
                }
                $output->writeln('<info>done.</info>');
            }
        }
        foreach ($createEmptyFiles as $shownName => $fullpath) {
            if (!file_exists($fullpath)) {
                $output->write("Creating empty file $shownName... ");
                if (@touch($fullpath) === false) {
                    throw new Exception("Failed to create empty file $fullpath");
                }
                $output->writeln('<info>done.</info>');
            }
        }
    }
}
