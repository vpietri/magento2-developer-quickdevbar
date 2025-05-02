<?php

namespace ADM\QuickDevBar\Profiler;

use ADM\QuickDevBar\Helper\Cookie;
use ADM\QuickDevBar\Helper\Debug;

use Magento\Framework\App\ObjectManager;

class Db extends \Zend_Db_Profiler
{
    protected static $_filePath;

    protected $queryBacktrace = [];

    private Cookie $cookieHelper;


    public function getBacktraceQuery()
    {
        if(empty($this->cookieHelper)) {
            //Mea culpa, mea maxima culpa
            $objectManager = ObjectManager::getInstance();
            $this->cookieHelper = $objectManager->create(\ADM\QuickDevBar\Helper\Cookie::class);
        }

        return $this->cookieHelper->isProfilerBacktraceEnabled();
    }


    /**
     * {@inheritdoc }
     */
    public function queryStart($queryText, $queryType = null)
    {
        $keyQuery = parent::queryStart($queryText, $queryType);
        if($keyQuery && $this->getBacktraceQuery()) {
            $this->queryBacktrace[$keyQuery] = Debug::trace([], 5);
        }
        return $keyQuery;
    }

    public function getQueryBt($keyQuery)
    {
        return $this->queryBacktrace[$keyQuery] ?? [];
    }
}
