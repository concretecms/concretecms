<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\IPRange;
use Concrete\Core\Permission\IPRangesCsvWriter;
use Concrete\Core\Permission\IPService;
use IPLib\Factory as IPFactory;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Range extends DashboardPageController
{
    public function view($type = null)
    {
        $type = (int) $type;
        switch ($type) {
            case IPService::IPRANGETYPE_BLACKLIST_MANUAL:
                $this->set('pageTitle', t('Blacklisted IP addresses (manual)'));
                break;
            case IPService::IPRANGETYPE_WHITELIST_MANUAL:
                $this->set('pageTitle', t('Whitelisted IP addresses'));
                break;
            case IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC:
            default:
                $this->set('pageTitle', t('Blacklisted IP addresses (automatic)'));
                $type = IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC;
                break;
        }
        $this->set('type', $type);
        $ip = $this->app->make('ip');
        $this->set('ip', $ip);
        $this->set('ranges', $ip->getRanges($type));
    }

    public function formatRangeRow(IPRange $range)
    {
        $html = '';
        $html .= '<tr data-range-id="' . $range->getID() . '">';
        $html .= '<td><a class="btn btn-xs btn-danger ccm-iprange-delete" href="#"><i class="fa fa-times"></i></a></td>';
        $html .= '<td><code>' . $range->getIpRange() . '</code></td>';
        if ($range->getType() === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $html .= '<td>';
            if ($range->getExpires() !== null) {
                $html .= $this->app->make('date')->formatPrettyDateTime($range->getExpires(), true);
            }
            $html .= '</td>';
            $html .= '<td class="pull-right"><a href="#" class="btn btn-xs btn-info ccm-iprange-makepermanent">' . t('Make permament') . '</a></td>';
        }
        $html .= '</tr>';

        return $html;
    }

    public function add_range($type)
    {
        if (!$this->token->validate('add_range/' . $type)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $range = IPFactory::rangeFromString($this->request->request->get('range'));
        if ($range === null) {
            throw new UserMessageException(t('The specified IP range is invalid'));
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        if (!$this->request->request->get('force') && ($type & IPService::IPRANGEFLAG_BLACKLIST) === IPService::IPRANGEFLAG_BLACKLIST) {
            $myIP = $ipService->getRequestIPAddress();
            if ($range->contains($myIP)) {
                if (!$ipService->isWhitelisted($myIP)) {
                    if ($range instanceof \IPLib\Range\Single) {
                        $msg = t('The specified IP address is the one you are currently using.');
                    } else {
                        $msg = t('The specified IP range contains the IP address you are currently using.');
                    }
                    $msg .= "\n" . t("If you don't add your IP address to the whitelist, you won't be able to log-in anymore from the current IP address.");

                    return $this->app->make(ResponseFactoryInterface::class)->json(['require_force' => $msg]);
                }
            }
        }
        $record = $ipService->createRange($range, $type);

        return $this->app->make(ResponseFactoryInterface::class)->json(['row' => $this->formatRangeRow($record)]);
    }

    public function delete_range($type)
    {
        if (!$this->token->validate('delete_range/' . $type)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $result = true;
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $record = $ipService->getRangeByID($this->request->request->get('id'));
        if ($record !== null) {
            if ($record->getType() !== (int) $type) {
                throw new UserMessageException(t('The specified IP range is invalid'));
            }
            $myIpWasWhitelisted = false;
            if (($type & IPService::IPRANGEFLAG_WHITELIST) === IPService::IPRANGEFLAG_WHITELIST) {
                $myIP = $ipService->getRequestIPAddress();
                $range = $record->getIpRange();
                if ($range->contains($myIP)) {
                    $myIpWasWhitelisted = true;
                }
            }
            $ipService->deleteRange($record);
            if ($myIpWasWhitelisted && $ipService->isBlacklisted($myIP)) {
                $result = t("The removed range contained your current IP address, which is now in the blacklist.\nIf you want to log in again with the current IP address, you should add your IP to the whitelist, or remove the relevant blacklist records.");
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($result);
    }

    public function make_range_permanent($type)
    {
        if (!$this->token->validate('make_range_permanent/' . $type)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $record = $ipService->getRangeByID($this->request->request->get('id'));
        if ($record === null) {
            throw new UserMessageException(t('Unable to find the IP range specified'));
        }
        if ($record->getType() !== (int) $type) {
            throw new UserMessageException(t('The specified IP range is invalid'));
        }
        $ipService->createRange($record->getIpRange(), IPService::IPRANGETYPE_BLACKLIST_MANUAL);
        $ipService->deleteRange($record);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function export($type, $includeExpired, $token)
    {
        if (!$this->token->validate("iprange/export/range/{$type}/$includeExpired", $token)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $type = (int) $type;
        switch ($type) {
            case IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC:
                $filename = 'blacklist-automatic';
                break;
            case IPService::IPRANGETYPE_BLACKLIST_MANUAL:
                $filename = 'blacklist-manual';
                break;
            case IPService::IPRANGETYPE_WHITELIST_MANUAL:
                $filename = 'whitelist';
                break;
            default:
                $filename = 'ip-ranges';
                break;
        }
        $filename .= '-' . $this->app->make('date')->formatCustom('Y-m-d-H-i-s') . '.csv';
        $includeExpired = (bool) $includeExpired;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $ranges = $ipService->getRanges($type, $includeExpired);
        $app = $this->app;

        return StreamedResponse::create(
            function () use ($app, $type, $ranges) {
                $writer = $app->build(IPRangesCsvWriter::class, ['writer' => Writer::createFromPath('php://output', 'w'), 'type' => $type]);
                $writer->insertHeaders();
                $writer->insertRanges($ranges);
            },
            StreamedResponse::HTTP_OK,
            $headers
        );
    }

    public function clear_data()
    {
        if (!$this->token->validate('blacklist-clear-data')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $valn = $this->app->make('helper/validation/numbers');
        /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
        $post = $this->request->request;

        $deleteFailedLoginAttempts = $post->get('delete-failed-login-attempts') === 'yes';
        if ($deleteFailedLoginAttempts) {
            $deleteFailedLoginAttemptsMinAge = $post->get('delete-failed-login-attempts-min-age');
            if (!$valn->integer($deleteFailedLoginAttemptsMinAge, 0)) {
                throw new UserMessageException(t('Please specify a valid number of days.'));
            }
            $deleteFailedLoginAttemptsMinAge *= 24 * 60 * 60;
        }
        switch ($post->get('delete-automatic-blacklist')) {
            case 'yes-keep-current':
                $deleteAutomaticBlacklist = true;
                $deleteAutomaticBlacklistOnlyExpired = true;
                break;
            case 'yes-all':
                $deleteAutomaticBlacklist = true;
                $deleteAutomaticBlacklistOnlyExpired = false;
                break;
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $messages = [];
        if ($deleteFailedLoginAttempts) {
            $messages[] = t2('%s failed login attempt has been deleted.', '%s failed login attempts have been deleted.', $ipService->deleteFailedLoginAttempts($deleteFailedLoginAttemptsMinAge));
        }
        if ($deleteAutomaticBlacklist) {
            $messages[] = t2('%s automatically banned IP address has been deleted.', '%s automatically banned IP addresses have been deleted.', $ipService->deleteAutomaticBlacklist($deleteAutomaticBlacklistOnlyExpired));
        }
        if (empty($messages)) {
            throw new UserMessageException(t('Please specify what you would like to delete.'));
        }
        $this->flash('success', implode("\n", $messages));

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
