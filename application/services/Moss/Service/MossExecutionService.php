<?php

namespace Application\Services\Moss\Service;

use Application\DataObjects\ParallelMoss\Configuration;
use Application\Exceptions\MossExecution\DatabaseRecordNotFoundException;
use Application\Exceptions\MossExecution\GeneralExecutionException;
use Application\Exceptions\MossExecution\MossDatabaseRecordNotFound;
use Application\Exceptions\MossExecution\MossModelInWrongStatus;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use CI_Controller;
use Exception;
use Parallel_moss_comparison;
use Ramsey\Uuid\Uuid;
use Solution;
use Task_set;

class MossExecutionService
{
    public const RESTARTS_MAXIMUM = 5;
    public const RESTARTS_DELAYS = [
        0 => 0,
        1 => 10,
        2 => 60,
        3 => 180,
        4 => 180,
        5 => 180,
        6 => 180,
        7 => 180,
        8 => 180, 
        9 => 180,
        10 => 180,
        11 => 180,
        12 => 180,
        13 => 180,
        14 => 180,
        15 => 180,
        16 => 180,
        17 => 180,
        18 => 180,
        19 => 180,
        20 => 180,
        21 => 180,
        22 => 180,
        23 => 180,
        24 => 180,
        25 => 180,
        26 => 180,
        27 => 180,
        28 => 180,
        29 => 180,
        30 => 180,
        31 => 180,
        32 => 180,
        33 => 180,
        34 => 180,
        35 => 180,
        36 => 180,
        37 => 180,
        38 => 180,
        39 => 180,
        40 => 180,
        41 => 180,
        42 => 180,
        43 => 180,
        44 => 180,
        45 => 180,
        46 => 180,
        47 => 180,
        48 => 180,
        49 => 180,
        50 => 180,
        51 => 180,
        52 => 180,
        53 => 180,
        54 => 180,
        55 => 180,
        56 => 180,
        57 => 180,
        58 => 180,
        59 => 180,
        60 => 180,
        61 => 180,
        62 => 180,
        63 => 180,
        64 => 180,
        65 => 180,
        66 => 180,
        67 => 180,
        68 => 180,
        69 => 180,
        70 => 180,
        71 => 180,
        72 => 180,
        73 => 180,
        74 => 180,
        75 => 180,
        76 => 180,
        77 => 180,
        78 => 180,
        79 => 180,
        80 => 180,
        81 => 180,
        82 => 180,
        83 => 180,
        84 => 180,
        85 => 180,
        86 => 180,
        87 => 180,
        88 => 180,
        89 => 180,
        90 => 180,
        91 => 180,
        92 => 180,
        93 => 180,
        94 => 180,
        95 => 180,
        96 => 180,
        97 => 180,
        98 => 180,
        99 => 180,
        100 => 180,
        101 => 180,
        102 => 180,
        103 => 180,
        104 => 180,
        105 => 180,
        106 => 180,
        107 => 180,
        108 => 180,
        109 => 180,
        110 => 180,
        111 => 180,
        112 => 180,
        113 => 180,
        114 => 180,
        115 => 180,
        116 => 180,
        117 => 180,
        118 => 180,
        119 => 180,
        120 => 180,
        121 => 180,
        122 => 180,
        123 => 180,
        124 => 180,
        125 => 180,
        126 => 180,
        127 => 180,
        128 => 180,
        129 => 180,
        130 => 180,
        131 => 180,
        132 => 180,
        133 => 180,
        134 => 180,
        135 => 180,
        136 => 180,
        137 => 180,
        138 => 180,
        139 => 180,
        140 => 180,
        141 => 180,
        142 => 180,
        143 => 180,
        144 => 180,
        145 => 180,
        146 => 180,
        147 => 180,
        148 => 180,
        149 => 180,
        150 => 180,
        151 => 180,
        152 => 180,
        153 => 180,
        154 => 180,
        155 => 180,
        156 => 180,
        157 => 180,
        158 => 180,
        159 => 180,
        160 => 180,
        161 => 180,
        162 => 180,
        163 => 180,
        164 => 180,
        165 => 180,
        166 => 180,
        167 => 180,
        168 => 180,
        169 => 180,
        170 => 180,
        171 => 180,
        172 => 180,
        173 => 180,
        174 => 180,
        175 => 180,
        176 => 180,
        177 => 180,
        178 => 180,
        179 => 180,
        180 => 180,
        181 => 180,
        182 => 180,
        183 => 180,
        184 => 180,
        185 => 180,
        186 => 180,
        187 => 180,
        188 => 180,
        189 => 180,
        190 => 180,
        191 => 180,
        192 => 180,
        193 => 180,
        194 => 180,
        195 => 180,
        196 => 180,
        197 => 180,
        198 => 180,
        199 => 180,
        200 => 180,
    ];
    
