<?php

namespace ADM\QuickDevBar\Block\Tab\Content;

class PhpInfo extends \ADM\QuickDevBar\Block\Tab\Panel
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->showPhpInfo();
    }

    /**
     * Return PHP Info CSS
     */
    protected function phpInfoCssLambda($value) {
        return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $value );
    }

    public function showPhpInfo()
    {
        $what = $this->hasShortWhat() ? INFO_VARIABLES|INFO_ENVIRONMENT : INFO_ALL;

        ob_start();
        phpinfo($what);
        if (preg_match ('%<style type="text/css">(.*?)</style>.*?<body>(.*)</body>%s', ob_get_clean(), $matches)) {
            return "<style type='text/css'>" . PHP_EOL .
                    join( PHP_EOL,
                            array_map(
                                    array(&$this, "phpInfoCssLambda"),
                                    preg_split( '/\n/', trim($matches[1]))
                            )
                    ). PHP_EOL .
                    ".phpinfodisplay table {width: 600px;}" . PHP_EOL .
                    "</style>" . PHP_EOL .
                    "<div class='phpinfodisplay'>" . PHP_EOL .
                    $matches[2]. PHP_EOL .
                    "</div>" . PHP_EOL;
        } else {
            return '';
        }
    }

}
