<?php

namespace Application\Services\Moss\Service;

use Application\DataObjects\ParallelMoss\Configuration;

class ConfigurationBuilder
{
    public function fromPostData($rawConfig): Configuration
    {
        return $this->buildConfiguration(is_array($rawConfig) ? $rawConfig : []);
    }
    
    /**
     * @param array{
     *  language: string,
     *  sensitivity: int,
     *  numberOfResults: int,
     *  solutions: array<int, int[]>,
     *  baseFiles: string[]
     * } $savedData
     *
     * @return Configuration
     */
    public function fromSavedData(array $savedData): Configuration
    {
        $mossConfig = new Configuration(
            $savedData['language'] ?? 'unknown',
            $savedData['sensitivity'] ?? 10,
            $savedData['numberOfResults'] ?? 250
        );
        
        $this->restoreSolutions($savedData['solutions'], $mossConfig);
        $this->restoreBaseFiles($savedData['baseFiles'], $mossConfig);
        
        return $mossConfig;
    }
    
    /**
     * @param array $rawConfig
     *
     * @return Configuration
     */
    private function buildConfiguration(array $rawConfig): Configuration
    {
        $mossConfig = new Configuration(
            $rawConfig['moss_setup']['l'] ?? 'unknown',
            (int)$rawConfig['moss_setup']['m'] ?? 10,
            (int)$rawConfig['moss_setup']['n'] ?? 250
        );
        
        $this->addComparisons($rawConfig, $mossConfig);
        
        return $mossConfig;
    }
    
    /**
     * @param array         $rawConfig
     * @param Configuration $mossConfig
     *
     * @return void
     */
    private function addComparisons(array $rawConfig, Configuration &$mossConfig): void
    {
        foreach ($rawConfig['comparison'] as $comparisonData) {
            foreach ($comparisonData['baseFile'] ?? [] as $baseFiles) {
                array_walk($baseFiles, function (string $baseFile) use ($mossConfig) {
                    $mossConfig->addBaseFile($baseFile);
                });
            }
            foreach ($comparisonData['solution'] ?? [] as $solutionId => $solutionData) {
                if (isset($solutionData['selected']) && (bool)(int)$solutionData['selected']) {
                    $mossConfig->addSolution((int)$solutionId, (int)$solutionData['version']);
                }
            }
        }
    }
    
    /**
     * @param array<int, int[]> $solutions
     * @param Configuration     $mossConfig
     *
     * @return void
     */
    private function restoreSolutions(array $solutions, Configuration &$mossConfig): void
    {
        foreach ($solutions as $solutionId => $versions) {
            foreach ($versions as $version) {
                $mossConfig->addSolution($solutionId, $version);
            }
        }
    }
    
    /**
     * @param string[]      $baseFiles
     * @param Configuration $mossConfig
     *
     * @return void
     */
    private function restoreBaseFiles(array $baseFiles, Configuration &$mossConfig): void
    {
        foreach ($baseFiles as $baseFile) {
            $mossConfig->addBaseFile($baseFile);
        }
    }
}