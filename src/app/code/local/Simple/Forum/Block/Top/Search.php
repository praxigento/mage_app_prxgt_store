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

class Simple_Forum_Block_Top_Search extends Mage_Core_Block_Template
{
	
	CONST SEARCH_VAR   = 'q_f';
	CONST RESET_VAR    = 'r';
	CONST REDIRECT_URL = 'r_url';
	
	protected function _prepareLayout()
	{
	    parent::_prepareLayout();
	}
	
	public function getSearchInputName()
	{
		return self::SEARCH_VAR;
	}
	
	public function isTopic()
	{
		if(Mage::registry('current_object') )
		{
			if(Mage::registry('current_object')->getIs_category() == 0)
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function getRetUrl()
	{
		if(Mage::registry('current_object') )
		{
			return 	Mage::registry('current_object')->getUrl_text();
		}
		else
		{
			return false;
		}
	}
	
	public function getSubmitButtonTitle()
	{
		if($this->isTopic()) return $this->__('Search Topic');
		else				 return $this->__('Search Forum');
	}
	
	public function getActionSubmitUrl()
	{
		if( Mage::registry('current_object') )
		{
			if( Mage::registry('current_object')->getIs_category() == 0 )
			{
				return $this->getBaseUrl() . Mage::registry('current_object')->getUrl_text();
			}	
		}
		return $this->getBaseUrl() . 'forum/search';
	}
	
	public function getSearchValue()
	{
		$sess = Mage::getSingleton('forum/session');
		if($sess->getSearchValue())
		{
			return $sess->getSearchValue();
		}
		else
		{
			return $this->getDefaultValue();
		}
	}
	
	public function haveSearchValue()
	{
		$sess = Mage::getSingleton('forum/session');
		if(!$sess->getSearchValue() )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function getDefaultValue()
	{
		return $this->__('SEARCH');
	}
	
	public function getResetSearchUrl()
	{
		if(!$ret_url = $this->getRetUrl())
		{
			return $this->getBaseUrl() . 'forum/search?' . self::RESET_VAR . '=true';
		}
		else
		{
			return $this->getBaseUrl() . 'forum/search?' . self::RESET_VAR . '=true&' . self::REDIRECT_URL . '=' . $ret_url;
		}
	}
}
?>