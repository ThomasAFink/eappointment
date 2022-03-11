<?php
/**
 *
 * @package zmsslim
 *
 */
namespace BO\Slim\Tests\Controller;

class Get extends BaseController
{

    /**
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $message = $request->getparam('message');
        return \BO\Slim\Render::withHtml(
            $response,
            'unittest.twig',
            array(
                'message' => $message,
                'lang' => ($request->getAttribute('route')) ? $request->getAttribute('route')->getArgument('lang') : '',
                'title' => 'GET test title'
            )
        );
    }
}
