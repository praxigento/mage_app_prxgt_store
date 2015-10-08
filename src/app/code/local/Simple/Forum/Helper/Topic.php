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

class Simple_Forum_Helper_Topic extends Mage_Core_Helper_Abstract
{

	private $url_all_topics       = 'forum/';
	private $skip_id              = 0;

	private $_NOT_VALID_KEYS      = array('forum/topic');

	private $pages_limits         = array
	(
		array(5, 10, 15),
		array(10, 15, 30),
		array(20, 30, 50)
	);

	public $_isActive             = false;

	public $breadCrumbBlock       = false;

	public $obj;

	public $latestPosts           = array();

	private $editor_locales       = array
	(
		'en',
		'de',
		'fr',
		'cz',
		'nl',
		'pl',
		'it',
		'ja',
		'pt',
		'sv'
	);

	public function getAvLovale($locale)
	{
		$arr_tmp = explode('_', $locale);
		if(!empty($arr_tmp[0]))$short   = $arr_tmp[0];
		else return $this->editor_locales[0];

		if(in_array($short, $this->editor_locales))
		{
			return $short;
		}
		else
		{
			return $this->editor_locales[0];
		}
	}

	public function canShow($topic)
	{
		if($this->isModerator()) return true;
		else return ($topic->getStatus() == 1);
	}

	public function setObject($obj)
	{
		$this->obj = $obj;
	}

	public function getObject()
	{
		return $this->obj;
	}

	public function getBreadcrumbPath($urlBegin = '', $is_edit = false, $_have_all_urls = false)
	{
		$bpath = array();
		$current_object = Mage::registry('current_object');
		$parent_object  = Mage::registry('current_object_parent');
		$bpath['alltopics'] = array('label'=>$this->__('Forum'));

		if($_have_all_urls)
		{
			$bpath['alltopics']['link'] = $urlBegin . $this->url_all_topics;
		}
		if($this->breadCrumbBlock)
		{
			$bpath['alltopics']['link'] = $urlBegin . $this->url_all_topics;
			$bpath['b_block']           = array('label'=>$this->breadCrumbBlock );
		}
		if(!$current_object && !$is_edit)
		{
			return $bpath;
		}
		if($current_object->getId() && !$is_edit)
		{
			$bpath['alltopics']['link'] = $urlBegin . $this->url_all_topics;
			if($current_object->getParent_id() && $current_object->getParent_id() != 0)
			{
				$bpath = $this->addTopicsBreadcrumbsParent($current_object->getParent_id(), $bpath);
			}
			$bpath['topic_' . $current_object->getId()] = array('label'=>$current_object->getTitle());
		}
		elseif($current_object->getId() && $is_edit)
		{
			$bpath['alltopics']['link'] = $urlBegin . $this->url_all_topics;
			if($current_object->getParent_id() && $current_object->getParent_id() != 0)
			{
				$bpath = $this->addTopicsBreadcrumbsParent($current_object->getParent_id(), $bpath);
			}
			if(Mage::registry('current_object')->getSystem_user_id() == Mage::registry('current_customer')->getId() &&  Mage::registry('current_object_post')->getSystem_user_id() || $this->isModerator())
			{
				$bpath['topic_' . $current_object->getId()] = array('label'=> $this->__('Edit Topic "%s" and Post', $current_object->getTitle()));
			}
			elseif(Mage::registry('current_object_post')->getSystem_user_id() == Mage::registry('current_customer')->getId())
			{
				$bpath['topic_' . $current_object->getId()] = array('label'=>$this->__('Edit Post in Topic "%s"', $current_object->getTitle()));
			}
			else
			{
				$bpath['topic_' . $current_object->getId()] = array('label'=>$this->__('Add Post to Topic "%s"', $current_object->getTitle() ));
			}
		}
		elseif( $is_edit )
		{
			$bpath['alltopics']['link'] = $urlBegin . $this->url_all_topics;
			if($parent_object->getId() && $parent_object->getId() != 0)
			{
				$bpath = $this->addTopicsBreadcrumbsParent($parent_object->getId(), $bpath);
			}
			$bpath['topic_' . $current_object->getId()] = array('label'=>$this->__('Add New Topic') );
		}
		return $bpath;
	}

