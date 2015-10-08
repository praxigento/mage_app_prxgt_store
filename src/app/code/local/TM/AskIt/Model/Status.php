<?php

class TM_AskIt_Model_Status extends Varien_Object
{
    const STATUS_PENDING	= 1;
    const STATUS_APROVED	= 2;
    const STATUS_DISAPROVED	= 3;
    const STATUS_CLOSE	        = 4;

    static public function getQuestionOptionArray()
    {
        return array(
            self::STATUS_PENDING     => Mage::helper('askit')->__('Pending'),
            self::STATUS_APROVED     => Mage::helper('askit')->__('Approved'),
            self::STATUS_DISAPROVED  => Mage::helper('askit')->__('Disapproved'),
            self::STATUS_CLOSE       => Mage::helper('askit')->__('Close')
        );
    }

    static public function getAnswerOptionArray()
    {
        return array(
            self::STATUS_PENDING     => Mage::helper('askit')->__('Pending'),
            self::STATUS_APROVED     => Mage::helper('askit')->__('Approved'),
            self::STATUS_DISAPROVED  => Mage::helper('askit')->__('Disapproved')
        );
    }
}