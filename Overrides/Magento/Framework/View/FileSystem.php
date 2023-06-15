<?php

namespace ADM\QuickDevBar\Overrides\Magento\Framework\View;

class FileSystem extends \Magento\Framework\View\FileSystem
{
    public static function normalizePath($path, $area = null)
    {
        if (empty($path)) return '';

        return parent::normalizePath($path, $area);
    }
}
