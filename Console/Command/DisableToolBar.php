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
class DisableToolBar extends AbstractStatusToolbar
{
    /**
     * @var string
     */
    protected $name='dev:quickdevbar:disable';

    /**
     * @var string
     */
    protected $description='Disable quickdevbar';

    /**
     * @var int
     */
    protected $status=0;

    /**
     * @var string
     */
    protected $message="Toolbar disabled";

}
