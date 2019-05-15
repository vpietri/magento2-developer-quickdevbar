<?php

namespace ADM\QuickDevBar\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\App\State;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Translate\ResourceInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\Framework\App\Language\Dictionary;

class Translate extends \Magento\Framework\Translate
{
    /**
     * Checks if we have loaded our translation data.
     *
     * @var bool
     */
    protected $_hasLoaded = false;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param DesignInterface $viewDesign
     * @param FrontendInterface $cache
     * @param ViewFileSystem $viewFileSystem
     * @param ModuleList $moduleList
     * @param Reader $modulesReader
     * @param ScopeResolverInterface $scopeResolver
     * @param ResourceInterface $translate
     * @param ResolverInterface $locale
     * @param State $appState
     * @param Filesystem $filesystem
     * @param RequestInterface $request
     * @param Csv $csvParser
     * @param Dictionary $packDictionary
     * @param DirectoryList $directoryList
     */
    public function __construct(
        DesignInterface $viewDesign,
        FrontendInterface $cache,
        ViewFileSystem $viewFileSystem,
        ModuleList $moduleList,
        Reader $modulesReader,
        ScopeResolverInterface $scopeResolver,
        ResourceInterface $translate,
        ResolverInterface $locale,
        State $appState,
        Filesystem $filesystem,
        RequestInterface $request,
        Csv $csvParser,
        Dictionary $packDictionary,
        DirectoryList $directoryList
    ) {
        parent::__construct(
            $viewDesign,
            $cache,
            $viewFileSystem,
            $moduleList,
            $modulesReader,
            $scopeResolver,
            $translate,
            $locale,
            $appState,
            $filesystem,
            $request,
            $csvParser,
            $packDictionary
        );
        $this->directoryList = $directoryList;
    }

    /**
     * Gets relative file path for absolute path.
     *
     * @param string $absolutePath
     * @return string
     */
    protected function _getRelativeFilePath($absolutePath)
    {
        return str_replace($this->directoryList->getRoot() . DIRECTORY_SEPARATOR, '', $absolutePath);
    }

    /**
     * Load current theme translation
     *
     * @return $this
     */
    protected function _loadThemeTranslation()
    {
        $file = $this->_getThemeTranslationFile($this->getLocale());
        if ($file) {
            $relativePath = $this->_getRelativeFilePath($file);
            foreach ($this->_getFileData($file) as $key => $value) {
                if ($key === $value) {
                    continue;
                }
                $this->_data['theme'][htmlspecialchars($key)] = [
                    'file' => $relativePath,
                    'translation' => htmlspecialchars($value)
                ];
            }
        }
        return $this;
    }

    /**
     * Load translation dictionary from language packages.
     *
     * @todo    It's also possible to get the filename of the language pack here, but generally only a single
     *          language pack will be installed for a given locale.
     * @return $this
     * @throws LocalizedException
     */
    protected function _loadPackTranslation()
    {
        $this->_data['pack'] = $this->packDictionary->getDictionary($this->getLocale());
        return $this;
    }

    /**
     * Loading current translation from DB
     *
     * @return $this
     */
    protected function _loadDbTranslation()
    {
        $this->_data['db'] = $this->_translateResource->getTranslationArray(null, $this->getLocale());
        return $this;
    }


    /**
     * Load data from module translation files by list of modules
     *
     * @param array $modules
     * @return $this
     */
    protected function loadModuleTranslationByModulesList(array $modules)
    {
        foreach ($modules as $module) {
            $moduleFilePath = $this->_getModuleTranslationFile($module, $this->getLocale());
            $relativePath = $this->_getRelativeFilePath($moduleFilePath);
            foreach ($this->_getFileData($moduleFilePath) as $key => $value) {
                if ($key === $value) {
                    continue;
                }
                $this->_data['module'][htmlspecialchars($key)] = [
                    'file' => $relativePath,
                    'translation' => htmlspecialchars($value)
                ];
            }
        }
        return $this;
    }

    /**
     * Gets translation data by type.
     *
     * @param string $type
     * @return array
     * @throws LocalizedException
     */
    public function getTranslationsByType($type)
    {
        if ($this->_hasLoaded === false) {
            $this->loadData(null, true);
            $this->_hasLoaded = true;
        }
        return isset($this->_data[$type]) ? $this->_data[$type] : [];
    }
}