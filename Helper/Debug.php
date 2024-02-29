<?php

namespace ADM\QuickDevBar\Helper;

class Debug extends \Magento\Framework\App\Helper\AbstractHelper
{
    private static $_filePath;


    /**
     * @return string
     */
    public static function traceString($separator=PHP_EOL)
    {
        $traceFormated=[];
        foreach (self::trace() as $i=>$traceLine) {
            $traceFormated[] = sprintf('#%d %s(%d) %s->%s()', $i, $traceLine['file'], $traceLine['line'], $traceLine['class'], $traceLine['function']);

        }
        return implode($separator, $traceFormated);

    }

    public static function traceHtml()
    {
        return self::traceString('<br/>');
    }

    /**
     * @param array $trace
     * @return array
     */
    public static function trace(array $trace=[], $skipLine = null)
    {
        if(empty($trace)) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            is_null($skipLine) && $skipLine=1;
        }

        $returnTrace = [];
        foreach ($trace as $i => $data) {
            if($i<$skipLine) {
                continue;
            }

            $className = '[class]';
            if (isset($data['class']) && isset($data['function'])) {
                $className = $data['class'];

                if (isset($data['object']) && get_class($data['object']) != $data['class']) {
                    $className = get_class($data['object']);
                }
            }
            if(preg_match('/Interceptor$/', $className)) {
                $className = '[interceptor]';
            }

            $methodName = $data['function'] ?? '[function]';
            $fileName = $data['file'] ?? '[file]';
            $line = $data['line'] ?? '[line]';

            $returnTrace[]= ['file'=>$fileName, 'line'=> $line, 'class'=>$className, 'function'=>$methodName];
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
