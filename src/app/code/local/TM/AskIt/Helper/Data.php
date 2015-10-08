<?php

class TM_AskIt_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getLinkHtml($product)
    {
        $productId = $product->getId();
        $link = Mage::getUrl($this->getRoute()) . $product->getUrlPath();
        $countQuestion = count(Mage::getModel('askit/askIt')
            ->getCollection()
                ->addStatusFilter(array(
                    2/*aprowed*/,
                    4/*closed*/
                ))
                ->addProductFilter($productId)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                 ->addQuestionFilter()
                ->addPrivateFilter()
                
                ->load()
        );
        $text = Mage::helper('askit')->__("Be the first to ask a question about this product") ;
        if($countQuestion) {
            $text = Mage::helper('askit')->__("Ask a question (%d)", $countQuestion);
        }
        return '<a href="' . $link . '">' . $text . '</a><br />';
    }
    
    public function trim($text, $len, $delim = '...')
	{
        if (@mb_strlen($text) > $len) {
            $whitespaceposition = mb_strpos($text, " ", $len) - 1;
                if( $whitespaceposition > 0 ) {
                    $chars = count_chars(mb_substr($text, 0, ($whitespaceposition + 1)), 1);
                    $text = mb_substr($text, 0, ($whitespaceposition + 1));
                }
            return $text . $delim;
        }
        return $text;
    }

    public function getRoute()
    {
        return 'productsquestions';
    }
}