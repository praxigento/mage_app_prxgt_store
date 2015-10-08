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

class Simple_Forum_Model_Sitemap extends Mage_Core_Model_Abstract
{
	private $limit         = 5;

	private $collection    = false;
	private $current_store = false;

	/**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('forum/topic');
    }

	public function ___initialize()
	{
		$this->___generateXml();
	}

    public function ___generateXml()
    {
    	if(!$this->getAllowedSitemap() || !$this->checkUpdate())
    	{
			return;
		}
		try
		{
			$this->initCollection();
	        $io = new Varien_Io_File();
	        $io->setAllowCreateFolders(true);
	        $io->open(array('path' => $this->getPathToSiteMap()));

	        $io->streamOpen($this->getFileSName());
	        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
	        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
	        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');

	        /**
	         * Generate forum pages sitemap
	         */
	        $changefreq = (string)Mage::getStoreConfig('sitemap/forum/changefreq');
	        $priority   = (string)Mage::getStoreConfig('sitemap/forum/priority');

			if(!is_array($this->collection))
	        {
				return;
			}

			foreach ($this->collection as $item)
			{
			   $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
	                htmlspecialchars($item->getData('url')),
	                $date,
	                $changefreq,
	                $priority
	            );
	            $io->streamWrite($xml);
	        }

	        unset($this->collection);

	        $io->streamWrite('</urlset>');
	        $io->streamClose();

	        $this->___setSitemapTime();
	        //$this->save();
	    }
	    catch (Exception $e)
		{
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        return $this;
    }

    private function checkUpdate()
    {
    	if (intval($this->___getSitemapTime() +  $this->getPeriod()) > time())
		{
            return false;
        }
        return true;
	}

    private function ___setSitemapTime()
    {
    	if(!Mage::getStoreConfig('forum/sitemap/update'))
    	{
			$c =   Mage::getModel('core/config_data');
			$c
				->setScope('default')
				->setPath('forum/sitemap/update')
				->setValue(time())
				->save();
		}
		else
		{
			Mage::getModel('core/config')->saveConfig('forum/sitemap/update', time() );
		}
	}

	private function ___getSitemapTime()
	{
		return Mage::getStoreConfig('forum/sitemap/update');
	}

	private function getPeriod()
	{
		return Mage::getStoreConfig('forum/sitemap/periodcreation') + 3600;
	}

    private function getFileSName()
    {
		return Mage::getStoreConfig('forum/sitemap/sitemapfilename');
	}

	private function getPathToSiteMap()
	{
		return Mage::getStoreConfig('forum/sitemap/path_to_sitemapfilename');
	}

	private function getAllowedSitemap()
	{
		return Mage::getStoreConfig('forum/sitemap/allowcreation');
	}

	private function getAvaiableStores()
	{
		return Mage::app()->getStores();
	}

    private function initCollection()
    {
    	$stores = $this->getAvaiableStores();
    	if($stores && is_array($stores))
    	{
    		foreach($stores as $store)
    		{
    			$this->current_store = $store->getId();
				$forums     = $this->getObjectsByParentId(0, $this->current_store);
				if($forums)
				{
					foreach($forums as $val)
					{
						$topics  = $this->getObjectsByParentId($val->getId());
						$this->setTopics($topics);
					}
				}
			}
		}
	}

	private function getObjectsByParentId($_id, $store_id = false)
	{
		$c = Mage::getModel('forum/topic')->getCollection();
		$c->getSelect()->where('parent_id=?', $_id)->where('status=1');
		if($store_id)
		{
			$c->addStoreFilterToCollection($store_id);
		}
		if($c->getSize())
		{
			return $c;
		}
		else
		{
			return false;
		}
	}

	private function setTopics($collection)
	{
		if($collection)
		{
			$store     = Mage::app()->getStore($this->current_store);
			$url_begin = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			$code      = $store->getCode();
			foreach($collection as $val)
			{
				if($val->getHave_subtopics() == 1)
				{                	$col = $this->getObjectsByParentId($val->getId(), $this->current_store);
                	if($col)
                	{                    	$this->setTopics($col);
                	}
				}
				else
				{
					$all = $this->getPagesQuantity($val->getId());
					if($all)
					{
						while($all)
						{
							$array = $this->collection;
							$array[$val->getId() . '_' . $all . '_' . $this->current_store] = new Varien_Object;
							$data = array('url' => Mage::helper('forum/sitemap')->__getUrl($url_begin, $code, $val, $this->limit, $all), 'store_id' => $this->current_store);
							$array[$val->getId() . '_' . $all . '_' . $this->current_store]->setData($data);
							$this->collection = $array;
							$all--;
						}
					}
				}
			}
		}
	}

	private function getPagesQuantity($topic_id)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->getSelect()->where('parent_id=?', $topic_id)->where('status=1');
		if($c->getSize())
		{
			$size  = $c->getSize();
			$total = ceil($size/$this->limit);
			return $total;
		}
	}
}
