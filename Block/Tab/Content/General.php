<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class General extends \ADM\QuickDevBar\Block\Tab\DefaultContent
{
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    public function getStore()
    {
        return $this->_storeManager->getWebsite();
    }
}