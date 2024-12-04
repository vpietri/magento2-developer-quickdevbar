<?php
namespace ADM\QuickDevBar\Plugin\Elasticsearch;


class ResponseFactory
{
    /**
     * @var ADM\QuickDevBar\Service\Elasticsearch
     */
    private $elasticsearchService;

    public function __construct(\ADM\QuickDevBar\Service\Elasticsearch $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * @param \Magento\Elasticsearch\SearchAdapter\ResponseFactory $subject
     * @param $result
     * @param $response
     * @return mixed
     */
    public function afterCreate(\Magento\Elasticsearch\SearchAdapter\ResponseFactory $subject, $result, $response)
    {

        //dd($result);

        return $result;
    }

}