	private function addTopicsBreadcrumbsParent($id, $bpath)
	{
		$model = Mage::getModel('forum/topic');
		$data  = $model->load($id);
		if($data->getTitle() && $data->getId())
		{
			if($data->getParent_id())
			{
				$bpath = $this->addTopicsBreadcrumbsParent($data->getParent_id(), $bpath);
			}
			$bpath['topic_' . $data->getId()] = array('label'=>$data->getTitle(), 'link' => $this->getObject()->getViewUrl($data->getId(), $data));
		}
		return $bpath;
	}

	public function getTopicsTreeArr($parent_id = 0, $status_all = true, $where = array(), $skip_parent = false, $use_store = false, $store_id = 0)
	{
		$model =  Mage::getModel('forum/topic')->getCollection();
		if($use_store)
		{
			$model->addStoreFilterToCollection($store_id);
		}
		if(!$status_all)
		{
			$model->getSelect()->where( 'status=1' );
		}
		if(count($where) > 0)
		{
			foreach($where as $key=>$val)
			{
				$model->getSelect()->where($val, $key);
			}
		}
		if(!$skip_parent)
		{
			$model->getSelect()->where('parent_id=?', $parent_id);
		}

		$data = $model->getData();
		if(count($data) > 0)
		{
			if($this->isModerator())
			{
				$status_all = true;
				$where      = array();
			}
			foreach($data as $key=>$val)
			{
				$data[$key]['_childs'] = $this->getTopicsTreeArr($val['topic_id'], $status_all, $where, false, $use_store, $store_id);
			}
		}
		return $data;
	}

	public function getOptionsTopics($status_all = true, $skip_id = 0, $where = array(), $no_text = false, $skip_parent = false, $ret_as_it = false, $use_store = false, $store_id= 0, $no_empty = false, $subtopics_parent_only = false)
	{
		$ret            = array();
		if(!$no_empty)
		{
			$no_text        = $no_text ? $no_text : $this->__('No Topic');
			$ret[]          = array
							 (
								'value' => 0,
								'label' => $no_text
							  );
		}
		$this->subtopics_parent_only = $subtopics_parent_only;
		$arr = $this->getTopicsTreeArr(0, $status_all, $where, $skip_parent, $use_store, $store_id);
		$this->skip_id = $skip_id;
		if(!$ret_as_it)$ret = $this->addTopicsRecursive($arr, $ret);
		else $ret = $this->addTopicsRecursiveArr($arr, $ret);

		return $ret;
	}

	private function addTopicsRecursiveArr($arr, $ret = array())
	{
		if(is_array($arr) && count($arr) > 0)
		{
			foreach($arr as $val)
			{
				if($val['topic_id'] == $this->skip_id || ($this->subtopics_parent_only && $val['is_category'] == 0 && $val['have_subtopics'] == 0))
				{
					continue;
				}
				if(($val['is_category'] == 1 &&  is_array($val['_childs']) && count($val['_childs']) > 0))
				{
					$ret[] = array
					(
						'value'    => $this->addTopicsRecursiveArr($val['_childs']),
						'label'    => $val['title'],
						'style'    => 'font-weight:bold;',
						'url_text' => $val['url_text']
					);
				}
				elseif($val['is_category'] == 0)
				{
					$ret[] = array
					(
						'value'    => $val['topic_id'],
						'label'    => $val['title'],
						'url_text' => $val['url_text']
					);
				}
			}
		}
		return $ret;
	}

	private function addTopicsRecursive($arr, $ret, $depth = 0)
	{
		if(is_array($arr) && count($arr) > 0)
		{
			foreach($arr as $val)
			{
				if($val['topic_id'] == $this->skip_id || ($this->subtopics_parent_only && $val['is_category'] == 0 && $val['have_subtopics'] == 0))
				{
					continue;
				}
				$nbsp = str_pad("", intval((3*($depth/10))*4), " - ", STR_PAD_LEFT);
				$ret[] = array
				(
					'value'    => $val['topic_id'],
					'label'    => $nbsp . $val['title'],
					'label_nbsp'    => $nbsp .  $val['title'],
					'style'    => 'margin-left:' . intval($depth) . 'px;' . ($depth == 0 ? 'font-weight:bold;' : ''),
					'url_text' => $val['url_text']
				);
				if(!empty($val['_childs']) && ($val['is_category'] == 1 || $val['have_subtopics'] == 1))
				{
					if(is_array($val['_childs']) && count($val['_childs']) > 0)
					{
						$ret = $this->addTopicsRecursive( $val['_childs'], $ret, $depth + 10 );
					}
				}
			}
		}

		return $ret;
	}

