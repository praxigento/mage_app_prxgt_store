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
 * URL-related stuff
 */
class AW_Kbase_Helper_Url extends Mage_Core_Helper_Abstract
{
    /*
     * URL types for passing to
     * @see AW_Kbase_Helper_Url::getUrl()
     */
    const URL_TYPE_MAIN     = 1;
    const URL_TYPE_ARTICLE  = 2;
    const URL_TYPE_CATEGORY = 3;
    const URL_TYPE_TAG      = 4;
    const URL_TYPE_SEARCH   = 5;

    /*
     * Query parameter names
     */
    const URL_PARAM_NAME_ARTICLE        = 'article';
    const URL_PARAM_NAME_ARTICLE_ID     = 'id';
    const URL_PARAM_NAME_CATEGORY       = 'category';
    const URL_PARAM_NAME_CATEGORY_ID    = 'cat';
    const URL_PARAM_NAME_TAG            = 'tag';
    const URL_PARAM_NAME_QUERY          = 'query';

    /*
     * Sorter query parameter names
     */
    const URL_PARAM_NAME_SORT           = 'sort';
    const URL_PARAM_NAME_SORT_DIR       = 'dir';

    /*
     * Pager query parameter names
     */
    const URL_PARAM_NAME_PAGE           = 'page';
    const URL_PARAM_NAME_LIMIT          = 'limit';

    /*
     * @var string URL key of the extension ('kbase' by default)
     */
    protected static $_moduleUrlKey = null;

    /*
     * @var bool Whether to use rewrited or canonical URLs
     */
    protected static $_useUrlRewrites = null;

    /*
     * @var string URL key suffix (usually .html)
     */
    protected static $_urlKeySuffix = null;

    /*
     * @var array Parsed URL parameters
     */
    protected static $_queryParams = null;

    /*
     * @var array Characters to be URL-encoded
     */
    protected static $_URL_ENCODED_CHARS = array(
            ' ', '+', '(', ')', ';', ':', '@', '&', '`', '\'',
            '=', '!', '$', ',', '/', '?', '#', '[', ']', '%',
        );

    /*
     * Returns URL parameter
     * @param string $name Paramtere name
     * @return string Paramter value
     */
    public static function getParam($name)
    {
        return (is_null(self::$_queryParams) || !isset(self::$_queryParams[$name]))
            ? false
            : urldecode(self::$_queryParams[$name]);
    }
    
    protected static function getModuleUrlKeyFromConfig($store = null) {
        return Mage::getStoreConfig('kbase/general/url_key', $store);
    }

    /*
     * Returns module root URL key
     * @return string Root URL key
     */
    public static function getModuleUrlKey() {
        if(!is_null(self::$_moduleUrlKey))
            return self::$_moduleUrlKey;
        $route = self::getModuleUrlKeyFromConfig();
        if(!$route) $route = 'kbase';
        self::$_moduleUrlKey = $route;
        return $route;
    }

    /*
     * Removes parameter name and its value from source string assuming parameters are path-encoded like /name1/value1/name2/value2/
     * @param string @source A string containing /paramName/paramValue/ pair
     * @param string $paramName Parameter name
     * @return string Source without name-value pair of parameter
     */
    public static function unsetParam($source, $paramName)
    {
        if(false !== $paramStartPos = strpos($source, '/'.$paramName.'/'))
            $source = substr($source, 0, $paramStartPos).substr($source, strpos($source, '/', $paramStartPos + strlen($paramName) + 2));
        return $source;
	}

    /*
     * Removes parameter name and its value from source string assuming parameters query-encoded like ?name1=value1&name2=value2
     * @param string @source A string containing paramName=paramValue pair
     * @param string $paramName Parameter name
     * @return string Source without name-value parameter pair
     */
    public static function unsetQueryParam($source, $paramName)
    {
        if(!$qmark = strpos($source, '?')) return $source;

        $params = explode('&', substr($source, $qmark + 1));

        foreach($params as $index => $param)
            if(substr($param, 0, strlen($paramName)+1) == $paramName.'=')
            {
                unset($params[$index]);
                break;
            }

        $params = count($params)
            ? '?'.implode('&', $params)
            : '';

        return substr($source, 0, $qmark).$params;
	}

