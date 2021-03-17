<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Sql extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * @var \ADM\QuickDevBar\Helper\Register
     */
    private $qdbHelperRegister;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->qdbHelperRegister = $qdbHelperRegister;
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
        return $this->qdbHelperRegister->getSqlData(true);
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
        $htmlSql = $sql;
        $htmlSql = preg_replace('/\b(SET|AS|ASC|COUNT|DESC|IN|LIKE|DISTINCT|INTO|VALUES|LIMIT)\b/', '<span class="sqlword">\\1</span>', $sql);
        $htmlSql = preg_replace('/\b(UNION ALL|DESCRIBE|SHOW|connect|begin|commit)\b/', '<br/><span class="sqlother">\\1</span>', $htmlSql);
        $htmlSql = preg_replace('/\b(UPDATE|SELECT|FROM|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|DELETE|INSERT)\b/', '<br/><span class="sqlmain">\\1</span>', $htmlSql);
        $htmlSql = preg_replace('/^<br\/>/', '', $htmlSql);
        return $htmlSql;
    }


    public function formatSqlTime($time)
    {
        $decimals = 2;
        $formatedTime = number_format(round(1000*$time, $decimals), $decimals);

        return $formatedTime . 'ms';
    }
}