	public function validateUrlKey($key, $id, $store_id = false)
	{
		if(in_array($key, $this->_NOT_VALID_KEYS))
		{
        	return true;
		}

		if(!$store_id) $store_id = Mage::app()->getStore()->getId();
		$model = Mage::getModel('forum/topic')->getCollection();
		$model->getSelect()->where('url_text=?', trim($key))->where('topic_id!=?', $id);
		if($store_id)
		{
			$model->getSelect()->where('store_id=? OR store_id = 0', $store_id);
		}
		return (($model->getSize()) > 0 ? true : false);
	}

	public function updateUrlRewrite($id_path, $storeId, $request_path, $target_path)
	{
		$this->deleteUrlRewrite($id_path);
		$model = Mage::getModel('core/url_rewrite')->setId(NULL)
		->setRequest_path($request_path)
		->setStore_id($storeId)
		->setTarget_path($target_path)
		->setId_path($id_path);

		$model->save();
	}

	public function updateUrlRewriteRequestPath($id_path, $request_path)
	{
		$m = Mage::getModel('core/url_rewrite')->load($id_path,'id_path');
		if($m->getId())
		{
			$m->setRequest_path($request_path);
			$m->save();
		}
	}

	public function deleteUrlRewrite($id_path)
	{
		if( $id_path && $id_path!='' )
		{
            try
			{
				$d = Mage::getModel('core/url_rewrite')->load($id_path, 'id_path');
				if($d->getId())
				{
	                $Model = Mage::getModel('core/url_rewrite');
	                $Model->setId($d->getId())
	                    ->delete();
	            }
            }
			catch (Exception $e)
			{
            }
        }
	}

	public function getUrlKeyFromTitle($string)
	{
		$r =  preg_replace("/[^a-zA-Z0-9-\.\-\s]/", "", $string);
		return $r;
	}

	public function buildUrlKeyFromTitle($title, $id)
	{
		$title_to_check = $new_title = $this->getUrlKeyFromTitle( str_replace(' ', '-', strtolower($title)) );
		$t = 1;
		if(str_replace('.', '', str_replace('-', '', $title_to_check)) == '')
		{
			$addon          = date('Y-m-d');
			$new_title      = $title_to_check = 'simple-forum-' . $addon;
		}
		while($this->validateUrlKey('forum/' . $title_to_check, $id))
		{
			$t++;
			$title_to_check = $new_title . ($t ? '-' . $t : '');
		}
		return $title_to_check;
	}

	public function ___getStoreId($_id)
	{
		$o = Mage::getModel('forum/topic')->load($_id);
		return $o->getStore_id();
	}

	public function ___updateUrlStoreById($_id, $store_id)
	{
		$id_path = $this->buildIdPath($_id);
		$o       = Mage::getModel('core/url_rewrite')->load($id_path, 'id_path');
		if($o->getId())
		{
			$o->setStore_id($store_id);
			$o->save();
		}
	}

	public function updateTopicStoreId($_id, $store_id)
	{
		$m = Mage::getModel('forum/topic')->load($_id);
		if($m->getId())
		{
			$m->setStore_id( $store_id );
			$m->save();
		}
	}

	public function getIdTopicByTitle($url, $status_all = false)
	{
		$modelCollection = Mage::getModel('forum/topic')->getCollection()->setPageSize(1);
		if($status_all)
		{
			 $modelCollection->getSelect()->where('status=?', 1);
		}
		$modelCollection->getSelect()->where('url_text=?', $url);
		foreach($modelCollection as $val)
		{
			return $val->getId();
		}
	}

	public function buildRequestPath( $id )
	{
		return 'forum/topic/view/id/' . $id . '/';
	}

	public function buildRequestForumPath( $id )
	{
		return 'forum/topic/view/id/' . $id . '/';
	}

	public function buildIdForumPath( $id )
	{
		return 'topic/view/' . $id;
	}

	public function buildIdPath( $id )
	{
		return 'topic/view/' . $id;
	}

