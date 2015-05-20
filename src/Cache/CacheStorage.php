<?php
namespace Otg\Ean\Cache;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Subscriber\Cache\CacheStorage as BaseStorage;

/**
 * Cache storage with support for the sig query parameter
 */
class CacheStorage extends BaseStorage
{
    public function cache(
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $request = clone $request;
        $request->getQuery()->remove('sig');

        parent::cache($request, $response);
    }

    public function delete(RequestInterface $request)
    {
        $request = clone $request;
        $request->getQuery()->remove('sig');

        parent::delete($request);
    }

    public function fetch(RequestInterface $request)
    {
        $request = clone $request;
        $request->getQuery()->remove('sig');

        return parent::fetch($request);
    }
}
