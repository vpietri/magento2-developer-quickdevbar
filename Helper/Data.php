<?php
namespace ADM\QuickDevBar\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{


    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $cacheFrontendPool;

    /**
     * @var string
     */
    protected $controllerMsg = '';

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     *
     * @var array
     */
    protected $defaultLogFiles = ['exception'=>'exception.log', 'system'=>'system.log', 'debug'=>'debug.log'];
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;
    /**
     * @var State
     */
    private $appState;
    /**
     * @var Session
     */
    private $session;
    private array $ideList;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Filesystem $filesystem,
        array $ideList,
        State $appState,
        Session $session
    ) {
        parent::__construct($context);
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->moduleList = $moduleList;


        $this->filesystem = $filesystem;
        $this->appState = $appState;
        $this->session = $session;
        $this->ideList = $ideList;
    }


    public function getIdeList()
    {
        return $this->ideList;
    }

    public function getIdeRegex()
    {
        if($ide = $this->getQdbConfig('ide')) {
            if (strtolower($ide) == 'custom' && $ideCustom = $this->getQdbConfig('ide_custom')) {
                return $ideCustom;
            }

            return $this->getIdeList()[$ide] ?? '';
        }
        return '';
    }

    public function getCacheFrontendPool()
    {
        return $this->cacheFrontendPool;
    }

    public function getQdbConfig($key, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        // Backward compatibility
        if($key=='handle_vardumper' && !class_exists(\Symfony\Component\VarDumper\VarDumper::class)) {
            return false;
        }

        return $this->getConfig('dev/quickdevbar/'.$key, $scopeType, $scopeCode);
    }

    public function getConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    public function defaultAppearance()
    {
        return $this->getQdbConfig('appearance');
    }

    public function isToolbarAccessAllowed($testWithRestriction=false)
    {
        $allow = false;
        $enable = $this->getQdbConfig('enable');

        if ($enable || $testWithRestriction) {

            if ($enable>1 || $testWithRestriction) {
                $allow = $this->isIpAuthorized();

                if(!$allow) {
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
        $areaEnabled = $this->getQdbConfig('area');

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

    public function getAllowedIps($separator = false)
    {
        $allowedIps = $this->getQdbConfig('allow_ips');
        if($allowedIps) {
            $allowedIps = preg_split('#\s*,\s*#', $allowedIps, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $allowedIps = [];
        }
        $allowedIps = array_merge(["127.0.0.1", "::1"], $allowedIps);

        return $separator ? implode($separator, $allowedIps) : $allowedIps;
    }

    public function getClientIp()
    {
        /*FIX FOR PROXY USERS RETURNING TWO IP ADDRESSES e.g. 127.0.0.1 127.0.0.1*/
//        return $this->_getRequest()->getClientIp();
        return $this->_remoteAddress->getRemoteAddress();
    }

    public function isUserAgentAuthorized()
    {
        $toolbarHeader = $this->getQdbConfig('toolbar_header');

        return !empty($toolbarHeader) ? preg_match('/' . preg_quote($toolbarHeader, '/') . '/', $this->getUserAgent()) : false;
    }

    public function getUserAgent()
    {
        return $this->_httpHeader->getHttpUserAgent(true);
    }


    public function getLogFiles($key = false)
    {
        $logFiles = [];
        foreach ($this->defaultLogFiles as $fileKey => $fileName) {
            $filepath = BP . '/var/log/' . $fileName;
            $logFiles[$fileKey] = ['id'=>$fileName
                    , 'name' => $fileName
                    , 'path' => $filepath
                    , 'reset' => $this->canResetFile($filepath)
                    , 'size' => $this->getFileSize($filepath)
                    ];
        }

        if (!$key) {
            return $logFiles;
        } elseif (!empty($logFiles[$key])) {
            return $logFiles[$key];
        } else {
            return false;
        }
    }

    /**
     * @param $filepath
     * @return bool
     */
    protected function canResetFile($filepath)
    {
        if (is_file($filepath) and is_writable($filepath)) {
            return true;
        }
        return false;
    }

    /**
     * @param $filepath
     * @return false|int
     */
    protected function getFileSize($filepath)
    {
        if (is_file($filepath) and file_exists($filepath)) {
            return filesize($filepath);
        }
        return 0;
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
    function tailFile($filepath, $lines = 1, $adaptive = true)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }

        // Sets buffer size
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

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
        $this->controllerMsg = $msg;
    }

    public function getControllerMessage()
    {
        return $this->controllerMsg;
    }

    public function getModuleVersion($moduleName)
    {
        $moduleInfo = $this->moduleList->getOne($moduleName);
        return !empty($moduleInfo['setup_version']) ? $moduleInfo['setup_version'] : '???';
    }

    protected function getWrapperBaseFilename($ajax = false)
    {
        $sessionId = $this->session->getSessionId();
        return  'qdb_register_' . (!$ajax ? 'std' : 'xhr') . '_' . $sessionId;
    }


    protected function getQdbTempDir()
    {
        $varDirWrite = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $varDirWrite->create('qdb');

        return $varDirWrite->getAbsolutePath() . 'qdb/';
    }

    public function getWrapperContent($ajax = false)
    {
//Clean old files
//        /** @var \SplFileInfo $fileInfo */
//        foreach (new \DirectoryIterator($this->getQdbTempDir()) as $fileInfo) {
//            if($fileInfo->isFile() && time() - $fileInfo->getMTime() > 20) {
//                //TODO: unlink only files starting with 'qdb_register_' . $sessionId
//                unlink($fileInfo->getPathname());
//            }
//        }

        $wrapperFiles = [];
        $filename = $this->getWrapperBaseFilename($ajax);
        foreach (new \DirectoryIterator($this->getQdbTempDir()) as $fileInfo) {
            if($fileInfo->isFile() && strpos($fileInfo->getFilename(), $filename)===0) {
                $wrapperFiles[] = $fileInfo->getPathname();
            }
        }

        if(empty($wrapperFiles)) {
            throw new LocalizedException(__('No files for wrapper'));
        }

        $serializer = new \Magento\Framework\Serialize\Serializer\Json();

        $content = [];
        foreach ($wrapperFiles as $wrapperContent) {
            $jsonContent = file_get_contents($wrapperContent);
            if($jsonContent) {
                foreach ($serializer->unserialize($jsonContent) as $contentKey => $contentValue) {
                    $content[$contentKey] =  empty($content[$contentKey]) ? $contentValue : array_merge($content[$contentKey], $contentValue);
                }
            }
            //TODO: remove foreach
            break;
        }

        if(empty($content)) {
            throw new LocalizedException(__('No data registered'));
        }

        /** @var \SplFileInfo $fileInfo */
        foreach (new \DirectoryIterator($this->getQdbTempDir()) as $fileInfo) {
            if($fileInfo->isFile()) {
                //TODO: unlink only files starting with 'qdb_register_' . $sessionId
                unlink($fileInfo->getPathname());
            }
        }

        return $content;
    }


    public function setWrapperContent($content, $ajax = false)
    {
        $filename = $this->getWrapperBaseFilename($ajax);
        if($ajax) {
            $filename .= time();
        }
        file_put_contents($this->getQdbTempDir() . $filename . '.json', $content);
    }

    /**
     * TODO: To removed
     * Asymmetric behavior frontend/admin is no more necessary
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isAjaxLoading()
    {
        if($this->appState->getAreaCode() != 'frontend') {
            return false;
        }
        //TODO: save Register Data to use Ajax
        // see: \ADM\QuickDevBar\Helper\Register::__construct
        return true;
    }

    /**
     * @param $file
     * @param $line
     * @return string
     */
    public function getIDELinkForFile($file, $line=1, $btFormat = '%2$s(%3$d)')
    {
        $relativeFile = $file;
        if(strpos($relativeFile, BP)===0) {
            $relativeFile = preg_replace('#' . BP . DIRECTORY_SEPARATOR . '?#', '', $file);
        }

        if($btLinkFormat = $this->getIdeRegex()) {
            return sprintf('<span data-ide-file="'.$btLinkFormat.'">'.$btFormat.'</span>', BP, $relativeFile, $line);
        }

        return sprintf($btFormat, BP, $relativeFile, $line);

    }

    /**
     * @param $class
     * @return string
     */
    public function getIDELinkForClass($class)
    {
        //return $class;
        try {
            $reflector = new \ReflectionClass($class);
            if($file=$reflector->getFileName()) {
                return $this->getIDELinkForFile($file, 1, $class);
            }

        } catch (\ReflectionException $e) {

        }
        return $class;
    }

}
