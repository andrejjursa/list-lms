<?php

namespace Application\Services\Moss;

use Application\Services\Moss\Request\GetComparisonsRequest;
use Application\Services\Moss\RequestMapper\GetComparisonsRequestMapper;

class RequestFactory
{
    /** @var GetComparisonsRequestMapper */
    private $getComparisonsRequestMapper;
    
    public function __construct(GetComparisonsRequestMapper $getComparisonsRequestMapper) {
        $this->getComparisonsRequestMapper = $getComparisonsRequestMapper;
    }
    
    /**
     * @param array|bool $httpData
     *
     * @return GetComparisonsRequest
     */
    public function constructGetComparisonsRequest($httpData): GetComparisonsRequest
    {
        if (!is_array($httpData)) {
            $httpData = [];
        }
        return $this->getComparisonsRequestMapper->map($httpData);
    }
}