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
    /** @var ConfigurationBuilder */
    protected $configurationBuilder;
    
    public function __construct(ConfigurationBuilder $configurationBuilder)
    {
        $this->configurationBuilder = $configurationBuilder;
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
        $this->assertMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_QUEUED);
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
                $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FAILED, $response);
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
            return true;
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
     * @param string                   $status
     *
     * @return void
     *
     * @throws MossModelInWrongStatus
     */
    private function assertMossModelStatus(Parallel_moss_comparison $model, string $status): void
    {
        if ($model->status !== $status) {
            throw new MossModelInWrongStatus(sprintf(
                'Wrong status of the moss database record, has to be "%s" and is "%s".',
                $status,
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
        }
        if (in_array(
            $status,
            [Parallel_moss_comparison::STATUS_FINISHED, Parallel_moss_comparison::STATUS_FAILED],
            true
        )) {
           $model->processing_finish = date('Y-m-d H:i:s.u');
        }
        if ($status === Parallel_moss_comparison::STATUS_FINISHED && $result !== null) {
            $model->result_link = $result;
        }
        if ($status === Parallel_moss_comparison::STATUS_FAILED && $result !== null) {
            $model->failure_message = $result;
        }
        $model->save();
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