<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Permission\IPRange;
use Concrete\Core\Permission\IPRangesCsvWriter;
use Concrete\Core\Permission\IPService;
use Exception;
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
        $this->set('ranges', $this->app->make('ip')->getRanges($type));
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
            throw new Exception($this->token->getErrorMessage());
        }
        $range = IPFactory::rangeFromString($this->request->request->get('range'));
        if ($range === null) {
            throw new Exception(t('The specified IP range is invalid'));
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $record = $ipService->createRange($range, $type);

        return $this->app->make(ResponseFactoryInterface::class)->json(['row' => $this->formatRangeRow($record)]);
    }

    public function delete_range($type)
    {
        if (!$this->token->validate('delete_range/' . $type)) {
            throw new Exception($this->token->getErrorMessage());
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $record = $ipService->getRangeByID($this->request->request->get('id'));
        if ($record !== null) {
            if ($record->getType() !== (int) $type) {
                throw new Exception(t('The specified IP range is invalid'));
            }
            $ipService->deleteRange($record);
        }

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function make_range_permanent($type)
    {
        if (!$this->token->validate('make_range_permanent/' . $type)) {
            throw new Exception($this->token->getErrorMessage());
        }
        $ipService = $this->app->make('ip');
        /* @var IPService $ipService */
        $record = $ipService->getRangeByID($this->request->request->get('id'));
        if ($record === null) {
            throw new Exception(t('Unable to find the IP range specified'));
        }
        if ($record->getType() !== (int) $type) {
            throw new Exception(t('The specified IP range is invalid'));
        }
        $ipService->createRange($record->getIpRange(), IPService::IPRANGETYPE_BLACKLIST_MANUAL);
        $ipService->deleteRange($record);

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
    }

    public function export($type, $includeExpired, $token)
    {
        if (!$this->token->validate("iprange/export/range/{$type}/$includeExpired", $token)) {
            throw new Exception($this->token->getErrorMessage());
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
}
