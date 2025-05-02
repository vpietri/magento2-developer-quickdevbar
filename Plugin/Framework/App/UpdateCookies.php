<?php

namespace ADM\QuickDevBar\Plugin\Framework\App;

use ADM\QuickDevBar\Helper\Cookie;
use Magento\Framework\App\PageCache\FormKey as CacheFormKey;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Escaper;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class UpdateCookies
{
    private CookieManagerInterface $cookieManager;

    private CookieMetadataFactory $cookieMetadataFactory;

    /**
     * @param CacheFormKey $cacheFormKey
     * @param Escaper $escaper
     * @param FormKey $formKey
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ConfigInterface $sessionConfig
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {

        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Set form key from the cookie.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(): void
    {

        $cookieValue = $this->cookieManager->getCookie(Cookie::COOKIE_NAME_PROFILER_ENABLED);
        if ($cookieValue) {
            //TODO: Update cookie lifetime

//            $metadata = $this->cookieMetadataFactory
//                ->createPublicCookieMetadata()
//                ->setDuration(Cookie::COOKIE_DURATION);
//
//            $this->cookieManager->setPublicCookie(
//                DbAdapter::COOKIE_NAME_PROFILER_ENABLED,
//                $cookieValue,
//                $metadata
//            );
        }
    }

}
