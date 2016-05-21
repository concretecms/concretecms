<?php
namespace Concrete\Core\Console\Command;

use Job;
use JobSet;
use RuntimeException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JobCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:job')
            ->setDescription(t('Run a concrete5 job'))
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
  1 errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $options = $input->getOptions();
            $formatter = $this->getHelper('formatter');

            if ($options['list']) {
                $output->writeln(t('Available Jobs'));
                $table = new Table($output);
                $table->setHeaders(array(t('Job Handle'), t('Job Name')));
                foreach (Job::getList() as $job) {
                    $table->addRow(array($job->getJobHandle(), $job->getJobName()));
                }
                $table->render();

                $output->writeln('');
                $output->writeln(t('Available Job Sets'));
                $table = new Table($output);
                $table->setHeaders(array(t('Set Name'), t('Jobs')));
                foreach (JobSet::getList() as $jobSet) {
                    $jobsInSet = array();
                    foreach ($jobSet->getJobs() as $job) {
                        $jobsInSet[] = $job->getJobName();
                    }
                    $table->addRow(array($jobSet->getJobSetName(), implode(', ', $jobsInSet)));
                }
                $table->render();
            } else {
                $jobs = array();

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
        } catch (Exception $x) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln('<error>'.$x->getMessage().'</error>');
            }
            $rc = 1;
        }

        return $rc;
    }
}
