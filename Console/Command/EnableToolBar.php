<?php

namespace ADM\QuickDevBar\Console\Command;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class ProductAttributesCleanUp
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EnableToolBar extends AbstractStatusToolbar
{
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
}
