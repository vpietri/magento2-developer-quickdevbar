<?php


namespace ADM\QuickDevBar\Service;


use ADM\QuickDevBar\Api\ServiceInterface;

class Sql implements ServiceInterface
{
    /**
     * @var \Magento\Framework\DataObject
     */
    private $sqlProfilerData;



    private $_sql_profiler;

    private $_resource;

    private $_longestQueryTime;

    private $_longestQuery;

    private $_shortestQueryTime;
    /**
     * @var array
     */
    private $_all_queries;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource,
                                \Magento\Framework\DataObjectFactory $objectFactory)
    {

        $this->objectFactory = $objectFactory;
        $this->resource = $resource;
    }

    public function pullData()
    {
        if(is_null($this->sqlProfilerData)) {
            $this->sqlProfilerData = $this->initSqlProfilerData();
        }
        return $this->sqlProfilerData;
    }

    protected function initSqlProfilerData()
    {
        $sqlProfilerData = $this->objectFactory->create();

        $longestQueryTime = 0;
        $shortestQueryTime = 100000;
        $longestQuery = '';
        $allQueries = [];
        if ($this->_sql_profiler === null) {
            $this->_sql_profiler = new \Zend_Db_Profiler();
            if ($this->resource !== null) {
                $this->_sql_profiler = $this->resource->getConnection('read')->getProfiler();
                if ($this->_sql_profiler->getQueryProfiles() && is_array($this->_sql_profiler->getQueryProfiles())) {
                    foreach ($this->_sql_profiler->getQueryProfiles() as $query) {
                        if ($query->getElapsedSecs() > $longestQueryTime) {
                            $longestQueryTime = $query->getElapsedSecs();
                            $longestQuery = $query->getQuery();
                        }
                        if ($query->getElapsedSecs() < $shortestQueryTime) {
                            $shortestQueryTime = $query->getElapsedSecs();
                        }

                        $allQueries[] = ['sql' => $query->getQuery(),
                            'params' => $query->getQueryParams(),
                            'time' => $query->getElapsedSecs(),
                            'grade' => 'medium'
                            //TODO: Add backtrace
                        ];
                    }
                }
            }
        }

        if(!$this->_sql_profiler->getTotalNumQueries()) {
            return [];
        }

        $numQueriesByType = [];
        foreach( [$this->_sql_profiler::INSERT,
                     $this->_sql_profiler::UPDATE,
                     $this->_sql_profiler::DELETE,
                     $this->_sql_profiler::SELECT,
                     $this->_sql_profiler::QUERY] as $type) {
            $numQueriesByType[$type] = $this->_sql_profiler->getTotalNumQueries($type);

        }

        $totalNumQueries = $this->_sql_profiler->getTotalNumQueries();
        $totalElapsedSecs = $this->_sql_profiler->getTotalElapsedSecs();
        $average = $totalElapsedSecs/$totalNumQueries;

        return [
                'all_queries' => $this->computeQueryGrade($allQueries, $shortestQueryTime, $longestQueryTime, $totalNumQueries, $average),
                'longest_query_time' => $longestQueryTime,
                'shortest_query_time' => $shortestQueryTime,
                'longest_query' => $longestQuery,
                'total_elapsed_secs' => $totalElapsedSecs,
                'total_num_queries' => $totalNumQueries,
                'num_queries_per_second' => floor($totalNumQueries/$totalElapsedSecs),
                'average' => $average,
                'total_num_queries_by_type' => $numQueriesByType,
                ];
    }

    protected function computeQueryGrade($allQueries, $shortestQueryTime, $longestQueryTime, $totalNumQueries, $average)
    {
        $squareSum = 0;
        foreach ($allQueries as $index => $query) {
            $squareSum = pow($query['time'] - $average, 2);
        }

        $standardDeviation = 0;
        if ($squareSum and $totalNumQueries) {
            $standardDeviation = sqrt($squareSum/$totalNumQueries);
        }

        foreach ($allQueries as $index => $query) {
            if ($query['time']<($shortestQueryTime+2*$standardDeviation)) {
                $allQueries[$index]['grade'] = 'good';
            } elseif ($query['time']>($longestQueryTime-2*$standardDeviation)) {
                $allQueries[$index]['grade'] = 'bad';
            }
        }

        return $allQueries;
    }
}