    /*
     * Returns parameter value from source string assuming parameters query-encoded like ?name1=value1&name2=value2
     * @param string @source A string containing paramName=paramValue pair
     * @param string $paramName Parameter name
     * @return string|null Source without name-value parameter pair or null if not found
     */
    public static function getQueryParam($source, $paramName)
    {
        if(!$qmark = strpos($source, '?')) return null;

        $params = explode('&', substr($source, $qmark + 1));

        foreach($params as $index => $param)
            if(substr($param, 0, strlen($paramName)+1) == $paramName.'=')
                return substr($param, strlen($paramName)+1);

        return null;
	}

    /*
     * Retrieves parameter value from source
     * @param string @source A string containing /paramName/paramValue/ pair
     * @param string $paramName Parameter name
     * @return string|null Parameter value or NULL if parameter was not found
     */
	public static function extractParam($source, $paramName)
    {
        if(false !== $paramStartPos = strpos($source, '/'.$paramName.'/'))
            return substr($source,
                        $paramValuePos = $paramStartPos + strlen($paramName) + 2,
                        strpos($source, '/', $paramValuePos) - $paramValuePos);
        return null;
	}

    /*
     * Reads from config and caches the setting to use rewrited URLs
     * @return bool
     */
    public static function useUrlRewrites()
    {
        if(is_null(self::$_useUrlRewrites))
            self::$_useUrlRewrites = true;

        return self::$_useUrlRewrites;
    }
    
    public static function getUrlKeySuffixFromConfig($store = null) {
        return Mage::getStoreConfig('kbase/url_rewrite/url_suffix', $store);
    }

    /*
     * Reads from config and caches the setting of URL key article suffix
     * @return string
     */
    public static function getUrlKeySuffix() {
        if(is_null(self::$_urlKeySuffix)) {
            $urlSuffix = self::getUrlKeySuffixFromConfig();
            if($urlSuffix && !preg_match('/^[a-z0-9_.-]*$/', $urlSuffix)) {
                $urlSuffix = preg_replace('/[^a-z0-9_.-]/', '', $urlSuffix);
            }
            self::$_urlKeySuffix = $urlSuffix;
        }
        return self::$_urlKeySuffix;
    }

    /*
     * Returns Knowledge Base home URL
     * @return string Knowledge Base home URL
     */
    public static function getHomeUrl()
    {
        return self::getUrl(self::URL_TYPE_MAIN);
    }

    /*
     * Changes URL protocol according to current secure setting
     * @param string $url Source URL
     * @return string Processed URL
     */
    public static function secureUrl($url, $store = null) {
        if(Mage::helper('kbase/config')->getUrlRewriteUseSecureUrls($store))
            $url = str_replace('http://', 'https://', $url);

        return $url;
    }

    /*
     * Converts article title to URL key according to URL standard
     * @param string @name Article title
     * @return string URL key
     */
    public static function nameToUrlKey($name)
    {
        $name = trim($name);

        $name = str_replace(self::$_URL_ENCODED_CHARS, '_', $name);

        do {
            $name = $newStr = str_replace('__', '_', $name, $count);
        } while($count);

        return $name;
    }

    /*
     * Performs additional parameter encoding if needed
     * @deprecated It's not necessary since URLs are UTF-8-encoded
     * @param string $str A string to encode
     * @return string Encoded string
     */
    public static function encode($str)
    {
        return $str;
    }

    /*
     * Encodes a string by replacing non-URL-allowed chars with their urlencoded representation
     * The trick is that the '%' sign is placed last in the $_URL_ENCODED_CHARS,
     * thus replacing all '%'s with '%25's and allowing the URL to be correct
     * @param string $str A string to encode
     * @return string Encoded string
     */
    public static function encodeSpecialChars($str)
    {
        $chars = self::$_URL_ENCODED_CHARS;
        unset($chars[0]);

        foreach($chars as $char)
            $str = str_replace($char, urlencode($char), $str);

        return $str;
    }

