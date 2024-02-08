<?php

namespace ADM\QuickDevBar\Profiler;

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
            $trace = debug_backtrace();
            $this->queryBacktrace[$keyQuery] = $this::trace($trace);
        }
        return $keyQuery;
    }

    public function getQueryBt($keyQuery)
    {
        return $this->queryBacktrace[$keyQuery] ?? [];
    }


    /**
     * @see \Magento\Framework\Debug::trace
     * @param array $trace
     * @param $html
     * @return array
     */
    public static function trace(array $trace, $html = false)
    {
        $returnTrace = [];
        $newIncrementStack = 0;
        foreach ($trace as $i => $data) {
            // skip from self
            //      to #5 query() called at [vendor/magento/framework/DB/Adapter/Pdo/Mysql.php:564]
            if ($i < 6) {
                continue;
            }
            $newIncrementStack++;

            // prepare method's name
            $className = '?class?';
            if (isset($data['class']) && isset($data['function'])) {
                $className = $data['class'];

                //Interceptor
                if (isset($data['object']) && get_class($data['object']) != $data['class']) {
                    //$className = get_class($data['object']) . '[' . $data['class'] . ']';
                }
            }

            $methodName = '?method?';
            if (isset($data['function'])) {
                $methodName = sprintf('%s(...)', $data['function']);
            }

            $fileName = '?file?';
            if (isset($data['file'])) {
                $pos = strpos($data['file'], self::getRootPath());
                if ($pos !== false) {
                    $data['file'] = substr($data['file'], strlen(self::getRootPath()) + 1);
                }
                $fileName = sprintf('%s:%d', $data['file'], $data['line']);
            }

            //$returnTrace[]= sprintf('#%d %s::%s called by [%s]', $newIncrementStack, $className, $methodName, $fileName);
            $returnTrace[]= sprintf('#%d %s::%s', $newIncrementStack, $className, $methodName);
        }

        return $returnTrace;
    }

    /**
     * @see \Magento\Framework\Debug::getRootPath
     *
     * @return false|string
     */
    public static function getRootPath()
    {
        if (self::$_filePath === null) {
            if (defined('BP')) {
                self::$_filePath = BP;
            } else {
                self::$_filePath = dirname(__DIR__);
            }
        }
        return self::$_filePath;
    }
}
