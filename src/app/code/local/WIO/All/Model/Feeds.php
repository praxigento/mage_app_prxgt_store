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

class WIO_All_Model_Feeds extends Mage_AdminNotification_Model_Feed
{
	public static function loadFeeds()
	{
		if(!Mage::getStoreConfig('wioall/feeds/allownotification'))
		{		
			return;
		}
		return Mage::getModel('wioall/feeds')->checkUpdate();
	}
	
    public function getPeriod()
    {
        return Mage::getStoreConfig('wioall/feeds/period') * 3600;
    }

    public function getLastUpdate()
    {		
        return Mage::app()->loadCache('wioall_last_update');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'wioall_last_update');
        return $this;
    }
    public function getRSSUrl()
    {
		
        if (is_null($this->_feedUrl)) 
		{
            $this->_feedUrl =  Mage::getStoreConfig('wioall/feeds/url');
        }
        return $this->_feedUrl;
    }

	public function checkUpdate()
	{
        if (($this->getPeriod() + $this->getLastUpdate()) > time()) 
		{
            return $this;
        }
        $feedData = array();

        $feedXml = $this->getFeedData();

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $feedData[] = array(
                    'severity'      => (int)$item->severity ? (int)$item->severity : 3,
                    'date_added'    => $this->getDate((string)$item->pubDate),
                    'title'         => (string)$item->title,
                    'description'   => (string)$item->description,
                    'url'           => (string)$item->link,
                );
            }
            if ($feedData) {
                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
            }

        }
        $this->setLastUpdate();

        return $this;
    }

	
    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig
		(
			array
			(
				'timeout'   => 1
			)
		);
        $curl->write(Zend_Http_Client::GET, $this->getRSSUrl(), '1.0');
        $data = $curl->read();
        if ($data === false) 
		{
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try 
		{
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) 
		{
            return false;
        }
        return $xml;
    }
	
    public function getDate($rssDate)
    {
        return date('Y-m-d H:i:s', strtotime($rssDate));
    }

}
