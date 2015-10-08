<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    3.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Ajaxcartpro_Model_Response extends Varien_Object
{
    protected $_error = array();
    protected $_msg = array();

    /**
     * @param string|array $msg
     * @return void
     */
    public function addError($msg)
    {
        if (is_array($msg)) {
            foreach($msg as $item) {
                $this->addError($item);
            }
        } else if($msg instanceof Mage_Core_Model_Message_Abstract) {
            $this->_error[] = $msg->getText();
        } else {
            $this->_error[] = $msg;
        }
    }

    public function addMsg($msg)
    {
        if (is_array($msg)) {
            foreach($msg as $item) {
                $this->addMsg($item);
            }
        } else if($msg instanceof Mage_Core_Model_Message_Abstract) {
            $this->_msg[] = $msg->getText();
        } else {
            $this->_msg[] = $msg;
        }
    }

    public function isError()
    {
        return !empty($this->_error);
    }

    public function toJson(array $arrAttributes = array())
    {
        $this->setSuccess(true);
        if ($this->isError()) {
            $this->setSuccess(false);
            $this->setMsg($this->_error);
        } else {
            $this->setMsg($this->_msg);
        }
        return parent::toJson($arrAttributes);
    }
}
