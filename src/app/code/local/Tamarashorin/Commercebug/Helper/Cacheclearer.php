<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/

class Tamarashorin_Commercebug_Helper_Cacheclearer
{
    public function clearCache()
    {			
        Mage::app()->cleanCache();
    }
}