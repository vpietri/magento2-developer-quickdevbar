<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use ADM\QuickDevBar\Helper\Cookie;
use ADM\QuickDevBar\Plugin\Zend\DbAdapter;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Sql extends \ADM\QuickDevBar\Block\Tab\Panel
{
    private $objectFactory;
    private Cookie $cookieHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ADM\QuickDevBar\Helper\Data $qdbHelper,
        \ADM\QuickDevBar\Helper\Register $qdbHelperRegister,
        DataObjectFactory $objectFactory,
        Cookie $cookieHelper,
        array $data = []
    ) {
        parent::__construct($context, $qdbHelper, $qdbHelperRegister, $data);
        $this->qdbHelperRegister = $qdbHelperRegister;
        $this->objectFactory = $objectFactory;
        $this->cookieHelper = $cookieHelper;
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

    public function formatSqlTrace($bt)
    {
        $traceFormated = [];
        foreach ($bt as $i=>$traceLine) {
            $traceFormated[] = sprintf('#%d %s %s->%s()', $i, $this->helper->getIDELinkForFile($traceLine['file'],$traceLine['line']) , $traceLine['class'], $traceLine['function']);
        }
        return '<div class="qdbTrace">'.implode('<br/>', $traceFormated).'</div>';
    }

    public function getProfilerEnabled()
    {
        return $this->cookieHelper->isProfilerEnabled();
    }

    public function getProfilerBacktraceEnabled()
    {
        return $this->cookieHelper->isProfilerBacktraceEnabled();
    }


    public function getButtonProfilerLabel()
    {
        return $this->getProfilerEnabled() ? 'Disable profiler session' : 'Enable profiler session';
    }

    public function getButtonProfilerBactraceLabel()
    {
        return $this->getProfilerBacktraceEnabled() ? 'Disable backtrace' : 'Enable backtrace';

    }

}
