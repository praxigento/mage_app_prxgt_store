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


class AW_Zblocks_Test_Block_Block extends EcomDev_PHPUnit_Test_Case {
    
    public function setup() {
        AW_Zblocks_Test_Model_Mocks_Foreignresetter::dropForeignKeys();
        parent::setup();
    }

    /**
     * @ctest
     * @dataProvider provider__toHtml__empty
     * The main goal of this test is simply verify that Zblocks module is stable and 
     * works as expected with different (event that do not exits) positions
     */
    public function _toHtml__empty($position,$assert=true) {
        
        $testBlock = new AW_Zblocks_Test_Block_Block_Testblock(array('block_position' => $position));        
        $block = $testBlock->renderView();
        if($assert) {
            $this->assertEquals($block, '', 'Empty string should be displayed');
        }
        return $block;
    }

    public function provider__toHtml__empty() {
        return array(
            array('menu.top'),
            array('position_that_does_not_exitsts'),
            array(''),
            array(5),
            array(' ` ~ ! @ # $ % ^ & * ( ) - _ = + \ | ][ {} " : ;  /., ?><')
        );
    }



   /**
    * @test
    * @loadFixture
    * @dataProvider provider__toHtml
    * 
    */
    public function _toHtml($position,$html) {
       
       $zblock = Mage::getModel('zblocks/zblocks')->load(2);       
       $zblock->setBlockPosition($position);           
       $testBlock = new AW_Zblocks_Test_Block_Block_Testblock($zblock->getData());   
       $this->assertEquals(preg_match("#".$html."#is",$testBlock->renderView()),1,'Incorrect block contents'); 
       
    }
    
    public function provider__toHtml() {
        return array(
            array('menu-top','PHPUNIT_MENU_TOP')           
        );      
    }
    
}