<?php
namespace ADM\QuickDevBar\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_scopeConfig;

    protected $_httpHeader;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Framework\HTTP\Header $httpHeader
    ) {
        $this->_scopeConfig = $scopeConfig;

        $this->_httpHeader = $httpHeader;

        parent::__construct($context);
    }


    public function isToolbarAccessAllowed()
    {

        $allow = false;
        $allowedIps = $this->_scopeConfig->getValue('dev/restrict/allow_ips');
        $toolbarHeader = $this->_scopeConfig->getValue('dev/restrict/toolbar_header');

        $clientIp = $this->_getRequest()->getClientIp();


        if( !empty($allowedIps) ) {
            if (!empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) !== false) {
                    $allow = true;
                }
            }
        } elseif ($clientIp && $clientIp=='127.0.0.1') {
            $allow = true;
        }

        if(!empty($toolbarHeader)) {
        		if(!preg_match('/' . $toolbarHeader . '/', $this->_httpHeader->getHttpUserAgent(true))) {
        		    $allow = false;
        		}
        }

        return $allow;
    }


    public function getLogFiles()
    {
        return array('system'=>'system.log', 'exception'=>'exception.log', 'debug'=>'debug.log');
    }


    /**
     *
     * Cut an paste from Hackathon_MageMonitoring_Helper_Data::tailFile
     * @see https://github.com/magento-hackathon/Hackathon_MageMonitoring/blob/master/app/code/community/Hackathon/MageMonitoring/Helper/Data.php
     *
     * tail -n in php, kindly lifted from https://gist.github.com/lorenzos/1711e81a9162320fde20
     *
     * @param string $filepath
     * @param int $lines
     * @param bool $adaptive use adaptive buffersize for seeking, if false use static buffersize of 4096
     *
     * @return string
     */
    function tailFile($filepath, $lines = 1, $adaptive = true) {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($f);
        return trim($output);
    }

}