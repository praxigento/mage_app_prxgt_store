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


class AW_Kbase_ArticleController extends Mage_Core_Controller_Front_Action
{

    /*
     * Redirects to login page if guests are not allowed and the customer is not registered
     */
    protected function _checkRegistered()
    {
        if(!AW_Kbase_Helper_Data::checkIfGuestsAllowed())
        {
            Mage::getSingleton('customer/session')
                ->addError($this->__('Only registered customers may visit %s section. Please log in or register',
                        Mage::getStoreConfig('kbase/general/title')));

            $this->_redirect('customer/account/login');
            return;
        }
    }

    /*
     * Redirects to 404 page if module frontend is disabled
     */
    protected function _checkFrontendEnabled()
    {
        if( AW_Kbase_Helper_Data::isModuleOutputDisabled()
        || !AW_Kbase_Helper_Data::getFrontendEnabled()
        )   $this->_redirect('noroute');
    }

    /**
     * Redirects to home page
     */
    public function redirectHome() {
        $this->_checkFrontendEnabled();
        $this->_redirectUrl(Mage::getBaseUrl().AW_Kbase_Helper_Url::getModuleUrlKey().'/');
    }

    /*
     * Redirects to referer page
     * Invented since native _redirectReferer function fails for custom rewrited URLs
     */
    protected function _goBack()
    {
        if( (!$refererUrl = $this->getRequest()->getServer('HTTP_REFERER'))
        ||  false === strpos($refererUrl, 'http')
        ||  0 !== strpos($refererUrl, AW_Kbase_Helper_Url::getUrl(AW_Kbase_Helper_Url::URL_TYPE_MAIN))
        )   $refererUrl = Mage::app()->getStore()->getBaseUrl();

        $this->getResponse()->setRedirect($refererUrl);

        return $this;
    }

    public function indexAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();
        $this->loadLayout();

        if($head = $this->getLayout()->getBlock('head'))
            $head->setTitle(Mage::getStoreConfig('kbase/general/title'));

