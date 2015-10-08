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
class Simple_Forum_Block_Edit_Breadcrumbs extends Mage_Core_Block_Template
{

	private $created = false;
	public  $_crumbs = null;

    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }
    
    public function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link', 'first', 'last', 'readonly'));
        if ((!isset($this->_crumbs[$crumbName])) || (!$this->_crumbs[$crumbName]['readonly'])) {
           $this->_crumbs[$crumbName] = $crumbInfo;
        }
        return $this;
    }

    protected function _prepareLayout()
    {
    	//	$this->setTemplate('forum/util/breadcrumbs.phtml');
	    $this->addCrumb('home', array(
            'label'=>Mage::helper('forum')->__('Home'),
            'title'=>Mage::helper('forum')->__('Go to Home Page'),
            'link'=>Mage::getBaseUrl()
        ));

        $title = array();
        $path  = Mage::helper('forum/topic')->getBreadcrumbPath(Mage::getBaseUrl(), 1);

        foreach ($path as $name => $breadcrumb) {
        	if(empty($breadcrumb['link']))
        	{
				$breadcrumb['last'] = true;
			}
            $this->addCrumb($name, $breadcrumb);
            $title[] = $breadcrumb['label'];
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
        }
        
        return parent::_prepareLayout();
    }
    
    public function _getCrumbs()
	{
		return $this->_crumbs;	
	}
}