	public function getChildsTopics($id)
	{
		$isModerator = $this->isModerator();
		$c = Mage::getModel('forum/topic')->getCollection();
		if(!$isModerator)$c->getSelect()->where('status=1');
		$c->getSelect()->where('parent_id=?', $id);
		return $c;
	}

	public function getTopicsQuantity( $id, $quantity = 0 )
	{
		$modelData = $this->getChildsTopics($id);
		if($modelData->getSize())
		{
			$quantity += $modelData->getSize();
			foreach($modelData as $val)
			{
				if($val->getIs_category() == 1 || $val->getHave_subtopics() == 1)
				{
					$new_id   = $val->getId();
					$quantity = $this->getTopicsQuantity($new_id, $quantity);
				}
			}
		}
		return $quantity;
	}

	public function getChildsPosts($obj, $parent_obj = false)
	{
		if($obj->getHave_subtopics() == 1)
		{
			return;
		}
		$isModerator = $this->isModerator();
		$model  = Mage::getModel('forum/post')->getCollection();
		if(!$isModerator)$model->getSelect()->where('status=1');
		$model->getSelect()->where('parent_id=?', $obj->getId());
		if($parent_obj)
		{
			$this->setLatestPosts($model, $obj,  $parent_obj);
		}
		return $model;
	}

	public function setLatestPosts($model, $obj, $parent_obj)
	{
		if($model->getSize())
		{
			foreach($model as $post)
			{
				if(empty($this->latestPosts[$parent_obj->getId()]))
				{
					$this->___setLatestPost($obj, $post, $parent_obj);
				}
				elseif( $post->getCreated_time() )
				{
					if(strtotime($this->latestPosts[$parent_obj->getId()]['created_time']) < strtotime($post->getCreated_time()))
					{
						$this->___setLatestPost($obj, $post, $parent_obj);
					}
				}
			}
		}
	}

	private function ___setLatestPost($obj, $post, $parent_obj)
	{
		$this->latestPosts[$parent_obj->getId()]['created_time']  = $post->getCreated_time();
		$this->latestPosts[$parent_obj->getId()]['topic_obj']     = $obj;
		$this->latestPosts[$parent_obj->getId()]['user_name']     = $post->getUser_name();
		$this->latestPosts[$parent_obj->getId()]['user_nick']     = $post->getUser_nick();
		$this->latestPosts[$parent_obj->getId()]['system_user_id']     = $post->getSystem_user_id();
		$this->latestPosts[$parent_obj->getId()]['post_id']       = $post->getId();
		$this->latestPosts[$parent_obj->getId()]['obj']           = $post;
	}

	public function getPostsQuantity( $obj, $quantity = 0, $obj_parent=false )
	{
		if($c = $this->getChildsPosts($obj, $obj_parent))
		{
			$quantity  += $c->getSize();
		}
		if($obj->getIs_category() == 1 || $obj->getHave_subtopics() == 1)
		{			$modelData  = $this->getChildsTopics($obj->getId());
			if($modelData->getSize())
			{
				foreach($modelData as $val)
				{
					$quantity = $this->getPostsQuantity( $val, $quantity, $obj_parent );
				}
			}
		}
		return $quantity;
	}

	public function setEntity( $id, $obj, $not_set_user = false ) //TODO
	{
		$entityModel = Mage::getModel('forum/entity');
		$entityModel->setId( $this->getEntityId($id) )
					->setEntity_type($obj->getEntity_type())
					->setEntity_id($id);

		$entityModel->save();

		if($obj->getEntityUserId() && !$not_set_user)
		{
			$this->setUserForum($obj->getEntityUserId(), $obj);
		}
	}

	public function setUserForum($userId, $obj, $store_id = false)
	{
		if(!$this->getUserBySystemId($userId, $store_id))
		{
			if($store_id === false)$store_id  = Mage::app()->getStore()->getId();
			$userModel = Mage::getModel('forum/user');
			$userModel->setId( NULL )
					->setSystem_user_id($userId)
					->setFirst_post(now())
					->setSystem_user_name($obj->getUser_name())
					->setStore_id( $store_id )
					;
			$userModel->save();
		}
	}

	public function updateTotalPosts($obj)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$user_id = $obj->getSystem_user_id() == 10000000 ? 0 : $obj->getSystem_user_id();
		$c->getSelect()->where('system_user_id=?', $user_id);

