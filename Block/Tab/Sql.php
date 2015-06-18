<?php

namespace ADM\QuickDevBar\Block\Tab;

class Sql extends DefaultTab
{

    protected $_sql_profiler;

    protected $_longestQueryTime = 0;

    protected $_longestQuery;

    protected $_resource;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                      \Magento\Framework\App\Resource $resource
                                    , array $data = [])
    {

        $this->_resource = $resource;
        $this->setTemplate('ADM_QuickDevBar::tab/sql.phtml');

        parent::__construct($context, $data);
    }


    public function getTitle()
    {
        $title = 'Queries';
        if ($this->getSqlProfiler()) {
            $title .= ' (' . $this->getSqlProfiler()->getTotalNumQueries() . ')';
        }

        return $title;
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
            return array();
        }
    }

    public function formatSql($sql)
    {
        $htmlSql = $sql;
        $htmlSql = preg_replace('/\b(SET|AS|ASC|COUNT|DESC|IN|LIKE|DISTINCT|INTO|VALUES|LIMIT|DESCRIBE)\b/', '<span class="sqlword">\\1</span>', $sql);
        $htmlSql = preg_replace('/\b(UNION ALL)\b/', '<br/><span class="sqljoin">\\1</span>', $htmlSql);
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


}