    /**
     * @var null|string
     */
    protected $status = null;
    
    /**
     * @var null|int
     */
    protected $restarts = null;
    
    /** @var ConfigurationBuilder */
    protected $configurationBuilder;
    
    public function __construct(ConfigurationBuilder $configurationBuilder)
    {
        $this->configurationBuilder = $configurationBuilder;
    }
    
    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    /**
     * @return int|null
     */
    public function getRestarts(): ?int
    {
        return $this->restarts;
    }
    
    /**
     * @param StartComparisonMessage $startComparisonMessage
     *
     * @return bool
     *
     * @throws MossDatabaseRecordNotFound
     * @throws MossModelInWrongStatus
     * @throws Exception
     */
    public function execute(StartComparisonMessage $startComparisonMessage): bool
    {
        $this->getCodeigniter();
        
        $id = $startComparisonMessage->getParallelMossComparisonID();
        
        $mossModel = $this->getParallelMossComparisonModel($id);
        $this->assertMossModelStatus(
            $mossModel,
            [Parallel_moss_comparison::STATUS_QUEUED, Parallel_moss_comparison::STATUS_RESTART]
        );
        $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_PROCESSING);
    
        $dir = null;
        try {
            $configuration = $this->configurationBuilder->fromSavedData($mossModel->configuration);
            
            $dir = $this->createTemporaryFolder();
            
            $response = '';
            if($this->doExecute($configuration, $dir, $response)) {
                $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FINISHED, $response);
                return true;
            } else {
                if (($mossModel->restarts ?? 0) < self::RESTARTS_MAXIMUM) {
                    $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_RESTART);
                    $this->incrementRestarts($mossModel);
                } else {
                    $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FAILED, $response);
                }
                return false;
            }
        } catch (\Throwable $exception) {
            $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FAILED, $exception->getMessage());
            return false;
        } finally {
            if ($dir !== null) {
                unlink_recursive($dir, true);
            }
        }
    }
    
    private function doExecute(Configuration $configuration, string $dir, string &$response): bool
    {
        $CI =& $this->getCodeigniter();
        $executeWithFiles = $this->prepareConfiguration($configuration, $dir);
        
        /** @var \mosslib $mosslib */
        $mosslib = $CI->mosslib;
        $mosslib->setLanguage($configuration->getLanguage());
        $mosslib->setResultLimit($configuration->getNumberOfResults());
        $mosslib->setIngoreLimit($configuration->getSensitivity());
        
        $currentDir = getcwd();
        
        try {
            chdir($dir);
            foreach ($executeWithFiles['baseFiles'] as $baseFile) {
                $file = $this->getFilePathInDirectory($baseFile, $dir);
                $mosslib->addBaseFile($file);
            }
            foreach ($executeWithFiles['solutions'] as $solution) {
                $file = $this->getFilePathInDirectory($solution, $dir);
                $mosslib->addFile($file);
            }
            $response = trim($mosslib->send());
            if ($response !== '') {
                return true;
            }
            $response = 'MOSS does not returned link to results.';
            return false;
        } catch (\Throwable $exception) {
            $response = $exception->getMessage();
            return false;
        } finally {
            chdir($currentDir);
        }
    }
    
    private function getFilePathInDirectory(string $originalFilePath, string $directoryPrefix): string
    {
        if (mb_strpos($originalFilePath, $directoryPrefix, 0) === 0) {
            $truncated = mb_substr($originalFilePath, mb_strlen($directoryPrefix));
            if (mb_substr($truncated, 0, mb_strlen(DIRECTORY_SEPARATOR)) === DIRECTORY_SEPARATOR) {
                $truncated = mb_substr($truncated, mb_strlen(DIRECTORY_SEPARATOR));
            }
            return $truncated;
        }
        return $originalFilePath;
    }
    
    private function &getCodeigniter(): CI_Controller
    {
        /** @var CI_Controller $CI */
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->helper('application');
        $CI->config->load('moss');
        $CI->load->library('mosslib');
        
        return $CI;
    }
    
    /**
     * @param int $id
     *
     * @return Parallel_moss_comparison
     *
     * @throws MossDatabaseRecordNotFound
     */
    private function getParallelMossComparisonModel(int $id): Parallel_moss_comparison
    {
        $parallelMoss = new Parallel_moss_comparison();
        $parallelMoss->get_by_id($id);
        
        if (!$parallelMoss->exists()) {
            throw new MossDatabaseRecordNotFound(sprintf(
                'Can\'t find moss database record with id "%d".',
                $id
            ));
        }
        
        return $parallelMoss;
    }
    
    /**
     * @param Parallel_moss_comparison $model
     * @param array<string>            $status
     *
     * @return void
     *
     * @throws MossModelInWrongStatus
     */
    private function assertMossModelStatus(Parallel_moss_comparison $model, array $status): void
    {
        if (!in_array($model->status, $status, true)) {
            throw new MossModelInWrongStatus(sprintf(
                'Wrong status of the moss database record, has to be one of "%s" and is "%s".',
                implode(', ', $status),
                $model->status
            ));
        }
    }
    
    /**
     * @param Parallel_moss_comparison $model
     * @param string                   $status
     * @param string|null              $result
     *
     * @return void
     */
    private function setMossModelStatus(
        Parallel_moss_comparison $model,
        string $status,
        ?string $result = null
    ): void {
        $model->status = $status;
        if ($status === Parallel_moss_comparison::STATUS_PROCESSING) {
            $model->processing_start = date('Y-m-d H:i:s.u');
            $model->processing_finish = null;
        }
        if (in_array(
            $status,
            [
                Parallel_moss_comparison::STATUS_FINISHED,
                Parallel_moss_comparison::STATUS_FAILED,
                Parallel_moss_comparison::STATUS_RESTART,
            ],
            true
        )) {
           $model->processing_finish = date('Y-m-d H:i:s.u');
        }
        if ($status === Parallel_moss_comparison::STATUS_FINISHED && $result !== null) {
            $model->result_link = $result;
        } else {
            $model->result_link = null;
        }
        if (in_array(
            $status,
            [Parallel_moss_comparison::STATUS_FAILED, Parallel_moss_comparison::STATUS_RESTART],
            true
            ) && $result !== null
        ) {
            $model->failure_message = $result;
        } else {
            $model->failure_message = null;
        }
        $model->save();
        $this->status = $model->status;
    }
    
    /**
     * @param Parallel_moss_comparison $model
     *
     * @return void
     */
    private function incrementRestarts(
        Parallel_moss_comparison $model
    ): void {
        if (($model->restarts ?? 0) >= self::RESTARTS_MAXIMUM) {
            return;
        }
        $model->restarts = ($model->restarts ?? 0) + 1;
        $model->save();
        $this->restarts = $model->restarts;
    }
    
    /**
     * @param string|null $where
     *
     * @return string
     * @throws Exception
     */
    private function createTemporaryFolder(?string $where = null): string
    {
        $ds = DIRECTORY_SEPARATOR;
        if ($where === null) {
            $basePath = realpath(BASEPATH . $ds . '..' . $ds . 'private' . $ds . 'moss') . $ds;
        } else {
            $basePath = realpath($where) . $ds;
        }
        
        do {
            $random = Uuid::uuid4()->toString();
        } while (file_exists($basePath . $random));
        mkdir($basePath . $random, DIR_WRITE_MODE, true);
        return $basePath . $random;
    }
    
    /**
     * @param Configuration $configuration
     * @param string        $dir
     *
     * @return array{baseFiles: string[], solutions: string[]}
     *
     * @throws Exception
     */
    private function prepareConfiguration(Configuration $configuration, string $dir): array
    {
        $output['baseFiles'] = $this->prepareBaseFiles($configuration, $dir);
        $output['solutions'] = $this->prepareSolutionFiles($configuration, $dir);
        
        return $output;
    }
    
    /**
     * @param Configuration $configuration
     * @param string $dir
     *
     * @return string[]
     *
     * @throws Exception
     */
    private function prepareBaseFiles(Configuration $configuration, string $dir): array
    {
        $subDir = $dir . DIRECTORY_SEPARATOR . 'b';
        mkdir($subDir, DIR_WRITE_MODE, true);
        
        $output = [];
        
        foreach ($configuration->getBaseFiles() as $baseFile) {
            /** @var string $normalized */
            $normalized = str_replace('\\', DIRECTORY_SEPARATOR, $baseFile);
            $parts = explode(DIRECTORY_SEPARATOR, $normalized);
            $filename = array_pop($parts);
            $dirname = $this->createTemporaryFolder($subDir);
            $to = $dirname . DIRECTORY_SEPARATOR . $filename;
            copy($baseFile, $to);
            $output[] = $to;
        }
        
        return $output;
    }
    
    /**
     * @param Configuration $configuration
     * @param string        $dir
     *
     * @return array
     *
     * @throws DatabaseRecordNotFoundException
     * @throws GeneralExecutionException
     */
    private function prepareSolutionFiles(Configuration $configuration, string $dir): array
    {
        $CI =& $this->getCodeigniter();
        
        $mossLangsExtensions = $CI->config->item('moss_langs_file_extensions');
        if (!is_array($mossLangsExtensions)) {
            $mossLangsExtensions = [];
        }
        
        $subDir = $dir . DIRECTORY_SEPARATOR . 's';
        mkdir($subDir, DIR_WRITE_MODE, true);
        
        $output = [];
        
        /** @var array<int, Task_set> $taskSets */
        $taskSets = [];
        
        foreach ($configuration->getSolutions() as $solutionId => $versions) {
            $solution = $this->getSolution($solutionId);
            $taskSet = $this->getTaskSetCached($solution->task_set_id, $taskSets);
            foreach ($versions as $version) {
                $file = $taskSet->get_student_files($solution->student_id, $version);
                if (count($file) === 1) {
                    $file = $file[$version];
                    $studentSubDir = $subDir . DIRECTORY_SEPARATOR . normalize($file['file_name']) . '_s'
                        . $solution->student_id . '_v' . $version;
                    mkdir($studentSubDir, DIR_WRITE_MODE, true);
                    $extractedFilesList = [];
                    if (!$taskSet->extract_student_zip_to_folder(
                        $file['file'],
                        $studentSubDir . DIRECTORY_SEPARATOR,
                        $mossLangsExtensions[$configuration->getLanguage()] ?? [],
                        $extractedFilesList
                    )) {
                        throw new GeneralExecutionException(sprintf(
                            'Can not extract student files from file "%s" for student ID "%d" version "%d".',
                            $file['file'],
                            $solution->student_id,
                            $version
                        ));
                    }
                    array_walk($extractedFilesList, function (string $file) use (&$output, $studentSubDir) {
                        $output[] = $studentSubDir . DIRECTORY_SEPARATOR . $file;
                    });
                }
            }
        }
        
        return $output;
    }
    
    /**
     * @param int $solutionId
     *
     * @return Solution
     *
     * @throws DatabaseRecordNotFoundException
     * @throws GeneralExecutionException
     */
    private function getSolution(int $solutionId): Solution
    {
        $solution = new Solution();
        $solution->get_by_id($solutionId);
        if (!$solution->exists()) {
            throw new DatabaseRecordNotFoundException(sprintf(
                'Solution ID "%d" is not found.',
                $solutionId
            ));
        }
        if ($solution->student_id === null) {
            throw new GeneralExecutionException(sprintf(
                'Solution ID "%d" does not have link to student.',
                $solution->id
            ));
        }
        return $solution;
    }
    
    /**
     * @param int   $taskSetID
     * @param array<int, Task_set> $taskSets
     *
     * @return Task_set
     *
     * @throws DatabaseRecordNotFoundException
     */
    private function getTaskSetCached(int $taskSetID, array &$taskSets): Task_set
    {
        if (isset($taskSets[$taskSetID]) && $taskSets[$taskSetID] instanceof Task_set
            && $taskSets[$taskSetID]->id === $taskSetID
        ) {
            return $taskSets[$taskSetID];
        }
        $taskSet = new Task_set();
        $taskSet->get_by_id($taskSetID);
        if (!$taskSet->exists()) {
            throw new DatabaseRecordNotFoundException(sprintf(
                'Task set ID "%d" is not found.',
                $taskSetID
            ));
        }
        $taskSets[$taskSetID] = $taskSet;
        return $taskSet;
    }
    
}
