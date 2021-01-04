<?php

namespace Concrete\Controller\SinglePage\Dashboard\Users\Groups;

use Concrete\Core\File\Service\File;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BulkUserAssignment extends DashboardPageController
{
    public function view()
    {
        if ($this->request->getMethod() === 'POST') {
            $mailAddresses = [];

            /** @var Validation $formValidator */
            $formValidator = $this->app->make(Validation::class);
            /** @var GroupRepository $groupRepository */
            $groupRepository = $this->app->make(GroupRepository::class);
            /** @var UserInfoRepository $userInfoRepository */
            $userInfoRepository = $this->app->make(UserInfoRepository::class);
            /** @var LoggerFactory $loggerFactory */
            $loggerFactory = $this->app->make(LoggerFactory::class);
            /** @var File $fileHelper */
            $fileHelper = $this->app->make(File::class);
            $logger = $loggerFactory->createLogger(Channels::CHANNEL_USERS);
            
            $formValidator->setData(array_merge($this->request->request->all()));
            $formValidator->addRequiredToken('bulk_user_assignment');
            $formValidator->addRequired('targetGroup', t('You need to select a target group.'));

            if ($formValidator->test()) {
                $targetGroupId = $this->request->request->get('targetGroup');
                $targetGroup = $groupRepository->getGroupByID($targetGroupId);

                $removeUnlistedUsers = $this->request->request->has('removeUnlistedUsers');

                if ($targetGroup instanceof Group) {
                    /** @var UploadedFile $csvFile */
                    $csvFile = $this->request->files->get('csvFile');

                    if ($csvFile instanceof UploadedFile) {
                        if (strtolower(pathinfo($csvFile->getClientOriginalName(), PATHINFO_EXTENSION)) === 'csv') {
                            $csvData = $fileHelper->getContents($csvFile->getPathname());

                            /*
                             * Validate the CSV file and extract all mail addresses.
                             */

                            foreach (explode(PHP_EOL, $csvData) as $line) {
                                $rows = str_getcsv($line);

                                if (count($rows) === 1) {
                                    if (filter_var($rows[0], FILTER_VALIDATE_EMAIL)) {
                                        $mailAddresses[] = $rows[0];
                                    } else if ($rows[0] !== null) {
                                        $this->error->add(t('The given CSV file contains invalid mail addresses.'));
                                    }
                                } else {
                                    $this->error->add(t('The given CSV file contains more than one column.'));
                                    break;
                                }
                            }
                        } else {
                            $this->error->add(t('The file extension is invalid.'));
                        }
                    } else {
                        $this->error->add(t('You need to upload a CSV file.'));
                    }
                } else {
                    $this->error->add(t('You need to select valid target group.'));
                }

                if (!$this->error->has()) {
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
                                $user->enterGroup($targetGroup);
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
                                $user->exitGroup($targetGroup);
                                $totalUsersRemovedFromTargetGroup++;
                            }
                        }
                    }

                    $this->set('success', t(
                        'Done! %s user records are found in the given CSV file. %s users were added to the target group and %s users were removed from target group. For further details please check the logs.',
                        $totalUsersProvided,
                        $totalUsersAddedToTargetGroup,
                        $totalUsersRemovedFromTargetGroup
                    ));
                }

            } else {
                $this->error = $formValidator->getError();
            }
        }
    }
}
