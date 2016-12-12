<?php

namespace Concrete\Core\Http;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use JsonSerializable;

interface ResponseFactoryInterface
{

    /**
     * Create a response
     * @param string $content The body of the response
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function create($content, $code = Response::HTTP_OK, array $headers = array());

    /**
     * Create a response
     * @param array|object|JsonSerializable $data The json data
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function json($data, $code = Response::HTTP_OK, array $headers = array());

    /**
     * Create a page not found response
     * @param string $content The body of the response
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function notFound($content, $code = Response::HTTP_NOT_FOUND, $headers = array());

    /**
     * Create an error response
     * @param string $content The body of the response
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function error($content, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = array());

    /**
     * Create a forbidden response
     * @param string $requestUrl The url that this requests used. This will be used to redirect after login
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function forbidden($requestUrl, $code = Response::HTTP_FORBIDDEN, $headers = array());

    /**
     * Create a redirect response
     * @param string $to The URL to redirect to
     * @param int $code The response code
     * @param array $headers Optional headers to include
     * @return SymfonyResponse
     */
    public function redirect($to, $code = Response::HTTP_MOVED_PERMANENTLY, $headers = array());

    /**
     * Create a response from a view object
     * @param \Concrete\Core\View\View $view
     * @param int $code
     * @param array $headers
     * @return SymfonyResponse
     */
    public function view(View $view, $code = Response::HTTP_OK, $headers = array());

    /**
     * Create a response from a controller object
     * @param Controller $controller
     * @param int $code
     * @param array $headers
     * @return SymfonyResponse
     */
    public function controller(Controller $controller, $code = Response::HTTP_OK, $headers = array());

    /**
     * Create a response from a collection object
     * @param Collection $collection
     * @param int $code
     * @param array $headers
     * @return SymfonyResponse
     */
    public function collection(Collection $collection, $code = Response::HTTP_OK, $headers = array());

}
