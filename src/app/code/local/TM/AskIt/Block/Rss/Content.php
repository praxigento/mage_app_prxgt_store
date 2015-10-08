<?php
class TM_AskIt_Block_Rss_Content extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_rss_askit';

    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
       /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_askit');
        $this->setCacheLifetime(600);
    }

    protected function _getProductId()
    {
        return $this->getRequest()->getParam('productId', false);
    }

    protected function _getCollection($productId = false, $questionId = false)
    {
        $collection = Mage::getModel('askit/askIt')
            ->getCollection()
            ->addStatusFilter(array(
                2/*aprowed*/,
                4/*closed*/
            ))
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addPrivateFilter()
            ->setorder('created_time','DESC')
            ;
        if ($productId) {
            $collection->addProductFilter($productId);
        }
//        if ($questionId) {
//            $collection->addParentIdFilter($questionId);
//        }

        return $collection->load();
        
    }

    protected function _toHtml()
    {
		$rssObj = Mage::getModel('rss/rss');
        $url = Mage::getUrl(Mage::helper('askit')->getRoute());
        $productId = $this->_getProductId();
        $product =  Mage::getModel('catalog/product')
                ->load($productId);
        $title = Mage::getStoreConfig('askit/rss/title') . ':' . $product->getName();

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $url,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $items = $this->_getCollection($productId)->getItems();
        $questions = array();
        foreach ($items as $item) {
            if (null !== $item->getParentId()) {
                continue;
            }
            $questions[$item->getId()] = $item;
        }
        foreach ($items as $item) {
            if (null === $item->getParentId() 
                || !($question = $questions[$item->getParentId()])) {
                
                continue;
            }

            $title = $product->getName(). ':' . $question->getText() . ' Posted by ' . $question->getCustomerName();
            $url = Mage::getUrl(Mage::helper('askit')->getRoute()) . $product->getUrlPath();
            
            $data = array(
                'title'         => $title,
                'link'          => $url,
                'description'   => $item->getText() . ' Posted by ' . $item->getCustomerName(),
                'lastUpdate' 	=> strtotime($item->getUpdateTime()),
            );

            $rssObj->_addEntry($data);
        }

        return $rssObj->createRssXml();
//
//		$collection = Mage::getModel('blog/blog')->getCollection()
//		->addStoreFilter(Mage::app()->getStore()->getId())
//		->setOrder('created_time ', 'desc');
//
//		$identifier = $this->getRequest()->getParam('identifier');
//
//		$tag = $this->getRequest()->getParam('tag');
//		if($tag){
//			$collection->addTagFilter(urldecode($tag));
//		}
//
//
//		if ($cat_id = Mage::getSingleton('blog/cat')->load($identifier)->getcatId()){
//			Mage::getSingleton('blog/status')->addCatFilterToCollection($collection, $cat_id);
//		}
//
//
//		Mage::getSingleton('blog/status')->addEnabledFilterToCollection($collection);
//
//		$collection->setPageSize((int)Mage::getStoreConfig('blog/rss/posts'));
//		$collection->setCurPage(1);
//
//
//
//		if ($collection->getSize()>0) {
//			foreach ($collection as $post) {
//
//				$data = array(
//							'title'         => $post->getTitle(),
//							'link'          => $this->getUrl($route . "/" . $post->getIdentifier()),
//							'description'   => $post->getPostContent(),
//							'lastUpdate' 	=> strtotime($post->getCreatedTime()),
//							);
//
//				$rssObj->_addEntry($data);
//			}
//		}
//
//		return $rssObj->createRssXml();
    }
}
