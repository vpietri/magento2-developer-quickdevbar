<?php
namespace ADM\QuickDevBar\Controller\Action;

class ConfigUpdate extends \ADM\QuickDevBar\Controller\Index
{
    /**
     * @var \Magento\Config\Model\Resource\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     *
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \ADM\QuickDevBar\Helper\Data $qdbHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Config\Model\Resource\Config $resourceConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
            \Magento\Framework\View\LayoutFactory $layoutFactory,
            \Magento\Config\Model\ResourceModel\Config $resourceConfig,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory);

        $this->_resourceConfig = $resourceConfig;
        $this->_storeManager = $storeManager;
        $this->_resultForwardFactory = $resultForwardFactory;
    }



    public function execute()
    {

        $error = false;
        $output = '';
        $config = $this->getRequest()->getParam('config');
        try {
            if (empty($config['key'])) {
                throw new \Exception('Key is missing');
            } else {
                $configKey = $config['key'];
            }

            $scopeList = ['default', 'websites', 'stores', 'auto'];
            if (empty($config['scope']) or !in_array($config['scope'], $scopeList)) {
                throw new \Exception('Scope is missing');
            } else {
                $configScope = $config['scope'];

                if($configScope=='auto') {
                    switch ($configKey) {
                        case 'template_hints_admin':
                        case 'template_hints_storefront':
                        case 'template_hints_blocks':
                        case 'translate':
                            $configScope = 'stores';
                            break;
                        default:
                            throw new \Exception('Scope auto is unrecognized');
                            break;
                    }
                }
            }

            if (empty($config['value'])) {
                $configValue = 1;
            } else {
                $configValue = $config['value'];
            }

            switch ($configScope) {
                case 'stores':
                    $configScopeId = $this->_storeManager->getStore()->getId();
                    break;
                case 'websites':
                    $configScopeId = $this->_storeManager->getWebsite()->getId();
                    break;
                default:
                    $configScopeId = 0;
                    break;
            }


            switch ($configKey) {
                case 'template_hints_admin':
                case 'template_hints_storefront':
                case 'template_hints_blocks':

                    $configValue = ($this->_qdbHelper->getConfig('dev/debug/' . $configKey, $configScope, $configScopeId)) ? 0 : 1;
                    $this->_resourceConfig->saveConfig('dev/debug/' . $configKey , $configValue, $configScope, $configScopeId);
                    $output = ucwords(str_replace('_', ' ', $configKey)) . " set " . ($configValue ? 'On' : 'Off');
                    break;
                case 'translate':

                    $configValue = ($this->_qdbHelper->getConfig('dev/translate_inline/active', $configScope, $configScopeId)) ? 0 : 1;

                    $this->_resourceConfig->saveConfig('dev/translate_inline/active', $configValue, $configScope, $configScopeId);
                    $output = "Translate set " . ($configValue ? 'On' : 'Off');
                    break;
                default:
                    break;
            }

            if ($output) {
                $this->_qdbHelper->setControllerMessage($output);
            }


        } catch ( \Exception $e) {
            $output = $e->getMessage();
            $error = true;
        }

        if (!$error) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('cache');
            return $resultForward;
        } else {
            $this->_view->loadLayout();
            $resultRaw = $this->_resultRawFactory->create();
            return $resultRaw->setContents($output);
        }

    }
}