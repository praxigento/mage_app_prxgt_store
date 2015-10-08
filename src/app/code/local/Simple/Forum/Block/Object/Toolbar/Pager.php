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

class Simple_Forum_Block_Object_Toolbar_Pager extends Mage_Page_Block_Html_Pager
{
    /**
     * Pages quantity per frame
     * @var int
     */
    protected $_frameLength = 5;

    /**
     * Next/previous page position relatively to the current frame
     * @var int
     */
    protected $_jump = 5;

    /**
     * Frame initialization flag
     * @var bool
     */
    protected $_frameInitialized = false;

    /**
     * Start page position in frame
     * @var int
     */
    protected $_frameStart;

    /**
     * Finish page position in frame
     * @var int
     */
    protected $_frameEnd;

    /**
     * Custom limit
     * @var int
     */
    protected $_limit;

    /**
     * Define default template and settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('forum/topics/toolbar/pager.phtml');
    }

    /**
     * Getter for $_frameStart
     *
     * @return int
     */
    public function getFrameStart()
    {
        $this->_initFrame();
        return $this->_frameStart;
    }

    /**
     * Getter for $_frameEnd
     *
     * @return int
     */
    public function getFrameEnd()
    {
        $this->_initFrame();
        return $this->_frameEnd;
    }

    /**
     * Return array of pages in frame
     *
     * @return array
     */
    public function getFramePages()
    {
        $start = $this->getFrameStart();
        $end = $this->getFrameEnd();
        return range($start, $end);
    }

    /**
     * Return page number of Previous jump
     *
     * @return int
     */
    public function getPreviousJumpPage()
    {
        if (!$this->getJump()) {
            return null;
        }
        $frameStart = $this->getFrameStart();
        if ($frameStart - 1 > 1) {
            return max(2, $frameStart - $this->getJump());
        }

        return null;
    }

    /**
     * Prepare URL for Previous Jump
     *
     * @return string
     */
    public function getPreviousJumpUrl()
    {
        return $this->getPageUrl($this->getPreviousJumpPage());
    }

    /**
     * Return page number of Next jump
     *
     * @return int
     */
    public function getNextJumpPage()
    {
        if (!$this->getJump()) {
            return null;
        }
        $frameEnd = $this->getFrameEnd();
        if ($this->getLastPageNum() - $frameEnd > 1) {
            return min($this->getLastPageNum() - 1, $frameEnd + $this->getJump());
        }

        return null;
    }

    /**
     * Prepare URL for Next Jump
     *
     * @return string
     */
    public function getNextJumpUrl()
    {
        return $this->getPageUrl($this->getNextJumpPage());
    }

    /**
     * Getter for $_frameLength
     *
     * @return int
     */
    public function getFrameLength()
    {
        return $this->_frameLength;
    }

    /**
     * Getter for $_jump
     *
     * @return int
     */
    public function getJump()
    {
        return $this->_jump;
    }

    /**
     * Setter for $_frameLength
     *
     * @param int $frame
     */
    public function setFrameLength($frame)
    {
        $frame = abs(intval($frame));
        if ($this->getFrameLength() != $frame) {
            $this->_setFrameInitialized(false);
            $this->_frameLength = $frame;
        }

        return $this;
    }

    /**
     * Setter for $_jump
     *
     * @param int $jump
     */
    public function setJump($jump)
    {
        $jump = abs(intval($jump));
        if ($this->getJump() != $jump) {
            $this->_setFrameInitialized(false);
            $this->_jump = $jump;
        }

        return $this;
    }

    /**
     * Setter for $_limit
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->_limit = abs(intval($limit));
        return $this;
    }

    /**
     * Return pager limitation from request or as assigned value
     *
     * @return int
     */
    public function getLimit()
    {
        if ($this->_limit !== null) {
            return $this->_limit;
        }
        return parent::getLimit();
    }

    /**
     * Custom setter for pager collection
     *
     * @param Varien_Data_Collection
     */
    public function setCollection($collection)
    {
      //  parent::setCollection($collection);
      	$this->_collection = $collection;
        $this->_setFrameInitialized(false);
        return $this;
    }

    /**
     * Whether to show first page in pagination or not
     *
     * @return bool
     */
    public function canShowFirst()
    {
        return $this->getJump() > 1 && $this->getFrameStart() > 1;
    }

    /**
     * Whether to show last page in pagination or not
     *
     * @return bool
     */
    public function canShowLast()
    {
        return $this->getJump() > 1 && $this->getFrameEnd() < $this->getLastPageNum();
    }

    /**
     * Whether to show link to Previous Jump
     *
     * @return bool
     */
    public function canShowPreviousJump()
    {
        return $this->getPreviousJumpPage() !== null;
    }

    /**
     * Whether to show link to Next Jump
     *
     * @return bool
     */
    public function canShowNextJump()
    {
        return $this->getNextJumpPage() !== null;
    }

    /**
     * Initialize frame data, such as frame start, frame start etc.
     *
     */
    protected function _initFrame()
    {
        if (!$this->isFrameInitialized()) {
            $start = 0;
            $end = 0;
            $collection = $this->getCollection();
            if($collection->last_page_number_tmp && $collection->last_page_number_tmp != 0)
            {
            	$last_page_number = $collection->getLastPageNumberTMP();
            }
            else
            {                $last_page_number = $collection->getLastPageNumber();
            }
            if ($last_page_number <= $this->getFrameLength())
            {
                $start = 1;
                $end = $last_page_number;
            }
            else {
                $half = ceil($this->getFrameLength() / 2);
                if ($collection->getCurPage() >= $half && $collection->getCurPage() <= $last_page_number - $half) {
                    $start  = ($collection->getCurPage() - $half) + 1;
                    $end = ($start + $this->getFrameLength()) - 1;
                }
                elseif ($collection->getCurPage() < $half) {
                    $start  = 1;
                    $end = $this->getFrameLength();
                }
                elseif ($collection->getCurPage() > ($last_page_number - $half)) {
                    $end = $last_page_number;
                    $start  = $end - $this->getFrameLength() + 1;
                }
            }

            $this->_frameStart = $start;
            $this->_frameEnd = $end;

            $this->_setFrameInitialized(true);
        }

        return $this;
    }

    /**
     * Setter for flag _frameInitialized
     *
     * @param bool $flag
     */
    protected function _setFrameInitialized($flag)
    {
        $this->_frameInitialized = (bool)$flag;
        return $this;
    }

    /**
     * Check if frame data was initialized
     *
     */
    public function isFrameInitialized()
    {
        return $this->_frameInitialized;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getLastPageNum() > 1) {
            return parent::_toHtml();
        } else {
            return '&nbsp;';
        }
    }

    protected function getLastPageNumTMP()
    {
    	$collection = $this->getCollection();    	if($collection->last_page_number_tmp && $collection->last_page_number_tmp != 0)
        {
        	$last_page_number = $collection->getLastPageNumberTMP();
        }
        else
        {
            $last_page_number = $collection->getLastPageNumber();
        }
        return $last_page_number;
    }
}

?>