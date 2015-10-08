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


class AW_Kbase_Model_Mysql4_Article extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('kbase/article', 'article_id');
    }

    /*
     * Returns article category IDs
     * @param int $articleId The ID of the article
     * @return array Article category IDs
     */
    public function getCategoryIds($articleId)
    {
        if (!$articleId) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getTable('kbase/category_article'), 'category_id')
            ->where('article_id=?', $articleId);

        return $db->fetchCol($select);
    }

    /*
     * Saves article-to-category relation
     * @param int $articleId Article ID
     * @param array $categoryIds Category IDs
     */
    public function saveCategoryIds($articleId, $categoryIds)
    {
        if (!is_array($categoryIds))
            $categoryIds = explode(',', $categoryIds);

        $existing = $this->getCategoryIds($articleId);
        $common = array_intersect($existing, $categoryIds);
        $deleted = array_diff($existing, $common);
        $new = array_diff($categoryIds, $common);

        $db = $this->_getWriteAdapter();

        if (!empty($deleted))
            $db->delete($this->getTable('kbase/category_article'),
                'article_id=' . $articleId . ' AND category_id IN (' . implode(',', $deleted) . ')');

        if (!empty($new)) {
            $data = array();
            foreach ($new as $categoryId)
                $data[] = array($articleId, $categoryId);

            AW_Kbase_Helper_Data::insertArray($this->getTable('kbase/category_article'),
                array('article_id', 'category_id'),
                $data
            );
        }
        return $this;
    }

    /*
     * Returns Article tags
     * @param int Article ID
     * @return array Article tags
     */
    public function getTags($articleId)
    {
        if (!$articleId) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getTable('kbase/tag'), 'tag_name')
            ->where('tag_article_id=?', $articleId);

        return $db->fetchCol($select);
    }

    /*
     * Saves article-to-tag relation
     * @param int @articleId Article ID
     * @param array @tags Article tags
     */
    public function saveTags($articleId, $tags)
    {
        if (!is_array($tags)) {
            $rawTags = explode(',', strtolower($tags));
            $tags = array();

            foreach ($rawTags as $tag)
            {
                $tag = trim($tag);

                while (false !== strpos($tag, '  '))
                    $tag = str_replace('  ', ' ', $tag);

                if ($tag)
                    $tags[] = $tag;
            }
        }

        $existing = $this->getTags($articleId);
        $common = array_intersect($existing, $tags);
        $deleted = array_diff($existing, $common);
        $new = array_diff($tags, $common);

        $db = $this->_getWriteAdapter();

        if (!empty($deleted)) {

            foreach ($deleted as $tagName) {
                $arrayWords[] = $db->getConnection()->quote($tagName);
            }
            $db->delete($this->getTable('kbase/tag'),
                'tag_article_id=' . $articleId . ' AND tag_name IN (' . implode(',', $arrayWords) . ')');
        }

        if (!empty($new)) {
            $data = array();
            foreach ($new as $tagName)
                $data[] = array($articleId, $tagName);

            AW_Kbase_Helper_Data::insertArray($this->getTable('kbase/tag'),
                array('tag_article_id', 'tag_name'),
                $data
            );
        }
        return $this;
    }

    /*
     * Returns the IDs of stores that the categories belongs to
     * @param mixed $categoryIds Category IDs
     * @return array Store IDs
     */
    public function getCategorySetStoreIds($categoryIds)
    {
        if (!is_array($categoryIds))
            $categoryIds = explode(',', $categoryIds);

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('cs' => $this->getTable('kbase/category_store')),
            array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)'))
        )
            ->where('cs.category_id IN (?)', $categoryIds)
            ->group('cs.category_id');

        if ($res = $db->fetchOne($select))
            return array_unique(explode(',', $res));
        else return array();
    }

    /*
     * Returns the IDs of stores that the article belongs to through its categories
     * @param int @articleId Article Id
     * @return array Article store IDs
     */
    public function getArticleStoreIds($articleId)
    {
        if (!$articleId) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('ca' => $this->getTable('kbase/category_article')), '')
            ->joinLeft(array('c' => $this->getTable('kbase/category')),
            'ca.category_id=c.category_id',
            ''
        )
            ->joinLeft(array('cs' => $this->getTable('kbase/category_store')),
            'ca.category_id=cs.category_id',
            array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)'))
        )
            ->where('ca.article_id=?', $articleId)
            ->group('ca.article_id');

        if ($res = $db->fetchOne($select))
            return array_unique(explode(',', $res));
        else return array();
    }

    /*
     * Returns the IDs of the stores having article with the same URL key
     * @param int @articleId Article ID, needed to exclude itself from result
     * @param string @url URL key
     * @return array The IDs of the stores having article with the same URL key
     */
    public function getSameUrlArticleStoreIds($articleId, $url)
    {
        if (!$url) return array();

        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('a' => $this->getTable('kbase/article')), '')
            ->joinInner(array('ca' => $this->getTable('kbase/category_article')),
            'a.article_id=ca.article_id',
            ''
        )
            ->joinInner(array('c' => $this->getTable('kbase/category')),
            'ca.category_id=c.category_id',
            ''
        )
            ->joinInner(array('cs' => $this->getTable('kbase/category_store')),
            'ca.category_id=cs.category_id',
            array('store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT cs.store_id)'))
        )
            ->where('a.article_url_key=?', $url)
            ->group('a.article_id');

        if ($articleId)
            $select
                ->where('a.article_id<>?', $articleId);

        if ($res = $db->fetchOne($select))
            return array_unique(explode(',', $res));
        else return array();
    }

    /*
     * Returns array of categories with sub-array containing first $articleLimit articles of these categories
     * @param int @articleLimit Maximum number of articles attached to each item
     * @return array Category info with articles array
     */
    public function getCategoryWithArticleList($articleLimit)
    {
        $db = $this->_getReadAdapter();

        // getting category list
        $select = $db->select()
            ->from(array('c' => $this->getTable('kbase/category')),
            array(
                'category_id',
                'category_name',
                'category_url_key'
            )
        )
            ->joinLeft(array('cs' => $this->getTable('kbase/category_store')),
            'c.category_id=cs.category_id',
            ''
        )
            ->where('cs.store_id=?', Mage::app()->getStore()->getId())
            ->where('c.category_status=1')
            ->order('c.category_order')
            ->order('c.category_name');

        $categoryList = $db->fetchAll($select);

        // getting article list ordered by category
        $select = $db->select()
            ->from(array('a' => $this->getTable('kbase/article')),
            array(
                'article_id',
                'article_title',
                'article_url_key',
            ))
            ->joinInner(array('ca' => $this->getTable('kbase/category_article')),
            'ca.article_id=a.article_id',
            'ca.category_id'
        )
            ->joinLeft(array('cs' => $this->getTable('kbase/category_store')),
            'ca.category_id=cs.category_id',
            ''
        )
            ->where('a.article_status=1')
            ->where('cs.store_id=?', Mage::app()->getStore()->getId())
            ->where('ca.category_id IS NOT NULL')
            ->order('ca.category_id')
            ->order('a.article_rating DESC');

        $articleList = $db->fetchAll($select);

        if (!$articleLimit) $articleLimit = 100500;
        $res = array();

        foreach ($categoryList as $category)
        {
            $found = false;
            $counter = (int)$articleLimit;
            foreach ($articleList as $article)
                if ($article['category_id'] == $category['category_id']) {
                    if (!$found) {
                        $found = true;
                        $res[] = array_merge($category, array('articles' => array()));
                    }
                    end($res);
                    if ($counter-- > 0)
                        $res[key($res)]['articles'][] = $article;
                }
                elseif ($found) break;
        }
        return $res;
    }

    /*
     * Returns Maximum number of articles with the same tag
     * @return int Maximum number of articles with the same tag
     */
    public static function getTagMaxCount()
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');

        $select = $read->select()
            ->from(array('t1' => new Zend_Db_Expr('(' . self::getTagSelect()->assemble() . ')')),
            new Zend_Db_Expr('max(weight)')
        );

        return $read->fetchOne($select);
    }

    /*
     * Returns SELECT statement for tag cloud for current store
     * @param bool $calculateWeight Whether to add tag weight to the select
     * @return Varien_Db_Select Select statement object
     */
    public static function getTagSelect($calculateWeight = true)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');

        $select = $read->select()
            ->from(array('t' => $resource->getTableName('kbase/tag')),
            ''
        )
            ->joinLeft(array('a' => $resource->getTableName('kbase/article')),
            't.tag_article_id=a.article_id',
            ''
        )
            ->joinLeft(array('ca' => $resource->getTableName('kbase/category_article')),
            't.tag_article_id=ca.article_id',
            ''
        )
            ->joinLeft(array('cs' => $resource->getTableName('kbase/category_store')),
            'ca.category_id=cs.category_id',
            ''
        )
            ->where('a.article_status=1')
            ->where('cs.store_id=?', Mage::app()->getStore()->getId())
            ->group('tag_name');

        if ($calculateWeight)
            $select
                ->columns(array('weight' => new Zend_Db_Expr('Count(tag_name)')));

        return $select;
    }

    /*
     * Returns tag cloud
     * @param bool $calculateWeight Whether to add tag weight to the select
     * @return array Tags
     */
    public static function getAllTags($calculateWeight = false)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');

        $select = self::getTagSelect($calculateWeight)
            ->columns(array('tag_name'));

        return $read->fetchAll($select);
    }

    /*
    * Returns the ID of the article with the URL key given
    * @param string $urlKey URL key of the article
    * @result int Article ID
    */
    public function getIdByUrlKey($urlKey, $noCat = false)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('a' => $this->getMainTable()),
            'a.article_id'
        )
            ->joinLeft(array('ca' => $this->getTable('kbase/category_article')),
            'ca.article_id=a.article_id',
            'ca.category_id'
        )
            ->joinLeft(array('c' => $this->getTable('kbase/category')),
            'ca.category_id=c.category_id',
            ''
        )
            ->joinLeft(array('cs' => $this->getTable('kbase/category_store')),
            'c.category_id=cs.category_id',
            ''
        )
            ->where('a.article_url_key=?', $urlKey);
        if ($noCat)
            $select->where('cs.store_id IS NULL');
        else
            $select->where('cs.store_id=?', Mage::app()->getStore()->getId());
        $select->limit(1);

        return $db->fetchOne($select);
    }

    /*
     * Returns categories that the article belongs to
     * @param int $articleId Article ID
     * @return array Categories
     */
    public function getCategories($articleId)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from(array('a' => $this->getMainTable()),
            ''
        )
            ->joinLeft(array('ca' => $this->getTable('kbase/category_article')),
            'ca.article_id=a.article_id',
            ''
        )
            ->joinLeft(array('c' => $this->getTable('kbase/category')),
            'ca.category_id=c.category_id',
            'c.*'
        )
            ->where('a.article_id=?', $articleId);

        return $db->fetchAll($select);
    }

    /*
     * Updates article rating
     * @param int $articleId Article ID
     * @param int $rating Article rating voted
     */
    public function vote($articleId, $rating)
    {
        $db = $this->_getWriteAdapter();

        $db->query('UPDATE ' . $this->getTable('kbase/article')
            . ' SET `article_rating` = (`article_rating`*`article_rating_votes`+' . $db->quote((int)$rating) . ')/(`article_rating_votes`+1),'
            . '`article_rating_votes`=`article_rating_votes`+1'
            . ' WHERE article_id=' . $db->quote($articleId));
    }

}