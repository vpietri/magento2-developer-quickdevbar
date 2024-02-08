<?php

namespace ADM\QuickDevBar\Service;

use ADM\QuickDevBar\Api\ServiceInterface;

class Sql implements ServiceInterface
{
    /**
     * @var \Magento\Framework\DataObject
     */
    private $sqlProfilerData;

    private $sqlProfiler;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;
    private $useQdbProfiler = false;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
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
        $longestQueryTime = 0;
        $shortestQueryTime = 100000;
        $longestQuery = '';
        $allQueries = [];
        if ($this->sqlProfiler === null) {
            $this->sqlProfiler = new \Zend_Db_Profiler();
            if ($this->resource !== null) {
                $this->sqlProfiler = $this->resource->getConnection('read')->getProfiler();

                $this->useQdbProfiler = method_exists($this->sqlProfiler, 'getQueryBt');


                if ($this->sqlProfiler->getQueryProfiles() && is_array($this->sqlProfiler->getQueryProfiles())) {
                    foreach ($this->sqlProfiler->getQueryProfiles() as $key => $query) {
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
                            'grade' => 'medium',
                            'bt' => $this->useQdbProfiler ? $this->sqlProfiler->getQueryBt($key) : null
                        ];
                    }
                }
            }
        }

        if(!$this->sqlProfiler->getTotalNumQueries()) {
            return [];
        }

        $numQueriesByType = [];
        foreach( [$this->sqlProfiler::INSERT,
                     $this->sqlProfiler::UPDATE,
                     $this->sqlProfiler::DELETE,
                     $this->sqlProfiler::SELECT,
                     $this->sqlProfiler::QUERY] as $type) {
            $numQueriesByType[$type] = $this->sqlProfiler->getTotalNumQueries($type);

        }

        $totalNumQueries = $this->sqlProfiler->getTotalNumQueries();
        $totalElapsedSecs = $this->sqlProfiler->getTotalElapsedSecs();
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
                'show_backtrace' => $this->useQdbProfiler
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
