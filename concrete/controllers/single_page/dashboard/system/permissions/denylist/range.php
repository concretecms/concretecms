<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions\Denylist;

use Concrete\Controller\SinglePage\Dashboard\System\Permissions\Denylist;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Permission\IpAccessControlRange;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\IpAccessControlService;
use Concrete\Core\Permission\IPRangesCsvWriter;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Range extends Denylist
{
    public function view($type = null, $id = '')
    {
        $service = $this->getService($id);
        if ($service === null) {
            return $this->app->make(ResponseFactoryInterface::class)->redirect(
                $this->app->make(ResolverManagerInterface::class)->resolve(['/dashboard/system/permissions/denylist']),
                302
            );
        }
        $category = $service->getCategory();
        $type = (int) $type;
        switch ($type) {
            case IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL:
                $this->set('pageTitle', t('%s: Denylisted IP addresses (manual)', $category->getDisplayName()));
                break;
            case IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL:
                $this->set('pageTitle', t('%s: Allowlisted IP addresses', $category->getDisplayName()));
                break;
            case IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC:
            default:
                $this->set('pageTitle', t('%s: Denylisted IP addresses (automatic)', $category->getDisplayName()));
                $type = IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC;
                break;
        }
        $this->set('type', $type);
        $this->set('service', $service);
        $this->set('myIPAddress', $this->app->make(AddressInterface::class));
    }

    public function formatRangeRow(IpAccessControlRange $range)
    {
        $html = '';
        $html .= '<tr data-range-id="' . $range->getIpAccessControlRangeID() . '">';
        $html .= '<td><a class="btn btn-sm btn-danger ccm-iprange-delete" href="#"><i class="fas fa-times"></i></a></td>';
        $html .= '<td><code>' . $range->getIpRange() . '</code></td>';
        if ($range->getType() === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
            $html .= '<td>';
            if ($range->getExpiration() !== null) {
                $html .= $this->app->make('date')->formatPrettyDateTime($range->getExpiration(), true);
            }
            $html .= '</td>';
            $html .= '<td class="text-end"><a href="#" class="btn btn-sm btn-info ccm-iprange-makepermanent">' . t('Make permament') . '</a></td>';
        }
        $html .= '</tr>';

        return $html;
    }

    public function add_range($type, $id)
    {
        if (!$this->token->validate('add_range/' . $type . '/' . $id)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $range = IPFactory::parseRangeString($this->request->request->get('range'));
        if ($range === null) {
            throw new UserMessageException(t('The specified IP range is invalid'));
        }
        $service = $this->getService($id);
        if ($service === null) {
            throw new UserMessageException(t('Unable to find the requested category'));
        }
        if (!$this->request->request->get('force') && ($type & IpAccessControlService::IPRANGEFLAG_BLACKLIST) === IpAccessControlService::IPRANGEFLAG_BLACKLIST) {
            $myIP = $this->app->make(AddressInterface::class);
            if ($range->contains($myIP)) {
                if (!$service->isAllowlisted($myIP)) {
                    if ($range instanceof \IPLib\Range\Single) {
                        $msg = t('The specified IP address is the one you are currently using.');
                    } else {
                        $msg = t('The specified IP range contains the IP address you are currently using.');
                    }
                    $msg .= "\n" . t("If you don't add your IP address to the allowlist, you won't be able to log-in anymore from the current IP address.");

                    return $this->app->make(ResponseFactoryInterface::class)->json(['require_force' => $msg]);
                }
            }
        }
        $record = $service->createRange($range, $type);

        return $this->app->make(ResponseFactoryInterface::class)->json(['row' => $this->formatRangeRow($record)]);
    }

    public function delete_range($type, $id)
    {
        if (!$this->token->validate('delete_range/' . $type . '/' . $id)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $service = $this->getService($id);
        if ($service === null) {
            throw new UserMessageException(t('Unable to find the requested category'));
        }
        $result = true;
        $record = $service->getRangeByID($this->request->request->get('id'));
        if ($record !== null) {
            if ($record->getType() !== (int) $type) {
                throw new UserMessageException(t('The specified IP range is invalid'));
            }
            $myIpWasAllowlisted = false;
            if (($type & IpAccessControlService::IPRANGEFLAG_WHITELIST) === IpAccessControlService::IPRANGEFLAG_WHITELIST) {
                $myIP = $this->app->make(AddressInterface::class);
                $range = $record->getIpRange();
                if ($range->contains($myIP)) {
                    $myIpWasAllowlisted = true;
                }
            }
            $service->deleteRange($record);
            if ($myIpWasAllowlisted && $service->isDenylisted($myIP)) {
                $result = t("The removed range contained your current IP address, which is now in the denylist.\nIf you want to log in again with the current IP address, you should add your IP to the allowlist, or remove the relevant denylist records.");
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->json($result);
    }

    public function make_range_permanent($type, $id)
    {
        if (!$this->token->validate('make_range_permanent/' . $type . '/' . $id)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $service = $this->getService($id);
        if ($service === null) {
            throw new UserMessageException(t('Unable to find the requested category'));
        }
        $range = $service->getRangeByID($this->request->request->get('id'));
        if ($range === null) {
            throw new UserMessageException(t('Unable to find the IP range specified'));
        }
        if ($range->getType() !== (int) $type) {
            throw new UserMessageException(t('The specified IP range is invalid'));
        }
        $range
            ->setType(IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL)
            ->setExpiration(null)
        ;
        $this->entityManager->flush($range);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function export($type, $id, $includeExpired, $token)
    {
        if (!$this->token->validate("iprange/export/range/{$type}/{$id}/$includeExpired", $token)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $service = $this->getService($id);
        if ($service === null) {
            throw new UserMessageException(t('Unable to find the requested category'));
        }
        $type = (int) $type;
        switch ($type) {
            case IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC:
                $filename = 'denylist-automatic';
                break;
            case IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL:
                $filename = 'denylist-manual';
                break;
            case IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL:
                $filename = 'allowlist';
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
        $ranges = $service->getRanges($type, $includeExpired);
        $app = $this->app;
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';

        return StreamedResponse::create(
            function () use ($app, $type, $ranges, $bom) {
                $writer = $app->make(IPRangesCsvWriter::class, [
                    'writer' => $app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                    'type' => $type,
                ]);
                echo $bom;
                $writer->insertHeaders();
                $writer->insertRanges($ranges);
            },
            StreamedResponse::HTTP_OK,
            $headers
        );
    }

    public function clear_data($id)
    {
        if (!$this->token->validate('denylist-clear-data/' . $id)) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $service = $this->getService($id);
        if ($service === null) {
            throw new UserMessageException(t('Unable to find the requested category'));
        }
        $valn = $this->app->make('helper/validation/numbers');
        $post = $this->request->request;

        $deleteFailedLoginAttempts = $post->get('delete-failed-login-attempts') === 'yes';
        if ($deleteFailedLoginAttempts) {
            $deleteFailedLoginAttemptsMinAge = $post->get('delete-failed-login-attempts-min-age');
            if (!$valn->integer($deleteFailedLoginAttemptsMinAge, 0)) {
                throw new UserMessageException(t('Please specify a valid number of days.'));
            }
            $deleteFailedLoginAttemptsMinAge *= 24 * 60 * 60;
        }
        switch ($post->get('delete-automatic-denylist')) {
            case 'yes-keep-current':
                $deleteAutomaticDenylist = true;
                $deleteAutomaticDenylistOnlyExpired = true;
                break;
            case 'yes-all':
                $deleteAutomaticDenylist = true;
                $deleteAutomaticDenylistOnlyExpired = false;
                break;
        }
        $messages = [];
        if ($deleteFailedLoginAttempts) {
            $messages[] = t2('%s failed login attempt has been deleted.', '%s failed login attempts have been deleted.', $service->deleteEvents($deleteFailedLoginAttemptsMinAge));
        }
        if ($deleteAutomaticDenylist) {
            $messages[] = t2('%s automatically banned IP address has been deleted.', '%s automatically banned IP addresses have been deleted.', $service->deleteAutomaticDenylist($deleteAutomaticDenylistOnlyExpired));
        }
        if (empty($messages)) {
            throw new UserMessageException(t('Please specify what you would like to delete.'));
        }
        $this->flash('success', implode("\n", $messages));

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }
}
