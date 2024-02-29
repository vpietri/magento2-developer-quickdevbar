<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use Magento\Framework\DataObjectFactory;

class Sql extends \ADM\QuickDevBar\Block\Tab\Panel
{
    private $objectFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Data $qdbHelper,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        DataObjectFactory $objectFactory,
        array $data = []
    ) {
        parent::__construct($context, $qdbHelper, $qdbHelperRegister, $data);
        $this->qdbHelperRegister = $qdbHelperRegister;
        $this->objectFactory = $objectFactory;
    }


    public function getTitleBadge()
    {
        if ($this->getSqlProfiler()) {
            return $this->getSqlProfiler()->getTotalNumQueries();
        }
        return false;
    }



    /**
     * @return Zend_Db_Profiler
     */
    public function getSqlProfiler()
    {
        return $this->objectFactory->create()->setData($this->qdbHelperRegister->getRegisteredData('sql_profiler'));
    }


    public function getAllQueries()
    {
        return $this->getSqlProfiler()->getAllQueries();
    }

    public function getTotalNumQueries($queryType = null)
    {
        return $this->getSqlProfiler()->getTotalNumQueries($queryType);
    }

    public function getTotalNumQueriesByType($queryType = null)
    {
        $numQueriesByType = $this->getSqlProfiler()->getTotalNumQueriesByType();
        return isset($numQueriesByType[$queryType]) ? $numQueriesByType[$queryType] : 0;
    }

    public function getTotalElapsedSecs()
    {
        return $this->getSqlProfiler()->getTotalElapsedSecs();
    }

    public function getAverage()
    {
        return $this->getSqlProfiler()->getAverage();
    }

    public function getLongestQuery()
    {
        return $this->getSqlProfiler()->getLongestQuery();
    }

    public function getLongestQueryTime()
    {
        return $this->getSqlProfiler()->getLongestQueryTime();
    }

    public function getNumQueriesPerSecond()
    {

        return $this->getSqlProfiler()->getNumQueriesPerSecond();
    }

    public function formatSql($sql)
    {
        $htmlSql = preg_replace('/\b(SET|AS|ASC|COUNT|DESC|IN|LIKE|DISTINCT|INTO|VALUES|LIMIT)\b/', '<span class="sqlword">\\1</span>', $sql);
        $htmlSql = preg_replace('/\b(UNION ALL|DESCRIBE|SHOW|connect|begin|commit)\b/', '<br/><span class="sqlother">\\1</span>', $htmlSql);
        $htmlSql = preg_replace('/\b(UPDATE|SELECT|FROM|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|DELETE|INSERT)\b/', '<br/><span class="sqlmain">\\1</span>', $htmlSql);

        return preg_replace('/^<br\/>/', '', $htmlSql);
    }

    public function formatParams($params) {
        if (is_array($params)) {
            ksort($params);

            return \json_encode($params);
        }

        return '';
    }


    public function formatSqlTime($time, $decimals = 2)
    {
        return number_format(round(1000 * $time, $decimals), $decimals) . 'ms';
    }

    public function useQdbProfiler()
    {
        return $this->getSqlProfiler()->getShowBacktrace();
    }

    public function formatSqlTrace(mixed $bt)
    {
        $traceFormated = [];
        foreach ($bt as $i=>$traceLine) {
//            $traceFormated[] = preg_replace_callback('/^(#\d+\s)(.*)(\s+\.\s+):(\d+)\s/', function ($matches) {
//                return $matches[1] . $this->helper->getIDELinkForFile($matches[2],$matches[3]).' ';
//            },$traceLine);

            //basename($traceLine['file'])
            $traceFormated[] = sprintf('#%d %s %s->%s()', $i, $this->helper->getIDELinkForFile($traceLine['file'],$traceLine['line']) , $traceLine['class'], $traceLine['function']);


        }
        return '<div class="qdbTrace">'.implode('<br/>', $traceFormated).'</div>';
    }

}
