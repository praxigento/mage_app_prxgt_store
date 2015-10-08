<?php
class TM_AskIt_Adminhtml_Model_System_Config_Source_QuestionStatus
{
    public function toOptionArray()
    {
        $statusses = Mage::getSingleton('askit/status')->getQuestionOptionArray();
        $data = array();
        foreach($statusses as $statusId => $statusLabel) {
            $data[] = array('value' => $statusId, 'label' => $statusLabel);
        }
        return $data;
    }
}