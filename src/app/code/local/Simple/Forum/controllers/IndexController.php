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

class Simple_Forum_IndexController extends Mage_Core_Controller_Front_Action
{
	
	CONST TITLE_VAR = 't';
	
	public function indexAction()
	{
		$title = $this->getRequest()->getParam(self::TITLE_VAR);
		if($title && $title != '')
		{
			$topic_id = Mage::helper('forum/topic')->getIdTopicByTitle($title, true);
			if($topic_id)
			{
				$this->_forward('view','topic', null, array('id'=>$topic_id));
				return;
			}
		}
        $this->_forward('index','topic');
	}
}

?>