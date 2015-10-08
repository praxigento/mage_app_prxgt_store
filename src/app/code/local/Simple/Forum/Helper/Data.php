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

class Simple_Forum_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_LAYOUT  = 'forum/designsettings/layout';

	const FORUM_ICONS_PATH        = 'forum/images/icons/';
	const FORUM_ICONS_SMALL_CLASS = 'simple-forum-icons-small';
	const FORUM_ICONS_PATH_MEDIUM = 'forum/images/icons_medium/';

	private $quantity_posts_hot_topic = 5;

	private $forum_icons_id  = array(

       'smile',
       'wink',
       'kiss',
       'saint',
       'evil',
       'question',
       'cry',
       'devil',
       'shame',
       'disappointed',
       'smart',
       'laughter',
       'monkey',
       'sad',
       'accept',
       'cancel',
	);

	private $forum_icons_img = array(
   		'accept' => 'accept.png',
   		'cancel' => 'cancel.png',
   		'saint'  => 'saint.png',
   		'evil'   => 'evil.png',
   		'question' => 'question.png',
   		'cry'    => 'cry.png',
   		'devil'  => 'devil.png',
   		'shame' => 'shame.png',
   		'disappointed' => 'disappointed.png',
   		'smart' => 'smart.png',
   		'laughter' => 'laughter.png',
   		'smile' => 'smile.png',
   		'monkey' => 'monkey.png',
   		'sad' => 'sad.png',
   		'kiss' => 'kiss.png',
   		'wink' => 'wink.png',
	);

	private $forum_icons_key_lang = array(
        'accept' => 'Accept',
        'cancel' => 'Cancel',
        'saint'  => 'Saint',
        'evil'   => 'Evil',
        'question' => 'Question',
        'cry' => 'Cry',
        'devil' => 'Devil',
        'shame' => 'Shame',
        'disappointed' => 'Disappointed',
        'smart' => 'Smart',
        'laughter' => 'Laughter',
        'smile' => 'Smile',
        'monkey' => 'Monkey',
        'sad'    => 'Sad',
        'kiss'   => 'Kiss',
        'wink'   => 'Wink'
	);

	public function getLayout()
    {
        return Mage::getStoreConfig( self::XML_PATH_LAYOUT );
    }

    public function getForumIcons()
    {         $return        = array();
         $return['ids'] = $this->forum_icons_id;
         $return['img'] = $this->forum_icons_img;
         $return['key'] = $this->forum_icons_key_lang;

         return $return;
    }

    public function getIconImg($id = null)
    {
    	$return = '';
    	if($id)
    	{
        	if(!empty($this->forum_icons_img[$id]))
        	{
        		$return = Mage::getDesign()->getSkinUrl(self::FORUM_ICONS_PATH . $this->forum_icons_img[$id]);
        	}
    	}
    	return $return;
    }

    public function getIconImgFront($id = null)
    {
    	$return = '';
    	if($id)
    	{
        	if(!empty($this->forum_icons_img[$id]))
        	{
        		$return = self::FORUM_ICONS_PATH . $this->forum_icons_img[$id];
        	}
    	}
    	return $return;
    }

    public function getIconImgMedium($id = null)
    {
        $return = '';
    	if($id)
    	{
        	if(!empty($this->forum_icons_img[$id]))
        	{
        		$return = Mage::getDesign()->getSkinUrl(self::FORUM_ICONS_PATH_MEDIUM . $this->forum_icons_img[$id]);
        	}
    	}
    	return $return;
    }

    public function getIconImgFrontMedium($id = null)
    {
        $return = '';
    	if($id)
    	{
        	if(!empty($this->forum_icons_img[$id]))
        	{
        		$return = self::FORUM_ICONS_PATH_MEDIUM . $this->forum_icons_img[$id];
        	}
    	}
    	return $return;
    }

    public function getForumIconsRadios($no_none = false)
    {    	$icons = $this->getForumIcons();
    	$ret   = array();
    	if(!$no_none)
    	{            $arr = array(
            	'label' => '&nbsp;' . $this->__('None'),
            	'value' => ''
            );
            $ret[] = $arr;
    	}
    	if(is_array($icons['ids']) && !empty($icons['ids']))
    	{         	foreach($icons['ids'] as $id)
         	{            	if(!empty($icons['img'][$id]) && !empty($icons['key'][$id]))
            	{            		$arr = array(
                    	'label' => '<nobr>&nbsp;&nbsp;<img src="' . $this->getIconImg($id) . '" class="'.self::FORUM_ICONS_SMALL_CLASS.'"> ' . $this->__($icons['key'][$id]) . '</nobr>',
                     	'value' => '' . $id,
            		);

            		$ret[] = $arr;
            	}
         	}
    	}
    	return $ret;
    }

    public function getQuantityPostsHotTopic()
    {    	return $this->quantity_posts_hot_topic;
    }
}

?>