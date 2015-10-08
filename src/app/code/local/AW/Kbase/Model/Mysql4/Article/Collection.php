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


class AW_Kbase_Model_Mysql4_Article_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    const TABLE_ALIAS_ARTICLE = 'a';
    const TABLE_ALIAS_CATEGORY_ARTICLE = 'ca';
    const TABLE_ALIAS_CATEGORY = 'c';
    const TABLE_ALIAS_CATEGORY_STORE = 'cs';
    const TABLE_ALIAS_STORE = 's';
    const TABLE_ALIAS_TAG = 't';
    const TABLE_ALIAS_USER = 'u';

    /*
     * @var array $_sortingFields Sortable article field list
     */
    protected $_sortingFields = array(
        AW_Kbase_Model_Source_Sorting::BY_DATE => 'article_date_updated',
        AW_Kbase_Model_Source_Sorting::BY_RATING => 'article_rating',
    );

    public function _construct()
    {
        parent::_construct();
        $this->_init('kbase/article');
    }

    protected function _afterLoad()
    {
        foreach ($this->_items as $model) {

            $model->afterLoad();
            $model->setData('store_ids', explode(',', $model->getData('store_ids')));

        }

        return parent::_afterLoad();
    }

    /*
     * Covers original bug in Varien_Data_Collection_Db
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns(array('count_total' => new Zend_Db_Expr('COUNT(DISTINCT main_table.article_id)')));

        return $countSelect;
    }

    /*
     * Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.article_id');

        return $this->getConnection()->fetchCol($idsSelect);
    }

    /*
     * Applies limit to collection select
     * @param int $limit limit
     */
    public function limit($limit)
    {
        if ($limit)
            $this->getSelect()->limit($limit);

        return $this;
    }

    /*
     * Joins admin/user table to the select
     */
    protected function _joinAuthorTable($tableAliasSuffix = '')
    {
        $tableName = self::TABLE_ALIAS_USER . $tableAliasSuffix;

        if (!array_key_exists($tableName, $this->getSelect()->getPart(Zend_Db_Select::FROM)))
            $this->getSelect()
                ->joinLeft(array($tableName => $this->getTable('admin/user')),
                'main_table.article_author_id=' . $tableName . '.user_id',
                ''
            );

        return $this;
    }

    /*
     * Joins category-to-article and (optionally) category tables to the select
     * @param bool $joinCategories Whether to join category table
     * @param string $tableAliasCASuffix Category-to-article table alias
     * @param string $tableAliasCSuffix Category table alias
     */
    protected function _joinCategoryTable($joinCategories = false, $tableAliasCASuffix = '', $tableAliasCSuffix = '')
    {
        $ca = self::TABLE_ALIAS_CATEGORY_ARTICLE . $tableAliasCASuffix;

        if (!array_key_exists($ca, $this->getSelect()->getPart(Zend_Db_Select::FROM)))
            $this->getSelect()
                ->joinLeft(array($ca => $this->getTable('kbase/category_article')),
                'main_table.article_id=' . $ca . '.article_id',
                ''
            );

        if ($joinCategories) {
            $c = self::TABLE_ALIAS_CATEGORY . $tableAliasCSuffix;

            if (!array_key_exists($c, $this->getSelect()->getPart(Zend_Db_Select::FROM)))
                $this->getSelect()
                    ->joinLeft(array($c => $this->getTable('kbase/category')),
                    $c . '.category_id=' . $ca . '.category_id',
                    ''
                );
        }

        return $this;
    }

    /*
     * Joins category-to-store table to the select
     */
    protected function _joinCategoryStoreTable()
    {

        if (!array_key_exists(self::TABLE_ALIAS_STORE, $this->getSelect()->getPart(Zend_Db_Select::FROM))) {

            $this->_joinCategoryTable(false);

            if (!array_key_exists(self::TABLE_ALIAS_CATEGORY_STORE, $this->getSelect()->getPart(Zend_Db_Select::FROM))) {
                $this->getSelect()
                    ->joinLeft(array(self::TABLE_ALIAS_CATEGORY_STORE => $this->getTable('kbase/category_store')), self::TABLE_ALIAS_CATEGORY_ARTICLE . '.category_id=' . self::TABLE_ALIAS_CATEGORY_STORE . '.category_id', ''
                );
            }
        }

        return $this;
    }

    /*
     * Joins tag table to the select
     * @param string $tableAliasSuffix Tag alias suffix added to distinguish filtering and grouping tables
     */
    protected function _joinTagTable($tableAliasSuffix = '')
    {
        $tableAlias = self::TABLE_ALIAS_TAG . $tableAliasSuffix;

        if (!array_key_exists($tableAlias, $this->getSelect()->getPart(Zend_Db_Select::FROM)))
            $this->getSelect()
                ->joinLeft(array($tableAlias => $this->getTable('kbase/tag')),
                'main_table.article_id=' . $tableAlias . '.tag_article_id',
                ''
            );

        return $this;
    }

    /*
     * Group select result by article_id field
     */
    protected function _groupByArticle()
    {
        if (!in_array('main_table.article_id', $this->getSelect()->getPart(Zend_Db_Select::GROUP)))
            $this->getSelect()
                ->group('main_table.article_id');

        return $this;
    }

    /*
     * Applies sorting to the collection
     * @param int Sorting order
     * @param sting Sorting direction
     */
    public function applySorting($sortOrder, $sortDir)
    {
        if ($sortOrder
            && array_key_exists($sortOrder, $this->_sortingFields)
        ) $this->getSelect()
            ->order($this->_sortingFields[$sortOrder] . ' ' . $sortDir);

        return $this;
    }

    /*
     * Joins category-to-article table and adds 'category_ids' grouped field to the select
     */
    public function addCategoryIds()
    {
        $this
            ->_joinCategoryTable()
            ->_groupByArticle()
            ->getSelect()
            ->columns(array(
            'category_ids' => new Zend_Db_Expr('GROUP_CONCAT(' . self::TABLE_ALIAS_CATEGORY_ARTICLE . '.category_id)'),
        ));


        return $this;
    }

    /*
     * Joins category table and adds category names as 'category_names' grouped field to the select
     */
    public function addCategoryNames()
    {
        $this
            ->_joinCategoryTable(true)
            ->_groupByArticle()
            ->getSelect()
            ->columns(array(
            'category_names' => new Zend_Db_Expr('GROUP_CONCAT(' . self::TABLE_ALIAS_CATEGORY . '.category_name ORDER BY ' . self::TABLE_ALIAS_CATEGORY . '.category_name)'),
        ));

        return $this;
    }

    /*
     * Joins category table and adds serialized category ids and names as 'category_names_ids' grouped field to the select
     */
    public function addCategoryNamesAndIds()
    {
        $this
            ->_joinCategoryTable(true)
            ->_groupByArticle()
            ->getSelect()
            ->columns(array(
            'category_names_ids' => new Zend_Db_Expr('GROUP_CONCAT(CONCAT(' . self::TABLE_ALIAS_CATEGORY . '.category_id, "=", ' . self::TABLE_ALIAS_CATEGORY . '.category_name) ORDER BY ' . self::TABLE_ALIAS_CATEGORY . '.category_name SEPARATOR "##")'),
        ));


        return $this;
    }

    /*
     * Joins tags table and adds article tags as 'article_tags' grouped field to the select
     */
    public function addTags()
    {
        $this
            ->_joinTagTable('_g')
            ->_groupByArticle()
            ->getSelect()
            ->columns(array(
            'article_tags' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT ' . self::TABLE_ALIAS_TAG . '_g.tag_name)'),
        ));

        return $this;
    }

    /*
     * If Multistore mode is on, joins category-to-store table and applies store condition to the select
     * @param int $storeId Store ID
     */
    public function setStoreFilter($storeId = null)
    {

        if (0 === $storeId) return $this;

        if (is_null($storeId)) {
            //            if(Mage::app()->isSingleStoreMode()) return $this;
            $storeId = Mage::app()->getStore()->getId();
        }

        $this
            ->_joinCategoryStoreTable()
            ->getSelect()
            ->where('((cs.store_id = ?) OR (cs.store_id IS NULL))', $storeId);

        return $this;
    }


    public function addStoreIds()
    {

        $this->_joinCategoryStoreTable()
            ->getSelect()
            ->columns(array(
            'store_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT ' . self::TABLE_ALIAS_CATEGORY_STORE . '.store_id)'),
        ));
        return $this;

    }


    public function addStoreFilter($stores = array())
    {

        $this->_joinCategoryStoreTable();

        $_stores = array(Mage::app()->getStore()->getId());
        if (is_string($stores)) $_stores = explode(',', $stores);
        if (is_array($stores)) $_stores = $stores;
        if (!in_array('0', $_stores))
            array_push($_stores, '0');
        if ($_stores == array(0)) return $this;
        $_sqlString = '(';
        $i = 0;
        foreach ($_stores as $_store) {

            $_sqlString .= sprintf("cs.store_id = %s", $this->getConnection()->quote($_store));
            if (++$i < count($_stores))
                $_sqlString .= ' OR ';
        }

        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);


        return $this;
    }

    /*
     * Applies status=enabled condition to the select
     * @param int $enabled article_status field value
     */
    public function addStatusFilter($enabled = 1)
    {
        $this->getSelect()
            ->where('main_table.article_status=?', (int)$enabled);

        return $this;
    }

    /*
     * Joins category-to-article table and applies category condition to the select
     * @param int $categoryId The ID of the category
     */
    public function addCategoryFilter($categoryId)
    {
        if ($categoryId)
            $this
                ->_joinCategoryTable(false, '_f')
                ->getSelect()
                ->where(self::TABLE_ALIAS_CATEGORY_ARTICLE . '_f.category_id=?', (int)$categoryId);

        return $this;
    }

    /*
     * Parses the query and applies the query phrase-containing condition to the select
     * @param string|array $words The query
     */
    public function addSearchFilter($words)
    {

        if (!is_array($words)) {
            $words = AW_Kbase_Helper_Data::parseQuery($words);
        }

        foreach ($words as $word) {
            $arrayWhere[] = $this->getConnection()->quoteInto("(main_table.article_title LIKE ?)", '%' . $word . '%');
            $arrayWhere[] = $this->getConnection()->quoteInto("(main_table.article_text  LIKE ?)", '%' . $word . '%');
        }
        if (is_array($arrayWhere) && count($arrayWhere) > 0) {
            $this->getSelect()->where(implode(' OR ', $arrayWhere));
        }

        return $this;
    }

    /*
     * Adds author name as 'u_name' field (as firstname lastname) to the select
     */
    public function addAuthorName()
    {
        $this
            ->_joinAuthorTable()
            ->getSelect()
            ->columns(
            array('u_name' => new Zend_Db_Expr('CONCAT(' . self::TABLE_ALIAS_USER . '.firstname, \' \', ' . self::TABLE_ALIAS_USER . '.lastname)'))
        //                    'u_firstname'  => 'firstname',
        //                    'u_lastname'   => 'lastname',
        //                    'u_email'      => 'email',
        //                    'u_username'   => 'username',
        //                )
        //                'u.*'
        );
        return $this;
    }

    /*
     * Adds author name to the select (as firstname lastname)
     */
    public function addAuthorFilter($author)
    {
        $this->_joinAuthorTable('_f');

        $words = explode(' ', $author);

        foreach ($words as $word) {
            $arrayWhere[] = $this->getConnection()->quoteInto("(" . self::TABLE_ALIAS_USER . "_f.firstname LIKE ?)", '%' . $word . '%');
            $arrayWhere[] = $this->getConnection()->quoteInto("(" . self::TABLE_ALIAS_USER . "_f.lastname LIKE ?)", '%' . $word . '%');
        }
        if (is_array($arrayWhere) && count($arrayWhere) > 0) {
            $this->getSelect()->where(implode(' OR ', $arrayWhere));
        }

        return $this;
    }

    /*
     * Joins tag table and applies the tag condition to the select
     * @param string $tag A tag to filter by
     */
    public function addTagFilter($tag)
    {
        $this
            ->_joinTagTable('_f')
            ->getSelect()
            ->where(self::TABLE_ALIAS_TAG . '_f.tag_name=?', $tag);

        return $this;
    }

}
