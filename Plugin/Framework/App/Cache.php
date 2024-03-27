<?php
namespace ADM\QuickDevBar\Plugin\Framework\App;

use Magento\Framework\App\CacheInterface;

class Cache
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
     * @param CacheInterface $subject
     * @param string $identifier
     */
    public function beforeLoad(CacheInterface $subject, string $identifier)
    {
        $this->cacheService->addCache('load', $identifier);
    }


    /**
     * @param CacheInterface $subject
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param $lifeTime
     * @return mixed
     */
    public function beforeSave(
        CacheInterface $subject,
        string $data,
        string $identifier,
        array $tags = [],
        $lifeTime = null
    ) {

        $this->cacheService->addCache('save', $identifier);
    }


    /**
     * @param CacheInterface $subject
     * @param string $identifier
     * @return void
     */
    public function beforeRemove(CacheInterface $subject, string $identifier)
    {
        $this->cacheService->addCache('remove', $identifier);
    }
}
