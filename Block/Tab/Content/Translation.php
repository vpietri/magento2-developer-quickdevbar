<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

use ADM\QuickDevBar\Helper\Translate;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class Translation extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * @var Translate
     */
    private $translate;

    /**
     * @param Template\Context $context
     * @param Translate $translate
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Translate $translate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->translate = $translate;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $type = $this->getTitle();
        if(!empty($this->_data['type'])) {
            $type = $this->_data['type'];
        }

        return strtolower($type);
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        if(empty($this->_template)) {
            if(in_array($this->getType(), ['module','theme'])) {
                $this->_template = "tab/translation/file.phtml";
            } else {
                $this->_template = "tab/translation/plain.phtml";
            }
        }

        return $this->_template;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTranslations()
    {
        return $this->translate->getTranslationsByType($this->getType());
    }
}