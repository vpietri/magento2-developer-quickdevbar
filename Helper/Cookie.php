<?php

namespace ADM\QuickDevBar\Helper;

use Magento\Framework\Stdlib\CookieManagerInterface;

class Cookie
{
    const COOKIE_NAME_PROFILER_ENABLED='qdb_db_profiler';

    const COOKIE_NAME_PROFILER_BACKTRACE_ENABLED='qdb_db_profiler_backtrace';

    private CookieManagerInterface $cookieManager;

    public function __construct(
        CookieManagerInterface $cookieManager
    ) {
        $this->cookieManager = $cookieManager;
    }

    public function isProfilerEnabled()
    {
        return (bool)$this->cookieManager->getCookie(self::COOKIE_NAME_PROFILER_ENABLED);
    }

    public function isProfilerBacktraceEnabled()
    {
        return (bool)$this->cookieManager->getCookie(self::COOKIE_NAME_PROFILER_BACKTRACE_ENABLED);
    }


}
