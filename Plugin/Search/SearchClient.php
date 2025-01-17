<?php

namespace ADM\QuickDevBar\Plugin\Search;

class SearchClient
{
    public function beforeQuery(\Magento\OpenSearch\Model\SearchClient $subject, array $query)
    {
        dump($query);

        return [$query];
    }
}
