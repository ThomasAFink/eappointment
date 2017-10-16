<?php
/**
 *
 * @package Zmscalldisplay
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmscalldisplay;

/**
 * Handle requests concerning services
 */
class Index extends BaseController
{

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $validator = $request->getAttribute('validator');
        $defaultTemplate = $validator->getParameter("template")
            ->isPath()
            ->setDefault('defaultplatz')
            ->getValue();
        $calldisplayHelper = (new Helper\Calldisplay($request));
        $calldisplay = $calldisplayHelper->getEntity();

        $template = (new Helper\TemplateFinder($defaultTemplate))->setCustomizedTemplate($calldisplay);
        return \BO\Slim\Render::withHtml(
            $response,
            $template->getTemplate(),
            array(
                'debug' => \App::DEBUG,
                'queueStatusRequested' => implode(',', $calldisplayHelper::getRequestedQueueStatus($request)),
                'scopeList' => $calldisplay->getFullScopeList()->getIdsCsv(),
                'title' => 'Aufrufanzeige',
                'calldisplay' => $calldisplay,
            )
        );
    }
}
