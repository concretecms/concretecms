<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\File\Service\File;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class BulkUserAssignCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:user-group:bulk-assign-users')
            ->setDescription('Bulk assign users to groups by a given CSV file.')
            ->addOption('csv-file', 'c', InputOption::VALUE_REQUIRED, 'Path to CSV file.')
            ->addOption('group-id', 'g', InputOption::VALUE_REQUIRED, 'The id of the target group.')
            ->addOption('remove-unlisted-users', 'r', InputOption::VALUE_OPTIONAL, 'Remove users from this group if they don\'t appear in CSV.')
            ->addOption('dry-run', 'd', InputOption::VALUE_OPTIONAL, 'Perform a dry run.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailAddresses = [];

        $app = Application::getFacadeApplication();
        /** @var GroupRepository $groupRepository */
        $groupRepository = $app->make(GroupRepository::class);
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $app->make(UserInfoRepository::class);
        /** @var LoggerFactory $loggerFactory */
        $loggerFactory = $app->make(LoggerFactory::class);
        /** @var File $fileHelper */
        $fileHelper = $app->make(File::class);
        $logger = $loggerFactory->createLogger(Channels::CHANNEL_USERS);

        $csvFile = $input->getOption('csv-file');
        $groupId = $input->getOption('group-id');
        $dryRun = $this->input->getParameterOption(['--dry-run', '-d']) !== false;
        $removeUnlistedUsers = $this->input->getParameterOption(['--remove-unlisted-users', '-r']) !== false;

        $targetGroup = $groupRepository->getGroupByID($groupId);

        if ($targetGroup instanceof Group) {

            if (strtolower(pathinfo($csvFile, PATHINFO_EXTENSION)) === 'csv') {
                $csvData = $fileHelper->getContents($csvFile);

                /*
                 * Validate the CSV and extract all mail addresses.
                 */

                foreach (explode(PHP_EOL, $csvData) as $line) {
                    $rows = str_getcsv($line);

                    if (count($rows) === 1) {
                        if (filter_var($rows[0], FILTER_VALIDATE_EMAIL)) {
                            $mailAddresses[] = $rows[0];
                        } else if ($rows[0] !== null) {
                            throw new Exception('The given CSV contains invalid mail addresses.');
                        }
                    } else {
                        throw new Exception('The given CSV contains more then one column.');
                    }
                }
            } else {
                throw new Exception('The file extension is invalid.');
            }
        } else {
            throw new Exception('You need to select valid target group.');
        }

        $totalUsersProvided = count($mailAddresses);
        $totalUsersAddedToTargetGroup = 0;
        $totalUsersRemovedFromTargetGroup = 0;

        /*
         * Add the given users to the target group.
         */

        foreach ($mailAddresses as $mailAddress) {
            $userInfo = $userInfoRepository->getByEmail($mailAddress);

            if ($userInfo instanceof UserInfo) {
                $user = $userInfo->getUserObject();

                if (!$user->inGroup($targetGroup)) {
                    if (!$dryRun) {
                        $user->enterGroup($targetGroup);
                    }

                    $totalUsersAddedToTargetGroup++;
                }
            }
        }

        /*
         * Remove all users from the target group that are not part of the given CSV file if this option is selected.
         */

        if ($removeUnlistedUsers) {
            foreach ($targetGroup->getGroupMembers() as $groupMember) {
                /** @var UserInfo $groupMember */
                if (!in_array($groupMember->getUserEmail(), $mailAddresses)) {
                    $user = $groupMember->getUserObject();

                    if (!$dryRun) {
                        $user->exitGroup($targetGroup);
                    }

                    $totalUsersRemovedFromTargetGroup++;
                }
            }
        }

        $output->writeln(sprintf('<info>Done! %s user records are found in the given CSV. %s users were added to the target group and %s users were removed from target group. For further details please check the logs.</info>',
            $totalUsersProvided,
            $totalUsersAddedToTargetGroup,
            $totalUsersRemovedFromTargetGroup
        ));

        return static::SUCCESS;
    }

}
