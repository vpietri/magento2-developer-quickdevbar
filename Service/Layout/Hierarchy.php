<?php


namespace ADM\QuickDevBar\Service\Layout;


use ADM\QuickDevBar\Api\ServiceInterface;

class Hierarchy implements ServiceInterface
{
    private $treeBlocksHierarchy;
    /**
     * @inheritDoc
     */
    public function pullData()
    {
        return $this->treeBlocksHierarchy;
    }

    public function addLayoutHierarchy(array $getTreeBlocksHierarchy)
    {
        $this->treeBlocksHierarchy = $getTreeBlocksHierarchy;
    }
}