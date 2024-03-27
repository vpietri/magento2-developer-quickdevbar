<?php

namespace ADM\QuickDevBar\Console\Command;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductAttributesCleanUp
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EnableToolBar extends AbstractStatusToolbar
{
    const ACTIVATE_SQL_QDB_PROFILER="sql-qdb-profiler";

    /**
     * @var string
     */
    protected $name='dev:quickdevbar:enable';

    /**
     * @var string
     */
    protected $description='Activate quickdevbar';

    /**
     * @var int
     */
    protected $status=1;

    /**
     * @var string
     */
    protected $message="Toolbar enabled";

    protected function configure()
    {

        parent::configure();
        $this->addOption(
            self::ACTIVATE_SQL_QDB_PROFILER,
            null,
            InputOption::VALUE_NONE,
            'Use QDB SQL profiler with backtrace'
        );
    }

    protected function getProfilerClass(InputInterface $input)
    {
        if ($input->getOption(self::ACTIVATE_SQL_QDB_PROFILER)) {
            return \ADM\QuickDevBar\Profiler\Db::class;
        }
        return parent::getProfilerClass($input);
    }
}
