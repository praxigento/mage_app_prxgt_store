<?php
class TM_AskIt_Block_Rss_Head extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $productId = $this->getRequest()->getParam('productId');

        parent::_prepareLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
			if (Mage::getStoreConfig('askit/rss/enabled')) {
                $url = Mage::getUrl('askit/index/rss') . 'productId/' . $productId;
				$headBlock->addItem('rss', $url/* , 'title="' . $title . '"'*/);
			}
		}
    }
}