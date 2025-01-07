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
abstract class Database extends \Symfony\Component\Console\Command\Command
{
    const DB_TABLENAME='table';
    /**
     * @var string
     */
    protected $name='dev:quickdevbar:dbschema';

    /**
     * @var string
     */
    protected $description='Show db schema';

    private  $resource;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource,
                                string                $name = null)
    {
        parent::__construct($name);

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->name);
        $this->setDescription($this->description);
        $this->addArgument(
            self::DB_TABLENAME,
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Table name'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $connection = $this->resource->getConnection();
        $ddl = $connection->describeTable($input->getArgument(self::DB_TABLENAME));



        $dbSchemaArr = ['<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">'];
        $dbSchemaArr[] = '    <table name="catalog_product_entity_datetime" resource="default" engine="innodb" comment="Catalog Product Datetime Attribute Backend Table">';

        foreach ($ddl as $ddlColumn) {

            $xmlColumn=[];
            $xmlColumn['name'] = $ddlColumn['COLUMN_NAME'];
            $xmlColumn['xsi:type'] = $ddlColumn['DATA_TYPE'];
            $xmlColumn['nullable'] = $ddlColumn['NULLABLE'];
            if($ddlColumn['DEFAULT']) {
                $xmlColumn['default'] = $ddlColumn['DEFAULT'];
            }
            $xmlColumn['scale'] = $ddlColumn['SCALE'];
            $xmlColumn['precision'] = $ddlColumn['PRECISION'];
            $xmlColumn['unsigned'] = $ddlColumn['UNSIGNED'];
            if($ddlColumn['PRIMARY']) {
                $xmlColumn['primary'] = $ddlColumn['PRIMARY'];
            }
            if($ddlColumn['IDENTITY']) {
                $xmlColumn['identity'] = $ddlColumn['IDENTITY'];
            }
            $xmlColumn['length'] = $ddlColumn['LENGTH'];
            $xmlColumn['comment'] = $ddlColumn['COLUMN_NAME'];
            array_filter($xmlColumn, fn($var) => $var !== null);
            array_walk($xmlColumn, function(&$item, $key) {
                if(!is_string($item)) {
                    $item = empty($item) ? 'false' : 'true';
                }
                $item = $key . '="'.$item.'"';
            });

            $dbSchemaArr[] = '        <column ' . implode(' ', $xmlColumn) . '/>';
        }
        $dbSchemaArr[] = '    </table>';
        $dbSchemaArr[] = '</schema>';




        $output->writeln("<info>" . implode(PHP_EOL, $dbSchemaArr) . "</info>");



//        $this->eventManager->dispatch('adminhtml_cache_flush_system');
//
//        $cachesToClear=['config'];
//        if ($input->getOption(self::CLEAN_HTML)) {
//            $cachesToClear = array_merge($cachesToClear, ['block_html', 'full_page']);
//        }
//
//        $lockTargetPath = ConfigFilePool::APP_ENV;
//        $profilerClass = $this->getProfilerClass($input);
//        if(!$this->status) {
//            $this->writer->saveConfig(
//                [$lockTargetPath => $this->arrayManager->set('db/connection/default/profiler', [], 0)],
//                false
//            );
//            $output->writeln("<info>SQL profiler is disabled in env.php</info>");
//        } elseif ($input->getOption(self::ACTIVATE_SQL_PROFILER) || $profilerClass) {
//
//            $profilerValue = [ 'enabled'=>1];
//            if($profilerClass) {
//                $profilerValue['class']=$profilerClass;
//            }
//
//            $this->writer->saveConfig(
//                [$lockTargetPath => $this->arrayManager->set('db/connection/default/profiler', [], $profilerValue)],
//                false
//            );
//            $output->writeln("<info>SQL profiler is enabled in env.php</info>");
//        }
//
//        $this->cacheManager->clean($cachesToClear);
//        $output->writeln("<info>Cache cleared: ".implode(",", $cachesToClear)."</info>");


        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }

    protected function getProfilerClass(InputInterface $input)
    {
        return '';
    }
}
