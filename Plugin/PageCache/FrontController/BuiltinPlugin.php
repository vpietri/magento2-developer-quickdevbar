<?php


namespace ADM\QuickDevBar\Plugin\PageCache\FrontController;


use Magento\PageCache\Model\Cache\Type as PageCache;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\ResponseInterface;

class BuiltinPlugin
{
    /**
     * @var \ADM\QuickDevBar\Service\App\Cache
     */
    private $cacheService;

    public function __construct(\ADM\QuickDevBar\Service\App\Cache $cacheService)
    {
        $this->cacheService = $cacheService;
    }


    /**
     * @param PageCache $subject
     * @param string $identifier
     */
    public function beforeLoad(PageCache $subject, string $identifier)
    {
        $this->cacheService->addCache('load', $identifier);
    }


    /**
     * @param PageCache $subject
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param $lifeTime
     * @return mixed
     */
    public function beforeSave(
        PageCache $subject,
        string $data,
        string $identifier,
        array $tags = [],
                       $lifeTime = null
    ) {

        $this->cacheService->addCache('save', $identifier);
    }


    /**
     * @param PageCache $subject
     * @param string $identifier
     * @return void
     */
    public function beforeRemove(PageCache $subject, string $identifier)
    {
        $this->cacheService->addCache('remove', $identifier);
    }
}