        $this->renderLayout();
    }

    /*
     * Article layout composing
     */
    public function articleAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        if(AW_Kbase_Helper_Data::isModuleOutputDisabled())
        {
            $this->redirectReferer();
            return;
        }

        try
        {
            if(!($articleId = $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_ARTICLE_ID))
            && !($articleUrlKey = ($key = AW_Kbase_Helper_Url::getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_ARTICLE))
                    ? $key
                    : $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_ARTICLE)
                )
            )  Mage::throwException($this->__('No article specified'));
            
            if(!$articleId)
            {
                $article = Mage::getModel('kbase/article')->loadByUrlKey($articleUrlKey);
                if(!$article->getData())
                    Mage::throwException($this->__('Article "%s" does not exist', $articleUrlKey));
            }
            else $article = Mage::getModel('kbase/article')->load($articleId);

            $article->addCategoryInfo();
            $article->addAuthorInfo();

            if(($categoryId = $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_CATEGORY_ID))
            || ($categoryUrlKey = ($key = AW_Kbase_Helper_Url::getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_CATEGORY))
                    ? $key
                    : $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_CATEGORY)
                )
            ) {
                if(!$categoryId)
                {
                    $category = Mage::getModel('kbase/category')->loadByUrlKey($categoryUrlKey);

                    if(!$categoryId = $category->getId()) {
                        if($article->getData()) {
                            return $this->_redirectUrl(Mage::helper('kbase/url')->getHomeUrl().$article->getData('article_url_key').Mage::helper('kbase/url')->getUrlKeySuffix());
                        } else {
                            Mage::throwException($this->__('Category "%s" does not exist', $categoryUrlKey));
                        }
                    }
                }
                else $category = Mage::getModel('kbase/category')->load($categoryId);
            }
            else {
                $categoryId = null;
                $category = null;
            }

            if(!$article->getArticleStatus())
            {
                Mage::getSingleton('core/session')->addError($this->__('The article is temporarily disabled'));
                $this->redirectHome();
                return;
            }

            $this
                ->loadLayout()
                ->getLayout()
                    ->getBlock('kbase_article')
                        ->setArticle($article)
                        ->setCategoryId($articleId)
                        ->setCategory($category)
                        ->setCategoryId($categoryId)
                        ->preparePage();

            $this->renderLayout();
        }
        catch (Exception $e)
        {
//            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->redirectHome();
            return;
        }
    }

    /*
     * Articles of category given
     */
    public function categoryAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        try
        {
            if(!($categoryId = $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_CATEGORY_ID))
            && !($categoryUrlKey = AW_Kbase_Helper_Url::getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_CATEGORY))
            )   Mage::throwException($this->__('No category specified'));

            if(!$categoryId)
            {
                $category = Mage::getModel('kbase/category')->loadByUrlKey($categoryUrlKey);
                
                if(!$categoryId = $category->getId())
                    Mage::throwException($this->__('Category "%s" does not exist', $categoryUrlKey));
            }
            else $category = Mage::getModel('kbase/category')->load($categoryId);
            if ($category->getCategoryStatus() == AW_Kbase_Model_Source_Status::STATUS_DISABLED) Mage::throwException($this->__('Category "%s" does not exist', $categoryUrlKey));

            $this
                ->loadLayout()
                ->getLayout()
                    ->getBlock('kbase_category')
                        ->setCategory($category)
                        ->setCategoryId($categoryId)
                        ->preparePage();

            $this->renderLayout();
        }
        catch (Exception $e)
        {
//            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->redirectHome();
            return;
        }
    }

    /*
     * Articles by tag
     */
    public function tagAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        try
        {
            if(!$tag = AW_Kbase_Helper_Url::getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_TAG))
                Mage::throwException($this->__('No tag specified'));

            $this
                ->loadLayout()
                ->getLayout()
                    ->getBlock('kbase_tag')
                        ->setTag(urldecode($tag))
                        ->preparePage();

            $this->renderLayout();
        }
        catch (Exception $e)
        {
//            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->redirectHome();
            return;
        }
    }

    /*
     * Articles containing the words given
     */
    public function searchAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        if(!$query = ($key = AW_Kbase_Helper_Url::getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_QUERY))
                    ? $key
                    : $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_QUERY)
        ) {
            $this->redirectHome();
            return;
        }
        $this
            ->loadLayout()
            ->getLayout()
                ->getBlock('kbase_search')
                    ->setQuery($query)
                    ->preparePage();

        $this->renderLayout();
    }

    /*
     * Article attachment download
     */
    public function attachmentAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        if(!$articleId = $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_ARTICLE_ID))
        {
            Mage::getSingleton('core/session')->addError($this->__('No article specified'));
            $this->redirectHome();
            return;
        }
        $fileName = AW_Kbase_Helper_Data::getAttachmentFilename($articleId);
        AW_Kbase_Helper_Data::downloadFile($fileName);
    }

    /*
     * Article rating vote
     */
    public function voteAction()
    {
        $this->_checkRegistered();
        $this->_checkFrontendEnabled();

        try
        {
            $session = Mage::getSingleton('core/session');
            $articleId = $this->getRequest()->getParam(AW_Kbase_Helper_Url::URL_PARAM_NAME_ARTICLE_ID);

            if(!AW_Kbase_Helper_Data::isArticleVoteAllowed($articleId))
            {
                if(AW_Kbase_Helper_Data::VOTINGS_PER_DAY <= (int) Mage::app()->getRequest()->getCookie(AW_Kbase_Helper_Data::COOKIE_ARTICLES_VOTED_COUNT))
                    $session->addNotice($this->__('You may vote only %s times per day!', AW_Kbase_Helper_Data::VOTINGS_PER_DAY));
                else
                    $session->addNotice($this->__('You have already voted on this article!'));

                $this->_goBack();
                return;
            }
            else
            {
                $rating = $this->getRequest()->getParam('rating');
                if($rating > 5 || $rating < 1) throw new Exception($this->__('Wrong rating'));

                Mage::getModel('kbase/article')->setArticleId($articleId)->vote($rating);

                if($articlesVoted = Mage::app()->getRequest()->getCookie(AW_Kbase_Helper_Data::COOKIE_ARTICLES_VOTED_IDS))
                    $articlesVoted .= ','.$articleId;
                else
                    $articlesVoted = $articleId;

                setcookie(
                        AW_Kbase_Helper_Data::COOKIE_ARTICLES_VOTED_IDS,
                        $articlesVoted,
                        time()+60*60*24*30,
                        '/'
                    );

                $articlesVotedCount = (int)Mage::app()->getRequest()->getCookie(AW_Kbase_Helper_Data::COOKIE_ARTICLES_VOTED_COUNT);

                setcookie(
                        AW_Kbase_Helper_Data::COOKIE_ARTICLES_VOTED_COUNT,
                        $articlesVotedCount + 1,
                        time()+60*60*24,
                        '/'
                    );
            }
            $session->addSuccess($this->__('Your voice has been accepted. Thank you!'));
        }
        catch (Exception $e) {
//            Mage::logException($e);
            $session->addError($this->__('Unable to vote. Please, try again later'));
        }
        $this->_goBack();
    }

}
