<?php
class TM_AskIt_Block_Adminhtml_AskIt_Edit_Form_Element_Autocompleter extends Varien_Data_Form_Element_Text
{
    protected $_url = '';
    
    protected $_value = '';


    public function __construct($attributes=array())
    {
        if (isset($attributes['autocompleterUrl'])) {
            $this->_url = $attributes['autocompleterUrl'];
        }
        if (isset($attributes['autocompleterValue'])) {
            $this->_value = $attributes['autocompleterValue'];
        }
        parent::__construct($attributes);
    }
    
    protected function _getJs() 
    {
        $text   = $this->getId();
        $update = $text . '_autocomplete';
        $url    = $this->_url;
        return "<div id=\"{$update}\" class=\"autocomplete\"></div>
            <script type=\"text/javascript\">
                new Ajax.Autocompleter(
                    '{$text}',
                    '{$update}',
                    '{$url}',
                    {
                        paramName:\"query\",
                        minChars:3,
                        indicator:\"global_search_indicator\",
                        afterUpdateElement : getSelectionId
                    }
                );
                function getSelectionId(text, li) {
                    //console.log(arguments);
                    //debugger;
                    text.value = li.title;
                    //debugger;
                    $('{$this->getId()}'.replace('_query', '')).value = li.id;
                }
            </script>";
    }


    public function getHtml()
    {
        $hidden = new Varien_Data_Form_Element_Hidden($this->getData());
        $hidden->setData('label', null);
        $hidden->setForm($this->getForm());
        
        $this->setId($this->getId() . '_query');
        $this->setName($this->getId());
        $this->setValue($this->_value);
        
        return $hidden->getHtml()
            .parent::getHtml() 
            . $this->_getJs();
    }
}
