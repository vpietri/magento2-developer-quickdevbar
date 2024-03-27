<?php

namespace ADM\QuickDevBar\Profiler;

use ADM\QuickDevBar\Helper\Debug;

class Db extends \Zend_Db_Profiler
{
    protected static $_filePath;

    protected $queryBacktrace = [];

    /**
     * {@inheritdoc }
     */
    public function queryStart($queryText, $queryType = null)
    {
        $keyQuery = parent::queryStart($queryText, $queryType);
        if($keyQuery) {
            $this->queryBacktrace[$keyQuery] = Debug::trace([], 5);
        }
        return $keyQuery;
    }

    public function getQueryBt($keyQuery)
    {
        return $this->queryBacktrace[$keyQuery] ?? [];
    }
}
