<?php
namespace ADM\QuickDevBar\Block\Adminhtml\System\Config\Form\Fieldset;

use Magento\Framework\Data\Form\Element\AbstractElement;

class IsEnabled extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \ADM\QuickDevBar\Helper\Data
     */
    protected $_qdbHelper;

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \ADM\QuickDevBar\Helper\Data $qdbHelper,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_qdbHelper = $qdbHelper;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = [];
        if ($this->_qdbHelper->isToolbarAccessAllowed()) {
            $html[] = __('Yep');
        } else {
            $html[] = '<strong>' . __('Nope') .'</strong>';
            if(!$this->_qdbHelper->isIpAuthorized()) {
                $html[] =  __('Your Ip "<i class="note">%1</i>" is not allowed, you should register it in the field below.', $this->_qdbHelper->getClientIp());
            }
            if(!$this->_qdbHelper->isUserAgentAuthorized()) {
                $html[] =  __('Your User Agent "<i class="note">%1</i>" is not allowed, you should add a user-agent pattern',  $this->_qdbHelper->getUserAgent());
            }
        }


        return implode('<br/>', $html);
    }

}
