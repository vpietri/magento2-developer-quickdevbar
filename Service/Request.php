<?php


namespace ADM\QuickDevBar\Service;


use ADM\QuickDevBar\Api\ServiceInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;

class Request implements ServiceInterface
{
    const LABEL_CACHE_HIDDEN = '[hidden by cache]';

    /**
     * @var Http
     */
    private $requestHttp;
    /**
     * @var State
     */
    private $appState;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var FrontNameResolver
     */
    private $frontNameResolver;
    /**
     * @var Session
     */
    private $session;

    public function __construct(Http $requestHttp,
                                State $appState,
                                ProductMetadataInterface $productMetadata,
                                FrontNameResolver $frontNameResolver,
                                Session $session
    )
    {
        $this->requestHttp = $requestHttp;
        $this->appState = $appState;
        $this->productMetadata = $productMetadata;
        $this->frontNameResolver = $frontNameResolver;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function pullData()
    {
        $request = $this->requestHttp;
        $requestData = [];
        $requestData[] = ['name' => 'Date sys', 'value' => date("Y-m-d H:i:s")];
        $requestData[] = ['name' => 'Base Url', 'value' => $request->getDistroBaseUrl(), 'is_url' => true];
        $requestData[] = ['name' => 'Path Info', 'value' => $request->getPathInfo()];
        $requestData[] = ['name' => 'Module Name', 'value' => $request->getControllerModule() ?: self::LABEL_CACHE_HIDDEN];
        $requestData[] = ['name' => 'Controller', 'value' => $request->getControllerName() ?: self::LABEL_CACHE_HIDDEN];
        $requestData[] = ['name' => 'Action', 'value' => $request->getActionName() ?: self::LABEL_CACHE_HIDDEN];
        $requestData[] = ['name' => 'Full Action', 'value' => $request->getFullActionName() ?: self::LABEL_CACHE_HIDDEN];
        $requestData[] = ['name' => 'Route', 'value' => $request->getRouteName() ?: self::LABEL_CACHE_HIDDEN];
        $requestData[] = ['name' => 'Area', 'value' => $this->appState->getAreaCode()];

        if ($this->session->isLoggedIn()) {
            $requestData[] = ['name' => 'Logged user', 'value' => $this->session->getCustomer()->getEmail()];
            $requestData[] = ['name' => 'Group id', 'value' => $this->session->getCustomer()->getGroupId()];
        }
        $requestData[] = ['name' => 'Session Id', 'value' => $this->session->getSessionId()];

        if ($request->getBeforeForwardInfo()) {
            $requestData[] = ['name' => 'Before Forward', 'value' => $request->getBeforeForwardInfo()];
        }

        if ($request->getParams()) {
            $requestData[] = ['name' => 'Params', 'value' => $request->getParams()];
        }
        $requestData[] = ['name' => 'Client IP', 'value' => $request->getClientIp()];
        $requestData[] = ['name' => 'Magento', 'value' => $this->productMetadata->getVersion()];
        $requestData[] = ['name' => 'Mage Mode', 'value' => $this->appState->getMode()];

        $requestData[] = ['name' => 'Backend', 'value' => $request->getDistroBaseUrl() . $this->frontNameResolver->getFrontName(), 'is_url' => true];

        return $requestData;
    }
}
