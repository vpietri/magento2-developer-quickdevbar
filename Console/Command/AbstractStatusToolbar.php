<?php

namespace ADM\QuickDevBar\Console\Command;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductAttributesCleanUp
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractStatusToolbar extends \Symfony\Component\Console\Command\Command
{

    const CLEAN_HTML="clear-front-cache";

    const ACTIVATE_SQL_PROFILER="sql-profiler";

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $message;


    protected  $resourceConfig;
    private  $cacheManager;
    private  $eventManager;
    private  $writer;
    private  $arrayManager;

    public function __construct(Config                $resourceConfig,
                                Manager               $cacheManager,
                                EventManagerInterface $eventManager,
                                Writer                $writer,
                                ArrayManager          $arrayManager,
                                string                $name = null)
    {
        parent::__construct($name);
        $this->resourceConfig = $resourceConfig;
        $this->cacheManager = $cacheManager;
        $this->eventManager = $eventManager;
        $this->writer = $writer;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->name);
        $this->setDescription($this->description);
        $this->addOption(
            self::CLEAN_HTML,
            null,
            InputOption::VALUE_NONE,
            'Clear front cache block_html & full_page'
        );
        $this->addOption(
            self::ACTIVATE_SQL_PROFILER,
            null,
            InputOption::VALUE_NONE,
            'Activate/deactivate SQL profiler'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->resourceConfig->saveConfig('dev/quickdevbar/enable', $this->status);
        $output->writeln("<info>" . $this->message . "</info>");

        $this->eventManager->dispatch('adminhtml_cache_flush_system');

        $cachesToClear=['config'];
        if ($input->getOption(self::CLEAN_HTML)) {
            $cachesToClear = array_merge($cachesToClear, ['block_html', 'full_page']);
        }

        $lockTargetPath = ConfigFilePool::APP_ENV;
        $profilerClass = $this->getProfilerClass($input);
        if(!$this->status) {
            $this->writer->saveConfig(
                [$lockTargetPath => $this->arrayManager->set('db/connection/default/profiler', [], 0)],
                false
            );
            $output->writeln("<info>SQL profiler is disabled in env.php</info>");
        } elseif ($input->getOption(self::ACTIVATE_SQL_PROFILER) || $profilerClass) {

            $profilerValue = [ 'enabled'=>1];
            if($profilerClass) {
                $profilerValue['class']=$profilerClass;
            }

            $this->writer->saveConfig(
                [$lockTargetPath => $this->arrayManager->set('db/connection/default/profiler', [], $profilerValue)],
                false
            );
            $output->writeln("<info>SQL profiler is enabled in env.php</info>");
        }

        $this->cacheManager->clean($cachesToClear);
        $output->writeln("<info>Cache cleared: ".implode(",", $cachesToClear)."</info>");


        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }

    protected function getProfilerClass(InputInterface $input)
    {
        return '';
    }
}
