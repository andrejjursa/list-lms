<?php

namespace Application\Services\Moss\Request;

class GetComparisonsRequest
{
    /** @var int */
    private $page = 1;
    
    /** @var int */
    private $pageSize = 25;
    
    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }
    
    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
    
    /**
     * @param int $page
     *
     * @return GetComparisonsRequest
     */
    public function setPage(int $page): GetComparisonsRequest
    {
        $this->page = $page;
        return $this;
    }
    
    /**
     * @param int $pageSize
     *
     * @return GetComparisonsRequest
     */
    public function setPageSize(int $pageSize): GetComparisonsRequest
    {
        $this->pageSize = $pageSize;
        return $this;
    }
}