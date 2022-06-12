<?php

namespace Application\Services\Moss\RequestMapper;

use Application\Services\Moss\Request\GetComparisonsRequest;

class GetComparisonsRequestMapper
{
    private const PAGE_SIZE_DEFAULT = 25;
    private const PAGE_SIZE_ALLOWED_VALUES = [self::PAGE_SIZE_DEFAULT, 50, 100];
    private const PAGE_DEFAULT = 1;
    
    public function map(array $httpData): GetComparisonsRequest
    {
        $getComparisonsRequest = new GetComparisonsRequest();
        
        $this->mapPage($httpData, $getComparisonsRequest);
        $this->mapPageSize($httpData, $getComparisonsRequest);
        
        return $getComparisonsRequest;
    }
    
    private function mapPage(array $httpData, GetComparisonsRequest &$getComparisonsRequest): void
    {
        $page = self::PAGE_DEFAULT;
        if (isset($httpData['page'])) {
            $page = max((int)$httpData['page'], self::PAGE_DEFAULT);
        }
        $getComparisonsRequest->setPage($page);
    }
    
    private function mapPageSize(array $httpData, GetComparisonsRequest &$getComparisonsRequest): void
    {
        $pageSize = self::PAGE_SIZE_DEFAULT;
        if (isset($httpData['pageSize'])) {
            $pageSize = (int)$httpData['pageSize'];
            if (!in_array($pageSize, self::PAGE_SIZE_ALLOWED_VALUES, true)) {
                $pageSize = self::PAGE_SIZE_DEFAULT;
            }
        }
        $getComparisonsRequest->setPageSize($pageSize);
    }
}