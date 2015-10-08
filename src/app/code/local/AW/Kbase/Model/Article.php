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
 * Article model
 */
class AW_Kbase_Model_Article extends Mage_Core_Model_Abstract
{
    /*
     * @var int Number of characters used by default to trim article_text to get short description
     */
    protected static $_shortDescriptionLimit = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('kbase/article');
        $this->setArticleRating(1);
    }

    protected function _beforeDelete()
    {
        AW_Kbase_Helper_Data::removeDirectory(AW_Kbase_Helper_Data::attachmentDirName($this->getId()));
        return parent::_beforeDelete();
    }

    protected function _afterLoad()
    {
        if(is_null($categoryIds = $this->getCategoryIds()))
            $this->setCategoryIds($this->getResource()->getCategoryIds($this->getId()));
        elseif(!is_array($categoryIds))
            $this->setCategoryIds(array_unique(explode(',', $categoryIds)));

        if(!is_null($categoryNamesIds = $this->getCategoryNamesIds()))
            $this->setCategoryNamesIds($this->_parseCategoryNamesIds($categoryNamesIds));

        // processing tags
        if(is_null($tags = $this->getArticleTags()))
            $tags = $this->getResource()->getTags($this->getId());
        elseif(!is_array($tags))
            $tags = explode(',', $tags);

        if(isset($tags))
        {
            foreach($tags as $key => $value)
                $tags[$key] = urldecode($value);

            $this->setArticleTags($tags);
        }

        return parent::_afterLoad();
    }

    /*
     * Pulic wrapper of the protected _afterLoad
     * Used when loading a collection of articles
     */
    public function afterLoad()
    {
        $this->_afterLoad();
    }

    protected function _afterSave()
    {
        $this->getResource()->saveCategoryIds($this->getId(), $this->getCategoryIds());
        $this->getResource()->saveTags($this->getId(), $this->getArticleTags());
        return parent::_afterSave();
    }

    /*
     * Unserializes the string from the SQL query
     * @param str category names and IDs serialized
     * @return array category_id => category_name
     */
    protected function _parseCategoryNamesIds($categoryNames)
    {
        $pairs = explode('##', $categoryNames);
        $cn = array();
        foreach($pairs as $pair)
        {
            list($cId, $cName) = explode('=', $pair, 2);
            $cn[$cId] = $cName;
        }
        return $cn;
    }

    /*
     * Adds information about the categories the article belongs to
     */
    public function addCategoryInfo()
    {
        $this->setCategories(
                $this->getResource()->getCategories($this->getId())
            );

        return $this;
    }

    /*
     * Adds information about the article author by loadind the admin/user model
     */
    public function addAuthorInfo()
    {
        $this->setAuthor(
                Mage::getModel('admin/user')
                    ->load($this->getArticleAuthorId())
            );

        return $this;
    }

    /*
     * Adds information about the IDs of the stores to which the article belongs through its categories
     */
    public function getStoreIds()
    {
        return $this->getResource()->getArticleStoreIds($this->getId());
    }

    /*
     * Checks whether there is an article with the same URL key among the stores the article belongs to
     * @return bool
     */
    public function isUrlKeyUsed()
    {
        $storeIds = $this->getResource()->getCategorySetStoreIds($this->getCategoryIds());
        $sameUrlArticleStoreIds = $this->getResource()->getSameUrlArticleStoreIds($this->getId(), $this->getArticleUrlKey());
        $res = array_intersect($storeIds, $sameUrlArticleStoreIds);
        return !empty($res);
    }

    /*
     * Reads to internal variable the number of characters used to get article short description
     * @return int The number or characters used to get article short description
     */
    public static function getShortDescriptionLimit()
    {
        if(is_null(self::$_shortDescriptionLimit))
            self::$_shortDescriptionLimit = Mage::getStoreConfig('kbase/general/listing_full_text_char_limit');

        return self::$_shortDescriptionLimit;
    }

    /*
     * Strips tags from article text and returns the article short description
     * @param empty|int $limit The number or characters used to get article short description
     * @return string Article short description
     */
    public function getShortDescription($limit = null) {
         
        if(is_null($limit)) { $limit = self::getShortDescriptionLimit(); }
        
        $sd = strip_tags(htmlspecialchars_decode($this->getArticleText()));
         
        /* If mbstring lib is not available return not truncated string */
        if(!in_array('mbstring',get_loaded_extensions())) {  
             return $sd;
       }
        
         /* Get endoding fallback to utf-8 */
        if(!$encoding = mb_detect_encoding($sd)) {            
            $encoding = 'UTF-8';
        }  
         
        if(!$limit ||  $limit >= mb_strlen($sd, $encoding))   { return $sd; }
        
        $str = trim(mb_substr($sd, 0, $limit, $encoding)) . "...";
        
        
        return htmlspecialchars($str);
       
    }

    /*
     * Loads itself using the URL key parameter
     * @param string $urlKey URL key used to identify the article
     */
    public function loadByUrlKey($urlKey)
    {
        $id = $this->getResource()->getIdByUrlKey($urlKey);
        if(!$id) $id = $this->getResource()->getIdByUrlKey($urlKey, true);
        return $this->load($id);
    }

    /*
     * Updated article rating according to value passed
     * @param int $rating Article rating voted, [1..5]
     */
    public function vote($rating)
    {
        $this->getResource()->vote($this->getId(), $rating);
        return $this;
    }

}
