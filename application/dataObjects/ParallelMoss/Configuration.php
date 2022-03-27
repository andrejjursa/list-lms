<?php

namespace Application\DataObjects\ParallelMoss;

class Configuration
{
    /**
     * @var array<int, int[]>
     */
    private $solutions = [];
    
    /**
     * @var string[]
     */
    private $baseFiles = [];
    
    /**
     * @var string
     */
    private $language;
    
    /**
     * @var int
     */
    private $sensitivity = 10;
    
    /**
     * @var int
     */
    private $numberOfResults = 250;
    
    public function __construct(string $language, int $sensitivity, int $numberOfResults)
    {
        $this->language = $language;
        $this->sensitivity = $sensitivity;
        $this->numberOfResults = $numberOfResults;
        $this->solutions = [];
        $this->baseFiles = [];
    }
    
    /**
     * @return array<int, int[]>
     */
    public function getSolutions(): array
    {
        return $this->solutions;
    }
    
    /**
     * @return string[]
     */
    public function getBaseFiles(): array
    {
        return $this->baseFiles;
    }
    
    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
    
    /**
     * @return int
     */
    public function getSensitivity(): int
    {
        return $this->sensitivity;
    }
    
    /**
     * @return int
     */
    public function getNumberOfResults(): int
    {
        return $this->numberOfResults;
    }
    
    public function addBaseFile(string $baseFile): void
    {
        if (!in_array($baseFile, $this->baseFiles, true)) {
            $this->baseFiles[] = $baseFile;
        }
    }
    
    public function addSolution(int $solutionId, int $version): void
    {
        if (isset($this->solutions[$solutionId]) && in_array($version, $this->solutions[$solutionId])) {
            return;
        }
        
        $this->solutions[$solutionId][] = $version;
    }
    
    public function toArray(): array
    {
        return [
            'language' => $this->language,
            'sensitivity' => $this->sensitivity,
            'numberOfResults' => $this->numberOfResults,
            'solutions' => $this->solutions,
            'baseFiles' => $this->baseFiles,
        ];
    }
    
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}