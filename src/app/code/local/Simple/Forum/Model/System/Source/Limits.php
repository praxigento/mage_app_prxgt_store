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
 
/**
 * Used in creating options for page limits Forums, Topics, Posts
 *
 */
class Simple_Forum_Model_System_Source_Limits
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>'5, 10, 15'),
            array('value' => 1, 'label'=>'10, 15, 30'),
            array('value' => 2, 'label'=>'20, 30, 50'),
        );
    }

}
