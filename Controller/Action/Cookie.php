<?php
namespace ADM\QuickDevBar\Controller\Action;

class Cookie extends \ADM\QuickDevBar\Controller\Index
{
    const COOKIE_DURATION = 8640000; // lifetime in seconds

    private \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager;

    private \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory;
    private \Magento\Framework\Session\Config $sessionConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \ADM\QuickDevBar\Helper\Data $qdbHelper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\Config $sessionConfig
    ) {
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory);

        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionConfig = $sessionConfig;
    }

    public function execute()
    {
        $cookieName = $this->getRequest()->getParam('qdbName');
        $output = 'No cookie name';
        try {
            if ($cookieName) {
                $cookieValue = $this->getRequest()->getParam('qdbValue');
                if(is_null($cookieValue)) {
                    if($this->getRequest()->getParam('qdbToggle')) {
                        $cookieValue = $this->cookieManager->getCookie($cookieName) ? null : true;
                    } else {
                        throw new \Exception('No value to set');
                    }
                }


                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
                $metadata->setPath($this->sessionConfig->getCookiePath());
                $metadata->setDomain($this->sessionConfig->getCookieDomain());
                $metadata->setDuration($this->sessionConfig->getCookieLifetime());
                $metadata->setSecure($this->sessionConfig->getCookieSecure());
                $metadata->setHttpOnly($this->sessionConfig->getCookieHttpOnly());
                $metadata->setSameSite($this->sessionConfig->getCookieSameSite());

                $this->cookieManager->setPublicCookie(
                    $cookieName,
                    $cookieValue,
                    $metadata
                );

                $output = $cookieName.':'.$cookieValue;
            }
        } catch (\Exception $e) {
            $output = $e->getMessage();
        }


        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents($output);

    }
}
