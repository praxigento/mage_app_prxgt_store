<?php

class WP_CustomMenu_Block_Toggle extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        if (!Mage::getStoreConfig('custom_menu/general/enabled')) return;
        if (Mage::getStoreConfig('custom_menu/general/ie6_ignore') && self::_isIE6()) return;
        $layout = $this->getLayout();
        $topnav = $layout->getBlock('catalog.topnav');
        if (is_object($topnav))
        {
            $topnav->setTemplate('webandpeople/custommenu/top.phtml');
            $head = $layout->getBlock('head');
            $head->addItem('skin_js', 'js/webandpeople/custommenu/custommenu.js');
            $head->addItem('skin_css', 'css/webandpeople/custommenu/custommenu.css');
        }
        else
        {
            $info = __FILE__ . ': ' . __LINE__ . "\n";
            $info.= 'Block "catalog.topnav" not found';
            Mage::log($info, null, 'wp_custommenu.log');
        }
    }

    private static function _isIE6()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/\bmsie [1-6]/i', $_SERVER['HTTP_USER_AGENT']);
    }
}
