<?php
class TM_Akismet_Model_Observer
{
//// example usage  
//    /**
//     * dispatch 'helpmate_notify_admin_ticket_change'
//     * 
//     * @param Varien_Event_Observer $observer
//     * @return TM_Akismet_Model_Observer
//     */
//    public function onchangeHelpmateTicket(Varien_Event_Observer $observer)
//    {
//        $service = Mage::getModel('akismet/service');
//        
//        $theard  = $observer->getEvent()->getTheard();
//        $ticket  = $theard->getTicket();
//        
//        $author = $ticket->getEmail();
//        if (null !== $ticket->getCustomerId()) {
//            $author = Mage::getModel('customer/customer')
//                ->load($ticket->getCustomerId())
//                ->getName();
//        }
//        
//        $isSpam  = $service->isSpam(
//            $author, 
//            $ticket->getEmail(), 
//            $theard->getText() //. $ticket->getTitle()
//        );
//        if ($isSpam) {
//            new Mage_Exception('this is spam');
//        }
//        
//        return $this;
//    }
}