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

class Simple_Forum_Model_Advanced_Topics extends Mage_Core_Model_Abstract
{
    private $_options = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    
	public function toOptionArray()
    {
		$ret = array();
		if(is_array($this->_options))
		{
			foreach($this->_options as $val)
			{
				$ret[] = array(
					'value'=>(string)$val,
				    'label'=>(string)$val	
				);
			}
		}

		return $ret;
	}

}

