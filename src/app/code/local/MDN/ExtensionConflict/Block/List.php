<?php

class MDN_ExtensionConflict_Block_List extends Mage_Adminhtml_Block_Widget_Grid
{
	
	public function __construct()
    {
        parent::__construct();
        $this->setId('ExtensionConflictGrid');
        $this->setDefaultSort('ec_is_conflict');
        $this->setDefaultDir('DESC');
        $this->_parentTemplate = $this->getTemplate();
        //$this->setTemplate('Organizer/Task/List.phtml');	
        $this->setEmptyText($this->__('No items'));                
    }
    
        
    /**
     * Charge la collection
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {		            
    	
        $collection = Mage::getModel('ExtensionConflict/ExtensionConflict')
        	->getCollection();
    			        	
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
   /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
         $this->addColumn('ec_core_module', array(
            'header'=> Mage::helper('ExtensionConflict')->__('Core Module'),
            'index' => 'ec_core_module'
        ));

         $this->addColumn('ec_core_class', array(
            'header'=> Mage::helper('ExtensionConflict')->__('Core Class'),
            'index' => 'ec_core_class'
        ));
        
        $this->addColumn('ec_rewrite_classes', array(
            'header'=> Mage::helper('ExtensionConflict')->__('Rewrite Classes'),
            'index' => 'ec_rewrite_classes'
        ));

        $this->addColumn('ec_is_conflict', array(
            'header'=> Mage::helper('ExtensionConflict')->__('Is Conflict'),
            'index' => 'ec_is_conflict',
            'renderer' => 'MDN_ExtensionConflict_Block_Widget_Grid_Column_Renderer_IsConflict',
            'align' => 'center'

        ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    public function getRefreshUrl()
    {
    	return $this->getUrl('ExtensionConflict/Admin/Refresh');
    }
    
    public function getUploadUrl()
    {
    	return $this->getUrl('ExtensionConflict/Admin/Upload');
    }
    
    public function getDeleteVirtualModuleUrl()
    {
    	return $this->getUrl('ExtensionConflict/Admin/DeleteVirtualModule');
    }
    

}
