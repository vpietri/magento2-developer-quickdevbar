<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Sql extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{

    protected $_sql_profiler;

    protected $_longestQueryTime = 0;

    protected $_longestQuery;

    protected $_resource;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                      \Magento\Framework\App\ResourceConnection $resource
                                    , array $data = [])
    {

        $this->_resource = $resource;
        $this->setTemplate('ADM_QuickDevBar::tab/sql.phtml');

        parent::__construct($context, $data);
    }

    public function getTitleBadge()
    {
        if ($this->getSqlProfiler()) {
            return $this->getSqlProfiler()->getTotalNumQueries();
        } else {
            return false;
        }
    }

    public function getSqlProfiler()
    {
        if (is_null($this->_sql_profiler)) {
            if(!is_null($this->_resource)) {
                $this->_sql_profiler = $this->_resource->getConnection('read')->getProfiler();
            } else {
                $this->_sql_profiler = false;
            }
        }

        return $this->_sql_profiler;
    }

    public function getQueryProfiles()
    {
        $profiler = $this->getSqlProfiler();

        if ($profiler) {
            return $profiler->getQueryProfiles();
        } else {
            return [];
        }
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


    protected function _getLongestQuery()
    {
        foreach ($this->getQueryProfiles() as $query) {
            if ($query->getElapsedSecs() > $this->_longestQueryTime) {
                $this->_longestQueryTime  = $query->getElapsedSecs();
                $this->_longestQuery = $query->getQuery();
            }
        }
    }

    public function getLongestQueryTime()
    {
        if (!$this->_longestQueryTime) {
            $this->_getLongestQuery();
        }
        return $this->_longestQueryTime;
    }

    public function getLongestQuery()
    {
        if (!$this->_longestQuery) {
            $this->_getLongestQuery();
        }
        return $this->_longestQuery;
    }

    public function formatSqlTime($time)
    {
        $decimals = 2;
        $formatedTime = number_format(round(1000*$time,$decimals),$decimals);

        return $formatedTime . 'ms';
    }


}