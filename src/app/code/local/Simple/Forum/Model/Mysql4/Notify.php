<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

class Simple_Forum_Model_Mysql4_Notify extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    { 
        $this->_init('forum/notify', 'notify_id');
    }
}

?>