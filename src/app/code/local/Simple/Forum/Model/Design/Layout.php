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

class Simple_Forum_Model_Design_Layout extends Mage_Core_Model_Abstract
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $layouts = array();
			$node = Mage::getConfig()->getNode('global/cms/layouts') ? Mage::getConfig()->getNode('global/cms/layouts') : Mage::getConfig()->getNode('global/page/layouts');
			
			foreach ($node->children() as $layoutConfig) {
				$this->_options[] = array(
				   'value'=>(string)$layoutConfig->template,
				   'label'=>(string)$layoutConfig->label
				);
			}
			
		}
        return $this->_options;
    }
}

