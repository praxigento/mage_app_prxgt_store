<?php

class Kanavan_Searchautocomplete_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function getSuggestUrl()
  {
        return $this->_getUrl('searchautocomplete/suggest/result', array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
  }
}
