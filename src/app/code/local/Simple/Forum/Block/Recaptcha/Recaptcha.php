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

class Simple_Forum_Block_Recaptcha_Recaptcha extends Mage_Core_Block_Template
{	
	
	private $avaiable_languges = array
	(
		'en',
		'nl',
		'fr',
		'de',
		'pt',
		'ru',
		'es',
		'tr'
	);

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

	public function getKeyPublic()
	{
		return Mage::getStoreConfig('forum/recaptchasetting/recaptchakey');
	}

	public function displayRecaptcha()
	{
		require_once('recaptchalib.php');
		$publickey = $this->getKeyPublic();
		echo recaptcha_get_html($publickey, null, false, '100%');
	}
	
	public function getLanguage()
	{
		$current_lang = Mage::app()->getLocale()->getDefaultLocale();
		$arr 		  = explode('_', $current_lang);
		if(!empty($arr[0])) 
		{
			if(in_array($arr[0], $this->avaiable_languges))
			{
				return $arr[0];
			}	
			else
			{
				return $this->avaiable_languges[0];
			}
		}
	}
}