		$all_posts = $c->getSize();
		$m = Mage::getModel('forum/user')->load($obj->getId());
		$m->setTotal_posts($all_posts);
		$m->save();
	}

	public function updateTotalPostsUser($system_user_id, $store_id = false)
	{
		if(!$system_user_id)
		{
			return;
		}
		$id = $this->getUserBySystemId($system_user_id, $store_id);
		$o  = Mage::getModel('forum/user')->load($id);
		if($o->getId())
		{
			$o->setTotal_posts(intval($o->getTotal_posts() + 1));
			$o->save();
		}
	}

	public function updateDateJoined($obj)
	{
		$c = Mage::getModel('forum/post')->getCollection();
		$c->setOrder('created_time', 'desc')
		->setPageSize(1);
		$user_id = $obj->getSystem_user_id() == 10000000 ? 0 : $obj->getSystem_user_id();
		$c->getSelect()->where('system_user_id=?', $user_id);
		if($c->getSize())
		{
			foreach($c as $val)
			{
				$date = $val->getCreated_time();
				$m    = Mage::getModel('forum/user')->load($obj->getId());
				$m->setFirst_post($date);
				$m->save();
				return;
			}
		}

	}

	public function getUserBySystemId($userId, $store_id = false)
	{
		if($store_id === false)$store_id = Mage::app()->getStore()->getId();
		$o = Mage::getModel('forum/user')->getCollection();
		$o->getSelect()->where('system_user_id=?', $userId)->where('store_id=?', $store_id);
		if($o->getSize())
		{
			foreach($o as $val)
			{
				return $val->getId();
			}
		}
	}

	public function getEntityId($id)
	{
		return 	Mage::getModel('forum/entity')->load($id, 'entity_id')->getId();
	}

	public function deleteEntity($id)
	{
		Mage::getModel('forum/entity')->setId( $this->getEntityId($id) )->delete();
	}

	public function updateEnitityView($id)
	{
		$all = 	Mage::getModel('forum/entity')->load($id, 'entity_id')->getTotal_views();
		$all++;
		$entityModel = Mage::getModel('forum/entity');
		$entityModel->setId( $this->getEntityId($id) )
					->setTotal_views($all);

		$entityModel->save();

	}


	public function getTotalViews($obj)
	{
		$id = $obj->getId();
		return 	Mage::getModel('forum/entity')->load($id, 'entity_id')->getTotal_views() ? Mage::getModel('forum/entity')->load($id, 'entity_id')->getTotal_views() : 0;
	}

	public function getForumUrl($base_url = '/')
	{
		return $base_url . 'forum';
	}

	public function isForumActive()
	{
		return $this->_isActive;
	}

	public function isModerator()
	{
		if(Mage::registry('current_customer'))
		{
			$customer = Mage::registry('current_customer');
		}
		else
		{
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		if(!$customer) return;
		$website_id       = $this->getWebsiteId();
		$moder_website_id = $this->getAllowedModerateWebsites($customer->getId());
		if($customer->getWebsite_id() == $website_id && $moder_website_id == $website_id)
		{
			return true;
		}
	}

	public function getAllowedModerateWebsites($_id)
	{
		$obj = Mage::getModel('forum/moderator')->load($_id, 'system_user_id');
		if($obj->getId())
		{
			return $obj->getUser_website_id();
		}
	}

	public function getWebsiteId()
	{
		$store = Mage::app()->getStore();
		return $store->getWebsite_id();
	}

	public function getStoreId()
	{
		$store = Mage::app()->getStore();
		return $store->getWebsite_id();
	}

	public function getModeratePosts()
	{
		return Mage::getStoreConfig('forum/forummoderation/moderateposts');
	}

	public function getModerateTopics()
	{
		return Mage::getStoreConfig('forum/forummoderation/moderatetopics');
	}

	public function getIsHaveSubtopics($id)
	{
		if($obj = Mage::getModel('forum/topic')->load($id))
		{
			return $obj->getHave_subtopics();
		}
	}


	public function getPagesLimits()
	{
		$l = Mage::getStoreConfig('forum/forumconfiguration/pageslimits');
		$l = $l ? $l : 0;
		return $this->pages_limits[$l];
	}

	public function getDisplayShortDescriptionField()
	{
		return Mage::getStoreConfig('forum/forumconfiguration/display_short_description');
	}

}