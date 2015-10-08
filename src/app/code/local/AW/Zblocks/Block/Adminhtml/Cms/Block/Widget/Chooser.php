<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
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
 * @package    AW_Zblocks
 * @version    2.3.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Zblocks_Block_Adminhtml_Cms_Block_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid {
 
    public function __construct($arguments=array()) {
        
        parent::__construct($arguments);
        $this->setDefaultSort('block_identifier');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('block_is_active' => '1'));
        
    }
    
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element) {

        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('zblocks_admin/adminhtml_block_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
                ->setElement($element)
                ->setTranslationHelper($this->getTranslationHelper())
                ->setConfig($this->getConfig())
                ->setFieldsetId($this->getFieldsetId())
                ->setSourceUrl($sourceUrl)
                ->setUniqId($uniqId);
        
        
 
        if ($element->getValue()) {
            $block = Mage::getModel('zblocks/zblocks')->load($element->getValue());
            if ($block->getId()) {                
                $label = trim($block->getBlockTitle());                
                if(empty($label)) {                    
                    $label = Mage::helper('zblocks')->__("Z-block #%d", $block->getId());
                }                 
                $chooser->setLabel($label);
           }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    
    public function getRowClickCallback() {
        
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;    
                 
                 if(/^\s+$/i.test(blockTitle)) {
                     blockTitle = "Z-block #" + blockId;
                 }

                ' . $chooserJsObject . '.setElementValue(blockId);
                ' . $chooserJsObject . '.setElementLabel(blockTitle);
                ' . $chooserJsObject . '.close();
            }
        ';
        return $js;
        
    }

   
    protected function _prepareCollection() {

        $collection = Mage::getModel('zblocks/zblocks')->getCollection();
        $collection->getSelect()
                ->from('', array('block_count' => 'count(c.zblock_id)',
                    'position' => "if(block_position='custom', CONCAT(block_position_custom, ' *'), block_position)"))
                ->joinLeft(array('c' => $collection->getTable('zblocks/content')), 'main_table.zblock_id = c.zblock_id', '')
                ->group('main_table.zblock_id');

        $this->setCollection($collection);
        parent::_prepareCollection();
        foreach ($this->getCollection()->getItems() as $item) {
            $item->setData('store_ids', @explode(',', $item->getData('store_ids')));
        } 
        return $this;
    }
 
    protected function _prepareColumns() {

        $this->addColumn('zblock_id', array(
            'header' => Mage::helper('zblocks')->__('Z-block ID'),
            'align' => 'left',
            'index' => 'zblock_id',
        ));


        $this->addColumn('block_title', array(
            'header' => Mage::helper('zblocks')->__('Title'),
            'align' => 'left',
            'index' => 'block_title',
        ));

        $this->addColumn('rotator_mode', array(
            'header' => Mage::helper('zblocks')->__('Mode'),
            'index' => 'rotator_mode',
            'type' => 'options',
            'align' => 'left',
            'width' => '80px',
            'options' => Mage::helper('zblocks')->getRotatorModesToOptionsArray(),
        ));

        $this->addColumn('schedule_from_date', array(
            'header' => Mage::helper('zblocks')->__('Date From'),
            'index' => 'schedule_from_date',
            'type' => 'date',
            'width' => '100px',
        ));

        $this->addColumn('schedule_to_date', array(
            'header' => Mage::helper('zblocks')->__('Date To'),
            'index' => 'schedule_to_date',
            'type' => 'date',
            'width' => '100px',
        ));

        $this->addColumn('block_is_active', array(
            'header' => Mage::helper('zblocks')->__('Status'),
            'index' => 'block_is_active',
            'align' => 'left',
            'width' => '80px',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('zblocks')->__('Disabled'),
                1 => Mage::helper('zblocks')->__('Enabled')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        
        return $this->getUrl('zblocks_admin/adminhtml_block_widget/chooser', array('_current' => true));
        
    }

}
