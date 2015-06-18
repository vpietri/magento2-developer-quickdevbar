<?php
namespace ADM\QuickDevBar\Controller\Index;

class Config extends ADM\QuickDevBar\Controller\Index
{
    const SHOP_SCOPE = 'stores';

    public function execute()
    {

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            $frontendHintsEnable = false;
            if (isset($postData['frontendHints']) && $postData['frontendHints']) {
                $frontendHintsEnable = true;
            }

            Mage::getConfig()->saveConfig('dev/debug/template_hints', $frontendHintsEnable, self::SHOP_SCOPE, Mage::app()->getStore()->getStoreId());
            Mage::getConfig()->saveConfig('dev/debug/template_hints_blocks', $frontendHintsEnable, self::SHOP_SCOPE, Mage::app()->getStore()->getStoreId());

            $translateInlineEnabled = false;
            if (isset($postData['translateInline']) && $postData['translateInline']) {
                $translateInlineEnabled = true;
            }

            Mage::getConfig()->saveConfig('dev/translate_inline/active', $translateInlineEnabled, self::SHOP_SCOPE, Mage::app()->getStore()->getStoreId());

            if (isset($postData['clearCache']) && $postData['clearCache']) {
                self::clearCache();
            }

            $this->_redirectReferer();
        }
    }
}