    /*
     * Returns Knowledge Base URL according to current config settings
     * @param int $type URL type
     * @param array $data Data used to construct result URL
     * @param array $category Optional data to construct category+article - like URLs
     * @return string Rewrited or canonical Knowledge Base URL
     */
    public static function getUrl($type=self::URL_TYPE_MAIN, $data = array(), $category = false)
    {
        $baseUrl = self::secureUrl(Mage::getBaseUrl()
                        . self::getModuleUrlKey()
                        . '/'
                    );

        if(is_object($data))
            $data = $data->getData();

        switch($type)
        {
            case self::URL_TYPE_MAIN :
                return self::useUrlRewrites()
                    ? $baseUrl
                    : Mage::getUrl(self::getModuleUrlKey().'/article/index');
                break;

            case self::URL_TYPE_ARTICLE :
                return is_array($category)
                    ? ( self::useUrlRewrites()
                        ? $baseUrl.self::encode($category['category_url_key']).'/'.self::encode($data['article_url_key']).self::getUrlKeySuffix()
                        : Mage::getUrl(self::getModuleUrlKey().'/article/article', array(
                            self::URL_PARAM_NAME_CATEGORY_ID => $data['category_id'],
                            self::URL_PARAM_NAME_ARTICLE_ID => $data['article_id'])
                          )
                      )
                    : ( self::useUrlRewrites()
                        ? $baseUrl.self::encode($data['article_url_key']).self::getUrlKeySuffix()
                        : Mage::getUrl(self::getModuleUrlKey().'/article/article', array(self::URL_PARAM_NAME_ARTICLE_ID => $data['article_id']))
                      );
                break;

            case self::URL_TYPE_CATEGORY :
                return self::useUrlRewrites()
                    ? $baseUrl.self::encode($data['category_url_key']).'/'
                    : Mage::getUrl(self::getModuleUrlKey().'/article/category', array(self::URL_PARAM_NAME_CATEGORY_ID => $data['category_id']));
                break;

            case self::URL_TYPE_TAG :
                return self::useUrlRewrites()
                    ? $baseUrl.'tag/'.self::encode($data['tag_name']).'/'
                    : Mage::getUrl(self::getModuleUrlKey().'/article/tag', array(self::URL_PARAM_NAME_TAG => $data['tag_name']));
                break;

            case self::URL_TYPE_SEARCH :
                return self::useUrlRewrites()
                    ? $baseUrl.'search/'.self::encodeUrlParams($data)
                    : Mage::getUrl(self::getModuleUrlKey().'/article/search', $data);
                break;

            default:
                return Mage::getBaseUrl();
        }
    }

    /*
     * Encodes associative array to URL-like string
     * @param array $data A data to be encoded
     * @param string $separator Delimiter to join data keys and values
     * @return string Encoded data
     */
    public static function encodeUrlParams($data, $separator = '/')
    {
        if(!is_array($data)) return $data;

        $res = '';
        foreach($data as $k => $v)
            if($v) $res .= $k.$separator.$v.$separator;

        return $res;
    }

    /*
     * Returns an URL with parameters given encoded
     * @param array $params Parameters
     * @return string URL with new parameters encoded
     */
    public static function getUrlWithParams($params=array())
    {        
        $requestUri = preg_replace("#\?.+$#","",Mage::app()->getRequest()->getRequestUri());
         
        foreach($params as $key => $value) {
            $requestUri = self::unsetParam($requestUri, $key).$key.'/'.$value.'/';
        }
        
        if($pos = strpos($requestUri, '/'.self::getModuleUrlKey())) {
            $requestUri = substr($requestUri, $pos);
        }
        
        
        return Mage::getUrl('').ltrim($requestUri, '/');
    }

