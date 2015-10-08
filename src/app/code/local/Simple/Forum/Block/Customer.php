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

class Simple_Forum_Block_Customer extends Mage_Core_Block_Template
{

	private $customer    = false;
	private $customer_id = false;

	private $customer_statuses = array
	(
    	'Administrator',
    	'Moderator',
    	'User'
	);

 	const ADM_USER_ID = 10000000;

 	const CONST_ADMIN     = 0;
    const CONST_MODERATOR = 1;
    const CONST_USER      = 2;

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $root = $this->getLayout()->getBlock('root');
		$root->setTemplate(Mage::helper('forum/data')->getLayout());
    }

	public function getCustomer()
	{    	if(!$this->customer_id)
    	{
    		$this->_setCustomerId();            $this->customer = new Varien_Object;
            $this->customer->setCId($this->customer_id);
            $this->customer->setUserSettingsData($this->_getCustomerData());
            $this->customer->setSystemUserData($this->_getCustomerSystemData());
            $this->customer->setTotalTopics($this->_getCustomerTotalTopics());
            $this->customer->setTotalPosts($this->_getCustomerTotalPosts());
            $this->customer->setStatus($this->_getCustomerStatus());
    	}
    	return $this->customer;
	}

	public function getAvatarUrl($obj)
	{
		$avatar = Mage::getStoreConfig('forum/advanced_settings/avatar_path');
		if($obj->getAvatar_name() && file_exists(Mage::getBaseDir() . '/' .  Mage::getStoreConfig('forum/advanced_settings/avatar_path') . '/' . $obj->getAvatar_name()))
        {
        	$img = $obj->getAvatar_name();
        	if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
        	{
                 return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . $obj->getAvatar_name();
        	}
        }
        else
        {
            if(Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') && Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') != '')
        	{
                 return Mage::getStoreConfig('forum/advanced_settings/avatar_url_path') . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
        	}
            $img = '/' . Mage::getStoreConfig('forum/advanced_settings/avatar_noimage');
        }
        return $this->getUrl($avatar) . $img;
	}

	public function getAvatarWidth()
	{
       	return Mage::getStoreConfig('forum/advanced_settings/avatar_width');
	}

	public function getCustomerName()
	{
		if($this->customer->getUserSettingsData()->getNickname() && $this->customer->getUserSettingsData()->getNickname() != '')
    	{        	return $this->customer->getUserSettingsData()->getNickname();
    	}
    	elseif($this->customer_id != self::ADM_USER_ID)
    	{
    		if($this->customer->getSystemUserData()->getFirstname() && $this->customer->getSystemUserData()->getLastname())
    		{
				return $this->customer->getSystemUserData()->getFirstname() . ' ' . $this->customer->getSystemUserData()->getLastname();
            }
    	}
    	else
    	{
    		if($this->customer_id == self::ADM_USER_ID)
    		{            	return $this->__('Forum Admin');
    		}        	return $this->__('Name Not Set');
    	}
	}

	public function getCustomerJoinedDate()
	{     	$user = Mage::getModel('forum/user')->load($this->customer_id, 'system_user_id');
     	return $user->getFirst_post();
	}

	public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function getPMUrl($customer)
    {    	if(is_object($customer))
    	{        	$id = $customer->getId();
    	}
    	else
    	{            $id = $customer;
    	}
        return Mage::helper('forum/customer')->getCustomerPMUrl($id);
    }

    public function getIsAdmin()
    {    	if(self::ADM_USER_ID == $this->customer_id)
    	{        	return true;
    	}
    }

    public function getIsMe()
    {    	if( $this->customer_id == Mage::registry('current_customer')->getId())
    	{            return true;
    	}
    }

	private function _setCustomerId()
	{    	$this->customer_id = Mage::registry('customer_id');
	}

 	private function _getCustomerData()
 	{
    	return Mage::registry('customer_forum');
 	}

 	private function _getCustomerSystemData()
 	{
 		return Mage::registry('customer');
 	}

 	private function _getCustomerTotalTopics()
 	{     	$collection = Mage::getModel('forum/topic')->getCollection();
     	if($this->customer_id == self::ADM_USER_ID)
     	{            $collection->getSelect()->where('status=1 AND (system_user_id = ' . self::ADM_USER_ID . ' OR system_user_id = ' . $this->customer_id . ')');
     	}
     	else
     	{
     		$collection->getSelect()->where('status=?', 1)->where('system_user_id=?', $this->customer_id);
     	}
     	return $collection->getSize();
 	}

 	private function _getCustomerTotalPosts()
 	{     	$collection = Mage::getModel('forum/post')->getCollection();
     	if($this->customer_id == self::ADM_USER_ID)
     	{
            $collection->getSelect()->where('status=1 AND (system_user_id = ' . self::ADM_USER_ID . ' OR system_user_id = ' . $this->customer_id . ')');
     	}
     	else
     	{
     		$collection->getSelect()->where('status=?', 1)->where('system_user_id=?', $this->customer_id);
     	}
     	return $collection->getSize();
 	}

 	private function _getCustomerStatus()
 	{    	if($this->customer_id == self::ADM_USER_ID)
    	{			return $this->__($this->customer_statuses[self::CONST_ADMIN]);
    	}
    	if(Mage::helper('forum/topic')->getAllowedModerateWebsites($this->customer_id))
		{
			return $this->__($this->customer_statuses[self::CONST_MODERATOR]);
		}

		return $this->__($this->customer_statuses[self::CONST_USER]);
 	}
}