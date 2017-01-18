<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class Sql extends \ADM\QuickDevBar\Block\Tab\Panel
{

    protected $_sql_profiler;

    protected $_all_queries = [];

    protected $_all_queries_stats = false;

    protected $_longestQueryTime = 0;

    protected $_shortestQueryTime = 100000;

    protected $_longestQuery;

    protected $_resource;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                      \Magento\Framework\App\ResourceConnection $resource
                                    , array $data = [])
    {

        $this->_resource = $resource;

        parent::__construct($context, $data);
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {

        $this->_initSqlProfilerData();

        return parent::_toHtml();
    }

    public function getTitleBadge()
    {
        if ($this->getSqlProfiler()) {
            return $this->getSqlProfiler()->getTotalNumQueries();
        }
        return false;
    }

    public function getSqlProfiler()
    {
        if (is_null($this->_sql_profiler)) {
            $this->_initSqlProfilerData();
        }
        return $this->_sql_profiler;
    }

    public function _initSqlProfilerData()
    {
        if (is_null($this->_sql_profiler)) {
            $this->_sql_profiler = new \Zend_Db_Profiler();
            if(!is_null($this->_resource)) {
                $this->_sql_profiler = $this->_resource->getConnection('read')->getProfiler();
                if ($this->_sql_profiler->getQueryProfiles() && is_array($this->_sql_profiler->getQueryProfiles())) {
                    foreach ($this->_sql_profiler->getQueryProfiles() as $query) {
                        if ($query->getElapsedSecs() > $this->_longestQueryTime) {
                            $this->_longestQueryTime = $query->getElapsedSecs();
                            $this->_longestQuery = $query->getQuery();
                        }
                        if ($query->getElapsedSecs() < $this->_shortestQueryTime) {
                            $this->_shortestQueryTime = $query->getElapsedSecs();
                        }

                        $this->_all_queries[] = ['sql' => $query->getQuery(), 'time' => $query->getElapsedSecs(), 'grade' => 'medium'];
                    }
                }
            }
        }
    }

    public function getTotalNumQueries($queryType = null)
    {
        return $this->_sql_profiler->getTotalNumQueries($queryType);
    }

    public function getTotalElapsedSecs()
    {
        return $this->_sql_profiler->getTotalElapsedSecs();
    }

    public function getAverage() {

        return ($this->getTotalNumQueries() &&  $this->_sql_profiler->getTotalElapsedSecs()) ?  $this->_sql_profiler->getTotalElapsedSecs()/$this->getTotalNumQueries() : 0;
    }

    public function getNumQueriesPerSecond() {

        return ($this->getTotalNumQueries() && $this->_sql_profiler->getTotalElapsedSecs() ?  round($this->getTotalNumQueries()/$this->_sql_profiler->getTotalElapsedSecs()) : 0);
    }

    public function getAllQueries()
    {
        if (!$this->_all_queries_stats) {

            $average = $this->getAverage();
            $squareSum = 0;
            foreach ($this->_all_queries as $index=>$query) {
                $squareSum = pow($query['time'] - $average, 2);
            }

            $standardDeviation = 0;
            if ($squareSum and $this->getTotalNumQueries()) {
                $standardDeviation = sqrt($squareSum/$this->getTotalNumQueries());
            }

            foreach ($this->_all_queries as $index=>$query) {
                if($query['time']<($this->_shortestQueryTime+2*$standardDeviation)) {
                    $this->_all_queries[$index]['grade'] = 'good';
                } elseif($query['time']>($this->_longestQueryTime-2*$standardDeviation)) {
                    $this->_all_queries[$index]['grade'] = 'bad';
                }
            }

            $this->_all_queries_stats = true;
        }
        return $this->_all_queries;
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

    public function getLongestQueryTime()
    {
        return $this->_longestQueryTime;
    }

    public function getLongestQuery()
    {
        return $this->_longestQuery;
    }

    public function formatSqlTime($time)
    {
        $decimals = 2;
        $formatedTime = number_format(round(1000*$time,$decimals),$decimals);

        return $formatedTime . 'ms';
    }

}
