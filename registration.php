<?php
//By now I have no other solution for a comoposer module :-(
try {
    \Magento\Framework\Component\ComponentRegistrar::register(
        \Magento\Framework\Component\ComponentRegistrar::MODULE,
        'ADM_QuickDevBar',
        __DIR__
    );
} catch (Exception $e) {

}