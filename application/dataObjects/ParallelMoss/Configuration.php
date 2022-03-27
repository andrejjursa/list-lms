<?php

namespace Application\DataObjects\ParallelMoss;

use Application\Exceptions\MossConfiguration\WrongArrayStructureException;
use Application\Exceptions\MossConfiguration\WrongArrayValueException;

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
    
    /**
     * @param array $array
     *
     * @return Configuration
     */
    public static function fromArray(array $array): Configuration
    {
        if (!array_key_exists('language', $array) || !array_key_exists('sensitivity', $array)
            || !array_key_exists('numberOfResults', $array) || !array_key_exists('solutions', $array)
            || !array_key_exists('baseFiles', $array)
        ) {
            throw new WrongArrayStructureException('Array does not contains required fields.');
        }
        if (!is_string($array['language'])) {
            throw new WrongArrayValueException('Language must be a string.');
        }
        if (!is_int($array['sensitivity'])) {
            throw new WrongArrayValueException('Sensitivity must be an integer.');
        }
        if (!is_int($array['numberOfResults'])) {
            throw new WrongArrayValueException('Number of results must be an integer.');
        }
        
        $configuration = new Configuration($array['language'], $array['sensitivity'], $array['numberOfResults']);
        
        foreach ($array['baseFiles'] as $baseFile) {
            if (!is_string($baseFile)) {
                throw new WrongArrayValueException(sprintf(
                    'Base file name "%s" must be a string, %s given.',
                    (string)$baseFile,
                    is_object($baseFile) ? get_class($baseFile) : gettype($baseFile)
                ));
            }
            $configuration->addBaseFile($baseFile);
        }
        
        foreach ($array['solutions'] as $solutionId => $versions) {
            if (!is_int($solutionId)) {
                throw new WrongArrayValueException(sprintf(
                    'Solution ID "%s" must be an integer, %s given.',
                    (string)$solutionId,
                    is_object($solutionId) ? get_class($solutionId) : gettype($solutionId)
                ));
            }
            foreach ($versions as $version) {
                if (!is_int($version)) {
                    throw new WrongArrayValueException(sprintf(
                        'Version "%s" of solution ID %s must be an integer, %s given.',
                        (string)$version,
                        (string)$solutionId,
                        is_object($version) ? get_class($version) : gettype($version)
                    ));
                }
                $configuration->addSolution($solutionId, $version);
            }
        }
        return $configuration;
    }
}