<?php
namespace ADM\QuickDevBar\Plugin\Elasticsearch;

use Magento\AdvancedSearch\Model\Client\ClientInterface;

class Client
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
     * @param ClientInterface $subject
     * @param array $result
     * @param array $query
     * @return array
     */
    public function afterQuery(ClientInterface $subject, $result, $query)
    {
        $this->elasticsearchService->addQuery($query, $result);

        return $result;
    }

}
