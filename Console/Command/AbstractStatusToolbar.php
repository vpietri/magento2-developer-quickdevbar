<?php

namespace ADM\QuickDevBar\Console\Command;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
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

    public function __construct(Config                $resourceConfig,
                                Manager               $cacheManager,
                                EventManagerInterface $eventManager,
                                string                $name = null)
    {
        parent::__construct($name);
        $this->resourceConfig = $resourceConfig;
        $this->cacheManager = $cacheManager;
        $this->eventManager = $eventManager;
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
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->resourceConfig->saveConfig('dev/quickdevbar/enable', $this->status);
        $output->writeln("<info>" . $this->message . "</info>");

        $this->eventManager->dispatch('adminhtml_cache_flush_system');
        $this->cacheManager->clean(['config']);
        //TODO: Conditionner
        if ($input->getOption(self::CLEAN_HTML)) {
            $output->writeln("<info>Front cache block_html & full_page cleared</info>");

            //$this->cacheManager->clean(['config', 'block_html', 'full_page']);
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
