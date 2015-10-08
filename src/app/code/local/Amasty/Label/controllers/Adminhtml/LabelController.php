<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/ 
class Amasty_Label_Adminhtml_LabelController extends Mage_Adminhtml_Controller_Action
{
    protected $_title     = 'Product label';
    protected $_modelName = 'label';
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Catalog'))->_title($this->__(ucwords($this->_title) . 's'));	 
        return $this;
    } 
    
    public function indexAction()
    {
	    $this->loadLayout(); 
        $this->_setActiveMenu('catalog/amlabel');
        $this->_addContent($this->getLayout()->createBlock('amlabel/adminhtml_' . $this->_modelName)); 	    
 	    $this->renderLayout();
    }

	public function newAction() 
	{
        $this->editAction();
	}
	
    public function editAction() 
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amlabel/' . $this->_modelName)->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amlabel')->__('Record does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		else {
		    $this->prepareForEdit($model);
		}
		
		Mage::register('amlabel_' . $this->_modelName, $model);

		$this->loadLayout();
		
		$this->_setActiveMenu('catalog/amlabel');
		$this->_title($this->__('Edit'));
		
        $this->_addContent($this->getLayout()->createBlock('amlabel/adminhtml_' . $this->_modelName . '_edit'));
        $this->_addLeft($this->getLayout()->createBlock('amlabel/adminhtml_' . $this->_modelName . '_edit_tabs'));
        
		$this->renderLayout();
	}

	public function saveAction() 
	{
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('amlabel/' . $this->_modelName);
	    $data = $this->getRequest()->getPost();
		if ($data) {
			
			$data = $this->_filterDates($data, array('from_date', 'to_date'));
			if (!empty($data['to_time'])) {
				$data['to_date'] = $data['to_date'] . ' ' . $data['to_time'];
			}
			
            if (!empty($data['from_time'])) {
				$data['from_date'] = $data['from_date'] . ' ' . $data['from_time'];
			} 
			
			$model->setData($data)->setId($id);
			try {
			    $this->prepareForSave($model);
			    
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('amlabel')->__($this->_title . ' has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }	
            return;
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amlabel')->__('Unable to find a record to save'));
        $this->_redirect('*/*');
	} 
	
    public function deleteAction()
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amlabel/' . $this->_modelName)->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError($this->__('Record does not exist'));
			$this->_redirect('*/*/');
			return;
		}
         
        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__($this->_title . ' has been successfully deleted'));
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }	
		
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_modelName . 's');
        if (!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amlabel')->__('Please select records'));
             $this->_redirect('*/*/');
             return;
        }
         
        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('amlabel/' . $this->_modelName)->load($id);
                $model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($ids)
                )
            );
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
        
    }
    
    public function optionsAction()
    {
        $result = '<input id="attr_value" name="attr_value" value="" class="input-text" type="text" />';
        
        $code = $this->getRequest()->getParam('code');
        if (!$code){
            $this->getResponse()->setBody($result);
            return;
        }
        
        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($code);
        if (!$attribute){
            $this->getResponse()->setBody($result);
            return;            
        }

        if (!in_array($attribute->getFrontendInput(), array('select', 'multiselect')) ){
            $this->getResponse()->setBody($result);
            return;            
        }
        
        $options = $attribute->getFrontend()->getSelectOptions();
        //array_shift($options);  
        
        $result = '<select id="attr_value" name="attr_value" class="select">';
        foreach ($options as $option){
            $result .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';      
        }
        $result .= '</select>';    
        
        $this->getResponse()->setBody($result);
        
    }    
    
    
    protected function prepareForSave($model)
    {
        // convert stores from array to string
        $stores = $model->getData('stores');
        if (is_array($stores)){
            // need commas to simplify sql query
            $model->setData('stores', ',' . implode(',', $stores) . ',');    
        } 
        else { // need for null value
            $model->setData('stores', ''); 
        }
        
        /*
         * Process customer groups
         */
    	$groups = $model->getData('customer_group');
        if (is_array($groups)){
            // need commas to simplify sql query
            $model->setData('customer_group', ',' . implode(',', $groups) . ',');    
        } 
        else { // need for null value
            $model->setData('customer_group', ''); 
        }
        
        //upload images
        $data = $this->getRequest()->getPost();
		$path = Mage::getBaseDir('media') . DS . 'amlabel' . DS;
		$imagesTypes = array('prod', 'cat');
		foreach ($imagesTypes as $type){
		    $field = $type . '_img';
		    
		    $isRemove = array_key_exists('remove_' . $field, $data);
		    $hasNew   = !empty($_FILES[$field]['name']);
		    
            try {
    		    // remove the old file
    		    if ($isRemove || $hasNew){
        	        $oldName = isset($data['old_' . $field]) ? $data['old_' . $field] : '';
        	        if ($oldName){
        	             @unlink($path . $oldName);
        	             $model->setData($field, '');
        	        }
    		    }
    
    		    // upload a new if any
    		    if (!$isRemove && $hasNew){
    		        //find the first available name
                    $newName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $_FILES[$field]['name']);
                    if (substr($newName, 0, 1) == '.') // all non-english symbols
                        $newName = 'label' . $newName;
                    $i=0;    
    		        while (file_exists($path . $newName)){
    		            $newName = $i . $newName;
    		            ++$i;
    		        }
               
                    $uploader = new Varien_File_Uploader($field);
                	$uploader->setFilesDispersion(false);
            		$uploader->setAllowRenameFiles(false);
               		$uploader->setAllowedExtensions(array('png','gif', 'jpg', 'jpeg'));
            		$uploader->save($path, $newName);	
            		 
            		$model->setData($field, $newName);     
    		    }   
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
            }
		}        
        
        return true;
    }
    
    protected function prepareForEdit($model)
    {
        $stores = $model->getData('stores');
        if (!is_array($stores)){
            $model->setData('stores', explode(',', $stores));    
        }
        
        return true;
    }
    
    protected function _title($text = null, $resetIfExists = true)
    {
        if (Mage::helper('ambase')->isVersionLessThan(1,4)){
            return $this;
        }
        return parent::_title($text, $resetIfExists);
    }  
    
    
       
}