<?php

namespace ADM\QuickDevBar\Observer;

use ADM\QuickDevBar\Service\Dumper;
use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class CheckHtmlObserver implements ObserverInterface
{
    private Dumper $dumper;

    private PolicyCollectorInterface $collector;

    public function __construct(Dumper $dumper,
                                PolicyCollectorInterface $collector)
    {
        $this->dumper = $dumper;
        $this->collector = $collector;
    }

    public function execute(Observer $observer)
    {

        $block = $observer->getEvent()->getBlock();
        $html = $observer->getEvent()->getTransport()->getHtml();
        $allowedHashes = [];
        $policies = $this->collector->collect();
        foreach ($policies as $policy) {
            if($policy->getId() =='script-src') {
                if($policy->isInlineAllowed()) {
                    //Noting to check
                    return null;
                }
                $allowedHashes = $policy->getHashes();
                break;
            }
        }

        //Without nonce
        $pattern = '/<script(?![^>]*\bnonce\s*=)[^>]*>(.*?)<\/script>/is';

        //Without nonce nor x-magento-init
        $pattern = '/<script(?![^>]*\b(?:nonce|type\s*=\s*["\']text\/x-magento-init["\']))[^>]*>(.*?)<\/script>/is';


        if( preg_match_all($pattern, $html, $matches)) {
            foreach ($matches[1] as $scriptContent) {
                $sha256 = $this->generateHashValue($scriptContent);
                if(!empty($allowedHashes[$sha256])) {
                    continue;
                }
                $this->dumper->addDump(
                    'Script violating CSP'. '<br>' .
                    '<pre>' .  htmlspecialchars($scriptContent). '</pre>' .
                    '(' . get_class($block) . ' :: '. $block->getTemplateFile() . ')<br>' .
                    'To enable execution use sha256: '. $this->generateHashValue($scriptContent) . '<br><br>',
                    [], "");
            }
        }
    }

    private function generateHashValue(string $content): string
    {
        return base64_encode(hash('sha256', $content, true));
    }
}
