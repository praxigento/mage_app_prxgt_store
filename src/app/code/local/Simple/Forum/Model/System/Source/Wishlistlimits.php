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
class Simple_Forum_Model_System_Source_Wishlistlimits
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 3, 'label'=>'3'),
            array('value' => 4, 'label'=>'4'),
            array('value' => 5, 'label'=>'5'),
            array('value' => 6, 'label'=>'6'),
            array('value' => 7, 'label'=>'7'),
            array('value' => 8, 'label'=>'8'),
            array('value' => 9, 'label'=>'9'),
            array('value' => 10, 'label'=>'10'),
        );
    }

}
