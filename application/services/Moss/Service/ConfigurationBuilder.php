<?php

namespace Application\Services\Moss\Service;

use Application\DataObjects\ParallelMoss\Configuration;

class ConfigurationBuilder
{
    public function fromPostData($rawConfig): Configuration
    {
        return $this->buildConfiguration(is_array($rawConfig) ? $rawConfig : []);
    }
    
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
}