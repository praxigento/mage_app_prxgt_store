<?php

class Simple_Forum_Block_Adminhtml_Moderators_Index extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = 'moderator_id';
        $this->_blockGroup = 'moderator';
        $this->_controller = 'adminhtml_moderators';
    }
}
