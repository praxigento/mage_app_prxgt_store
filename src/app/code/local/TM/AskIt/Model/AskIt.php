<?php
class TM_AskIt_Model_AskIt extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('askit/askIt');
    }
}