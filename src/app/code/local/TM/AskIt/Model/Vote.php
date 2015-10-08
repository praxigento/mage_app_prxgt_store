<?php
class TM_AskIt_Model_Vote extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('askit/vote');
    }

    public function isVoted($itemId, $customerId)
    {
        return $this->_getResource()->isVoted($itemId, $customerId);
    }
}