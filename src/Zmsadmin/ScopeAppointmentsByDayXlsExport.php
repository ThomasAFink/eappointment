<?php
/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsadmin;

use \XLSXWriter;

/**
 * Handle requests concerning services
 */
class ScopeAppointmentsByDayXlsExport extends BaseController
{

    /**
     *
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        //parameters
        $scopeId = $args['id'];
        $selectedDate = $args['date'];
        $selectedDateTime = $selectedDate ? new \DateTimeImmutable($selectedDate) : \App::$now;

        // HTTP requests
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 2])->getEntity();
        $workstationRequest = new \BO\Zmsclient\WorkstationRequests(\App::$http, $workstation);
        if ($workstation->getScope()->id != $scopeId) {
            $scope = \App::$http->readGetResult('/scope/' . $scopeId . '/')->getEntity();
            $workstationRequest->setDifferentScope($scope);
        }
        $processList = $workstationRequest->readProcessListByDate($selectedDateTime);

        // data refinement
        $visibleProcessList = $processList
            ->toQueueList(\App::$now)
            ->withStatus(['confirmed', 'queued'])
            ->withSortedArrival()
            ->toProcessList();

        // rendering
        $xlsSheetTitle = $selectedDateTime->format('d.m.Y');

        $clusterColumn = $workstation->isClusterEnabled() ? 'Kürzel' : 'Lfd. Nummer';
        $xlsHeaders = [
            $clusterColumn => $workstation->isClusterEnabled() ? 'string' : 'integer',
            'Uhrzeit/Ankunftszeit' => 'string',
            'Nr.' => 'integer',
            'Name' => 'string',
            'Telefon' => 'string',
            'Email' => 'string',
            'Dienstleistung' => 'string',
            'Anmerkungen' => 'string'
        ];
        $writer = new XLSXWriter();
        $writer->writeSheetHeader($xlsSheetTitle, $xlsHeaders);

        $key = 1;
        foreach ($visibleProcessList as $queueItem) {
            $client = $queueItem->getFirstClient();
            $request = count($queueItem->requests) > 0 ? $queueItem->requests[0] : [];
            $row = [
                $workstation->isClusterEnabled() ? $queueItem->getCurrentScope()->shortName : $key++ ,
                $queueItem->getArrivalTime()->format('H:i:s'),
                $queueItem->queue['number'],
                $client['familyName'],
                $client['telephone'],
                $client['email'],
                $queueItem->requests->getCsvForProperty('name'),
                $queueItem->amendment
            ];

            $writer->writeSheetRow($xlsSheetTitle, $row);
        }

        $response->getBody()->write($writer->writeToString());

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader(
                'Content-Disposition',
                sprintf('download; filename="tagesuebersicht_%s.xlsx"', $xlsSheetTitle)
            );
    }
}
