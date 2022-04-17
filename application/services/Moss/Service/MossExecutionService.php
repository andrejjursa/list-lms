<?php

namespace Application\Services\Moss\Service;

use Application\DataObjects\ParallelMoss\Configuration;
use Application\Exceptions\MossExecution\MossDatabaseRecordNotFound;
use Application\Exceptions\MossExecution\MossModelInWrongStatus;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use CI_Controller;
use Exception;
use Parallel_moss_comparison;
use Ramsey\Uuid\Uuid;

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
        
        $dir = $this->createTemporaryFolder();
        
        //sleep(5);
        
        
        $configuration = $this->configurationBuilder->fromSavedData($mossModel->configuration);
        
        try {
            $executeWithFiles = $this->prepareConfiguration($configuration, $dir);
        } catch (\Throwable $exception) {
            $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FAILED);
            return false;
        } finally {
            unlink_recursive($dir, true);
        }
        
        if (rand(0, 1) === 1) {
            $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FINISHED);
            $mossModel->result_link = 'https://www.google.com';
            $mossModel->save();
            return true;
        } else {
            $this->setMossModelStatus($mossModel, Parallel_moss_comparison::STATUS_FAILED);
            return false;
        }
    }
    
    private function &getCodeigniter(): CI_Controller
    {
        /** @var CI_Controller $CI */
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->helper('application');
        
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
     *
     * @return void
     */
    private function setMossModelStatus(Parallel_moss_comparison $model, string $status): void
    {
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
        $model->save();
    }
    
    /**
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
     * @return array{baseFiles: string[]}
     *
     * @throws Exception
     */
    private function prepareConfiguration(Configuration $configuration, string $dir): array
    {
        $output['baseFiles'] = $this->prepareBaseFiles($configuration, $dir);
        
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
}