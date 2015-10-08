<?php

class TM_AskIt_Model_Mysql4_Vote extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('askit/vote', 'id');
    }

    /**
     *
     * @param int $itemId
     * @param int $customerId
     * @return bool
     */
    public function isVoted($itemId, $customerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('item_id = ?', $itemId)
            ->where('customer_id = ?', $customerId);

        $row = $this->_getReadAdapter()->fetchAll($select);
        return count($row) == 0 ? false : true;
    }
}