    /*
     * Caches URL rewrite
     * @todo cache search requests
     * @param string $from Initial URL
     * @param string $to Rewritten URL
     * @return bool Always true
     */
    public static function cacheRewrite($from, $to)
    {
        if(Mage::getStoreConfig('kbase/url_rewrite/cache_requests'))
        {
            $from = urldecode(trim($from, '/'));
            try
            {
                Mage::getModel('core/url_rewrite')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->setUrlRewriteId(null)
                    ->setRequestPath($from)
                    ->setTargetPath($to)
                    ->setIdPath($from)
                    ->setIsSystem(0)
                    ->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return true;
    }

    /*
     * Checks whether the request given matches module URLs
     * @param Zend_Controller_Request_Http $request Request to match
     * @return bool Whether the request given matches module URLs
     */
    public static function matchUrl(Zend_Controller_Request_Http $request)
    {
        if( AW_Kbase_Helper_Data::isModuleOutputDisabled()
        || !AW_Kbase_Helper_Data::getFrontendEnabled()
        )   return false;

        $urlKey = self::getModuleUrlKey();
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();
        
        if($request->getParam('___from_store')) {
            $_fromStore = Mage::app()->getSafeStore($request->getParam('___from_store'));
            if($_fromStore->getData()) {
                $_oldUrlKey = self::getModuleUrlKeyFromConfig($_fromStore);
                if(strpos($pathInfo, '/'.$_oldUrlKey) === 0 && $_oldUrlKey != $urlKey) {
                    $_newUrl = self::secureUrl(Mage::getBaseUrl().$urlKey.'/');
                    $response = Mage::app()->getResponse();
                    $response->setRedirect($_newUrl);
                    $response->sendHeaders();
                    $request->setDispatched(true);
                    return true;
                }
            }
        }

        if(0 !== strpos($pathInfo, '/'.$urlKey.'/')) // if path does not begin with /kbase/
            return false;

        $pathInfo = substr($pathInfo, strlen($urlKey)+2);

        $request
            ->setModuleName('kbase')
            ->setControllerName('article');

        if(!strlen($pathInfo)) // no parameters passed, go to KB start page
        {
            $request->setActionName('index');
            return  AW_Kbase_Helper_Data::getFrontendEnabled()
                &&  self::cacheRewrite($request->getPathInfo(), 'kbase/article/index');
        }

        $urlKeySuffix = self::getUrlKeySuffix();
        $isArticle = false;
        if(!$urlKeySuffix) {
            $_path = explode('/', $pathInfo);
            $_aUrl = false;
            if(count($_path) == 1)
                $_aUrl = $_path[0];
            if(count($_path) == 2)
                $_aUrl = $_path[1];
            if($_aUrl) {
                $_aUrl = urldecode($_aUrl);
                $_aId = Mage::getModel('kbase/article')->getResource()->getIdByUrlKey($_aUrl);
                if(!$_aId) $_aId = Mage::getModel('kbase/article')->getResource()->getIdByUrlKey($_aUrl, true);
                if($_aId) $isArticle = true;
            }
        }
        
        /**
         * Checking is customer comes from other store view
         * having various article suffix id in comparison
         * with current
         */
        if($request->getParam('___from_store') && !$isArticle) {
            if(!isset($_fromStore))
                $_fromStore = Mage::app()->getSafeStore($request->getParam('___from_store'));
            if($_fromStore->getData()) {
                $_oldUrlSuffix = self::getUrlKeySuffixFromConfig($_fromStore);
                if($urlKeySuffix != $_oldUrlSuffix) {
                    $_path = explode('/', $pathInfo);
                    $_aUrl = false;
                    if(count($_path) == 1)
                        $_aUrl = $_path[0];
                    if(count($_path) == 2)
                        $_aUrl = $_path[1];
                    if($_aUrl) {
                        $_articleUrlKey = substr($_aUrl, 0, strpos($_aUrl, $_oldUrlSuffix));
                        $_aId = Mage::getModel('kbase/article')->getResource()->getIdByUrlKey($_articleUrlKey);
                        if(!$_aId) $_aId = Mage::getModel('kbase/article')->getResource()->getIdByUrlKey($_articleUrlKey, true);
                        if($_aId) {
                            $_storeIds = Mage::getModel('kbase/article')->getResource()->getArticleStoreIds($_aId);
                            if(in_array(Mage::app()->getStore()->getId(), $_storeIds)) {
                                $_newUrl = self::secureUrl(Mage::getBaseUrl().$urlKey.'/'.$_articleUrlKey.$urlKeySuffix);
                                $response = Mage::app()->getResponse();
                                $response->setRedirect($_newUrl);
                                $response->sendHeaders();
                                $request->setDispatched(true);
                                return true;
                            }
                        }
                    }
                }
            }
        }

        if(($urlKeySuffix                       // if suffix is set
            &&  '/' != $urlKeySuffix            // and it is not equal to '/'
//            &&  '/' != substr($pathInfo, -1)    // and path does not end with '/'
            &&  $urlKeySuffix == substr($pathInfo, -strlen($urlKeySuffix)) || $isArticle)  // and path ends with suffix
        ) {
            $pathInfo = substr($pathInfo, 0, strlen($pathInfo)-strlen($urlKeySuffix));
            $path = explode('/', $pathInfo);

            if(count($path) == 1)
            {
                $request->setActionName('article');

                self::$_queryParams[self::URL_PARAM_NAME_ARTICLE] = $path[0];
                return self::cacheRewrite($request->getPathInfo(), 'kbase/article/article/'.self::URL_PARAM_NAME_ARTICLE.'/'.$path[0]);
            }
            elseif(count($path) == 2)
            {
                $request->setActionName('article');

                self::$_queryParams[self::URL_PARAM_NAME_CATEGORY] = $path[0];
                self::$_queryParams[self::URL_PARAM_NAME_ARTICLE] = $path[1];
                return self::cacheRewrite($request->getPathInfo(), 'kbase/article/article/'.self::URL_PARAM_NAME_CATEGORY.'/'.$path[0].'/'.self::URL_PARAM_NAME_ARTICLE.'/'.$path[1]);
            }
            return false;
        }
        // since further there will be listing pages only, we should check the condition here
        if(!Mage::getStoreConfig('kbase/general/frontent_enabled')) return false;

        // init parameters with optional ones
        self::$_queryParams = array(
            self::URL_PARAM_NAME_LIMIT      => false,
            self::URL_PARAM_NAME_PAGE       => false,
            self::URL_PARAM_NAME_SORT       => false,
            self::URL_PARAM_NAME_SORT_DIR   => false,
            self::URL_PARAM_NAME_QUERY      => false,
        );

        $pathInfo = substr($pathInfo, 0, strlen($pathInfo) - 1);
        $path = explode('/', $pathInfo);
        for($i = count($path)-1; $i > 0; $i -= 2)
        {
            $paramName = $path[$i-1];
            if(array_key_exists($paramName, self::$_queryParams))
            {
//                for(; $path[$i] != $decoded = urldecode($path[$i]); $path[$i] = $decoded);
//                self::$_queryParams[$paramName] = $path[$i];
                self::$_queryParams[$paramName] = urldecode($path[$i]);
                unset($path[$i]);
                unset($path[$i-1]);
            }
        }
        
        foreach(self::$_queryParams as $paramName => $value)
        {
            if($value = self::getQueryParam($requestUri, $paramName))
            {
                self::$_queryParams[$paramName] = urldecode($value);
                $requestUri = self::unsetQueryParam($requestUri, $paramName);
            }
        }

        if(1 == count($path))
        {
            if('search' == $path[0])
            {
                 // if there were known parameters passed as query params
                if($requestUri != $request->getRequestUri())
                {
                    foreach(self::$_queryParams as $name => $value)
                        if($value)
                            self::$_queryParams[$name] = self::encodeSpecialChars($value);

                    Mage::app()->getFrontController()->getResponse()
                        ->setRedirect(self::getUrl(self::URL_TYPE_SEARCH, self::$_queryParams))
                        ->sendResponse();
                    exit;
                }
                $request->setActionName('search');
                return true;
            }
            $request->setActionName('category');
            self::$_queryParams[self::URL_PARAM_NAME_CATEGORY] = $path[0];
            return true;
        }
        elseif( 2 == count($path)
            &&  'tag' == $path[0]
            &&  $path[1]
        ) {
            $request->setActionName('tag');
            self::$_queryParams[self::URL_PARAM_NAME_TAG] = $path[1];
            return true;
        }
        return false;
    }
}
