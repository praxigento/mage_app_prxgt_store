<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Tamarashorin_Commercebug_Model_Ajaxresponse extends Varien_Object
{
    public function render()
    {
        header('Content-Type: application/json');
        echo Mage::getSingleton('commercebug/jsonbroker')->jsonEncode($this->getData());
    }
}
