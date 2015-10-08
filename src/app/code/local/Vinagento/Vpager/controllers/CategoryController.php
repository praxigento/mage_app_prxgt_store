<?php
require_once BP.'/app/code/core/Mage/Catalog/controllers/CategoryController.php';
class Vinagento_Vpager_CategoryController extends Mage_Catalog_CategoryController{
	protected function _construct(){
		$request = $this->getRequest();
		if($params = $request->getParam('_escaped_fragment_')){
			$tmpParams = array();
			parse_str($params,$tmpParams);
		}
	}
	protected function _getListHtml(){
    	$layout = $this->getLayout();
        $update = $layout->getUpdate()->load('catalog_category_ajax_view');
        $layout->generateXml()->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    protected function _getLayeredNavHtml(){
    	$layout = $this->getLayout();
        $update = $layout->getUpdate()->load('catalog_category_layered_ajax');
        $layout->generateXml()->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	public function viewAction()
    {
    	$oneRequest = $this->getRequest();
    	if($oneRequest->isXmlHttpRequest()){
    		if ($category = $this->_initCatagory()) {
    			if($oneRequest->getParam('l')){
    				$this->getResponse()->setBody($this->_getLayeredNavHtml());
    			}else{
    				$this->getResponse()->setBody($this->_getListHtml());
    			}
    		}
    	}else{
    		parent::viewAction();
    	}
    }
 }
