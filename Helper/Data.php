<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var string
     */
    protected $_controllerMsg = '';

    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    /**
     *
     * @var array
     */
    protected $_defaultLogFiles = ['exception'=>'exception.log', 'system'=>'system.log', 'debug'=>'debug.log'];

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
            \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_moduleList = $moduleList;

        parent::__construct($context);
    }


    public function getCacheFrontendPool()
    {
        return $this->_cacheFrontendPool;
    }


    public function getConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    public function defaultAppearance()
    {
        return $this->getConfig('dev/quickdevbar/appearance');
    }

    public function isToolbarAccessAllowed()
    {
        $allow = false;
        $enable = $this->getConfig('dev/quickdevbar/enable');

        if ($enable) {

            if($enable>1) {
                $allow = $this->isIpAuthorized();

                if(!$allow ) {
                		$allow = $this->isUserAgentAuthorized();
                }
            } else {
                $allow = true;
            }
        }

        return $allow;
    }

    public function isToolbarAreaAllowed($area)
    {
        $areaEnabled = $this->getConfig('dev/quickdevbar/area');

        return ($areaEnabled == \Magento\Framework\App\Area::AREA_GLOBAL)
                || ($area == $areaEnabled);

   }


    public function isIpAuthorized()
    {
        if (array_search($this->getClientIp(), $this->getAllowedIps()) !== false) {
            $allow = true;
        } else {
            $allow = false;
        }

        return $allow;
    }

    public function getAllowedIps($separator=false)
    {
        $allowedIps = $this->getConfig('dev/quickdevbar/allow_ips');
        if($allowedIps) {
            $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
        } else {
            $allowedIps = array();
        }
        $allowedIps = array_merge(array("127.0.0.1", "::1"), $allowedIps);

        return $separator ? implode($separator ,$allowedIps) : $allowedIps;
    }

    public function getClientIp()
    {
        /*FIX FOR PROXY USERS RETURNING TWO IP ADDRESSES e.g. 127.0.0.1 127.0.0.1*/
//        return $this->_getRequest()->getClientIp();
        return $this->_remoteAddress->getRemoteAddress();
    }

    public function isUserAgentAuthorized()
    {
        $toolbarHeader = $this->getConfig('dev/quickdevbar/toolbar_header');

        return !empty($toolbarHeader) ? preg_match('/' . preg_quote($toolbarHeader, '/') . '/', $this->getUserAgent()) : false;
    }

    public function getUserAgent()
    {
        return $this->_httpHeader->getHttpUserAgent(true);
    }

    public function displayMagentoFile($path)
    {
        return str_replace(BP, '', $path);
    }


    public function getLogFiles($key=false)
    {
        $logFiles = [];
        foreach ($this->_defaultLogFiles as $fileKey=>$fileName) {
            $filepath = BP . '/var/log/' . $fileName;
            $logFiles[$fileKey] = ['id'=>$fileName
                    , 'name' => $fileName
                    , 'path' => $filepath
                    , 'reset' => $this->_canResetFile($filepath)
                    , 'size' => $this->_getFileSize($filepath)
                    ];
        }

        if (!$key) {
            return $logFiles;
        } elseif (!empty($logFiles[$key]))  {
            return $logFiles[$key];
        } else {
            return false;
        }
    }

    protected  function _canResetFile($filepath)
    {
        if (is_file($filepath) and is_writable($filepath)) {
            return true;
        } else {
            return false;
        }
    }

    protected  function _getFileSize($filepath)
    {

        if (is_file($filepath) and file_exists($filepath)) {
            return filesize($filepath);
        } else {
            return 0;
        }
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

    public function setControllerMessage($msg)
    {
        $this->_controllerMsg = $msg;
    }

    public function getControllerMessage()
    {
        return $this->_controllerMsg;
    }

    public function getModuleVersion($moduleName)
    {
        $moduleInfo = $this->_moduleList->getOne($moduleName);
        return !empty($moduleInfo['setup_version']) ? $moduleInfo['setup_version'] : '???';
    }

}
