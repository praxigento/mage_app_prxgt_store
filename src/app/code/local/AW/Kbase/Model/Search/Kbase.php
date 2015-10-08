<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
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
 * @package    AW_Kbase
 * @version    1.3.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */

/*
 * A class implementing Knowledge Base Global search functionality in Admin section
 */
class AW_Kbase_Model_Search_Kbase extends Varien_Object
{

    /*
     * Searches articles containing query words
     */
    public function load()
    {
        $res = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($res);
            return $this;
        }

        $query=addslashes($this->getQuery());

		$collection = Mage::getModel('kbase/article')->getCollection()
            ->addSearchFilter($query);

        $helper = Mage::helper('kbase');

        foreach ($collection as $article)
            $res[] = array(
                'id'            => 'article/'.$article->getId(),
                'type'          => $helper->__('KB Article'),
                'name'          => $article->getArticleTitle(),
                'description'   => $article->getShortDescription(30),
                'form_panel_title' => $helper->__('"%s" article', $article->getId(), $article->getArticleTitle()),
                'url'           => Mage::getSingleton('adminhtml/url')->getUrl('kbase_admin/adminhtml_article/edit', array('id' => $article->getId())),
            );

        $this->setResults($res);

        return $this;
    }

}

