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
 * Non-URL-related stuff
 */
class AW_Kbase_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Directory name where attachments recide
     */
    const ATTACHMENT_DIR = 'kbase';

    /*
     * Cookie parameter containing IDs of articles voted last day
     */
    const COOKIE_ARTICLES_VOTED_IDS     = 'kbase-articles-voted-ids';

    /*
     * Cookie parameter containing quantity of votes given last day
     */
    const COOKIE_ARTICLES_VOTED_COUNT   = 'kbase-articles-voted-count';

    /*
     * Votings per day limit
     */
    const VOTINGS_PER_DAY = 10;

    /*
     * Number of decimal signs of rating displayed on the frontend
     */
    const RATING_DECIMALS = 1;

    /*
     * @var bool Stores the appropriate setting from config
     */
    protected static $_frontendEnabled = null;

    /*
     * Returns whether module frontend is enabled
     * @return string Root URL key
     */
	public static function getFrontendEnabled()
    {
        if(null === self::$_frontendEnabled)
            self::$_frontendEnabled = Mage::getStoreConfig('kbase/general/frontent_enabled');

        return self::$_frontendEnabled;
	}

    /*
     * Returns a path to article attachment directory
     * @param int $articleId Article ID
     * @return string Path to article attachment directory
     */
    public static function attachmentDirName($articleId)
    {
        return Mage::getBaseDir('media').DS.self::ATTACHMENT_DIR.DS.$articleId;
    }

    /*
     * Returns full path to article attachment file
     * @param int $articleId Article ID
     * @return string Path to article attachment file
     */
    public static function getAttachmentFilename($articleId)
    {
        $fileName =     self::attachmentDirName($articleId)
                    .   DS
                    .   Mage::getModel('kbase/article')->load($articleId)->getArticleAttachment();

        return $fileName;
    }

    /*
     * Removes directory including its content
     * @param string $dir Directory name
     */
    public static function removeDirectory($dir)
    {
        if(!is_dir($dir)) return @unlink($dir);

        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..') continue;
            if(!self::removeDirectory($dir.DS.$item)) return false;
        }
        return @rmdir($dir);
    }

    /*
     * Downloads file to client
     * @param string $filename The name of the file to download
     * @param $content The contents downloaded
     */
    public static function downloadFile($filename, $content=null)
    {
        if(is_null($content))
        {
            if($handle = @fopen($filename, "r"))
            {
                $content = @fread($handle, filesize($filename));
                @fclose($handle);
            }
        }

        $contentType='application/octet-stream';

        $response = Mage::app()->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', "attachment; filename=\"".basename($filename)."\"");
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    /*
     * Returns whether module output is disabled from Advanced->Disable module output in Admin Config section
     * @return bool
     */
    public static function isModuleOutputDisabled()
    {
        return (bool) Mage::getStoreConfig('advanced/modules_disable_output/AW_Kbase');
    }

    /*
     * Checks whether registration is needed and returns TRUE if current customer is allowed and FALSE if registration is needed
     * @return bool Check result
     */
    public static function checkIfGuestsAllowed()
    {
        return     !Mage::getStoreConfig('kbase/general/registered_customers_only')
                ||  Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /*
     * Removes not-necessary stuff from the phrase given and splits it to array
     * @param string $phrase A phrase to prepare
     * @return array Prepared phrase
     */
    public static function preparePhrase($phrase)
    {
        $phrase = strtolower(trim(str_replace(',', ' ', $phrase)));

        while(false !== strpos($phrase, '  '))
            $phrase = str_replace('  ', ' ', $phrase);

        if($phrase) return explode(' ', $phrase);
        return array();
    }

    /*
     * Parses search query and returns array of words and exact phrases enclosed in double quotes (")
     * @param string $query A query to be parsed
     * @return array Parsed query
     */
    public static function parseQuery($query) {
       
        $keywords = preg_split("/[\s]+/", $query, -1, PREG_SPLIT_NO_EMPTY);
         
         
        return $keywords;
        
    } 

    /*
     * Checks whether voting for article is currently allowed
     * @param int $articleId The ID of the article
     * @return bool Check result
     */
    public static function isArticleVoteAllowed($articleId)
    {
        return  !in_array($articleId, explode(',', Mage::app()->getRequest()->getCookie(self::COOKIE_ARTICLES_VOTED_IDS)))
            &&   self::VOTINGS_PER_DAY > (int) Mage::app()->getRequest()->getCookie(self::COOKIE_ARTICLES_VOTED_COUNT);
    }

    /*
     * Returns true if current Magento version is 1.4 and higher
     * @return bool
     */
    public static function mageVersionIsAbove13()
    {
        $version = explode('.', Mage::getVersion());

        return $version[0] >= 1 && $version[1] >= 4;
    }

    /**
     * Insert array to table based on columns definition
     *
     * @param   string $table
     * @param   array $columns
     * @param   array $data
     * @return  int
     */
    public static function insertArray($table, array $columns, array $data)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_write');

        $vals = array();
        $bind = array();
        $columnsCount = count($columns);
        foreach ($data as $row) {
            if ($columnsCount != count($row)) {
                throw new Varien_Exception('Invalid data for insert');
            }
            $line = array();
            if ($columnsCount == 1) {
                if ($row instanceof Zend_Db_Expr) {
                    $line = $value->__toString();
                } else {
                    $line = '?';
                    $bind[] = $row;
                }
                $vals[] = sprintf('(%s)', $line);
            } else {
                foreach ($row as $value) {
                    if ($value instanceof Zend_Db_Expr) {
                        $line[] = $value->__toString();
                    }
                    else {
                        $line[] = '?';
                        $bind[] = $value;
                    }
                }
                $vals[] = sprintf('(%s)', join(',', $line));
            }
        }

        // build the statement
        $columns = array_map(array($read, 'quoteIdentifier'), $columns);
        $sql = sprintf("INSERT INTO %s (%s) VALUES%s",
            $read->quoteIdentifier($table, true),
            implode(',', $columns), implode(', ', $vals));

        // execute the statement and return the number of affected rows
        $stmt = $read->query($sql, $bind);
        $result = $stmt->rowCount();

        return $result;
    }

    /*
     * Recursively searches and replaces all occurrences of search in subject values replaced with the given replace value
     * @param string $search The value being searched for
     * @param string $replace The replacement value
     * @param array $subject Subject for being searched and replaced on
     * @return array Array with processed values
     */
    public static function recursiveReplace($search, $replace, $subject)
    {
        if(!is_array($subject))
            return $subject;

        foreach($subject as $key => $value)
            if(is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif(is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

    public function prepareTags($tags) {
        if(!is_array($tags))
            $tags = explode (',', $tags);
        for($i = 0; $i < count($tags); $i++)
            $tags[$i] = trim($tags[$i]);
        $tags = array_unique($tags);
        return $tags;
    }
}
