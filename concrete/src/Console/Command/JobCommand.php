<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\ConsoleAwareInterface;
use Job;
use JobSet;
use RuntimeException;
use Concrete\Core\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JobCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:job')
            ->setDescription(t('Run a concrete5 job'))
            ->addEnvOption()
            ->addOption('set', null, InputOption::VALUE_NONE, t('Find jobs by set instead of job handle'))
            ->addOption('list', null, InputOption::VALUE_NONE, t('List available jobs'))
            ->addArgument(
                'jobs',
                InputArgument::IS_ARRAY,
                t('Jobs to run (separate multiple jobs with a space)')
            )
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-job
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        $options = $input->getOptions();
        $formatter = $this->getHelper('formatter');

        if ($options['list']) {
            $output->writeln(t('Available Jobs'));
            $table = new Table($output);
            $table->setHeaders([t('Job Handle'), t('Job Name')]);
            foreach (Job::getList() as $job) {
                $table->addRow([$job->getJobHandle(), $job->getJobName()]);
            }
            $table->render();

            $output->writeln('');
            $output->writeln(t('Available Job Sets'));
            $table = new Table($output);
            $table->setHeaders([t('Set Name'), t('Jobs')]);
            foreach (JobSet::getList() as $jobSet) {
                $jobsInSet = [];
                foreach ($jobSet->getJobs() as $job) {
                    $jobsInSet[] = $job->getJobName();
                }
                $table->addRow([$jobSet->getJobSetName(), implode(', ', $jobsInSet)]);
            }
            $table->render();
        } else {
            $jobs = [];

            $jobsArg = $input->getArgument('jobs');

            if (empty($jobsArg)) {
                throw new RuntimeException(t('At least one job must be provided'));
            }

            if ($options['set']) {
                foreach ($jobsArg as $setName) {
                    $set = JobSet::getByName($setName);
                    if ($set) {
                        $jobs = array_merge($jobs, $set->getJobs());
                    } else {
                        $rc = 1;
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(
                                '<error>' . t('A job set with name "%s" was not found', $setName) . '</error>'
                            );
                        }
                    }
                }
            } else {
                foreach ($jobsArg as $jobHandle) {
                    $job = Job::getByHandle($jobHandle);
                    if ($job) {
                        $jobs[] = $job;
                    } else {
                        $rc = 1;
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(
                                '<error>' . t('A job with handle "%s" was not found', $jobHandle) . '</error>'
                            );
                        }
                    }
                }
            }

            if (!empty($jobs)) {
                foreach ($jobs as $job) {
                    // Provide the console objects to objects that are aware of the console
                    if ($job instanceof ConsoleAwareInterface) {
                        $job->setConsole($this->getApplication(), $output, $input);
                    }

                    $result = $job->executeJob();
                    if ($result->isError()) {
                        $rc = 1;
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(
                                $formatter->formatSection(
                                    $job->getJobHandle(), '<error>' . t('Job Failed') . '</error>'
                                )
                            );
                        }
                        break;
                    }
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                        $output->writeln(
                            $formatter->formatSection($job->getJobHandle(), $result->getResultMessage())
                        );
                    }
                }
            }
        }

        return $rc;
    }
}
