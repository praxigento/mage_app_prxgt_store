<?php
/**
* Copyright © Pulsestorm LLC: All rights reserved
*/
class Tamarashorin_Commercebug_Model_Graphviz
{
    public function capture()
    {    
        $collector  = new Tamarashorin_Commercebug_Model_Collectorgraphviz; 
        $o = new stdClass();
        $o->dot = Tamarashorin_Commercebug_Model_Observer_Dot::renderGraph();
        $collector->collectInformation($o);

        //logging        
        // $data = new stdClass();
        // $data->dot = Tamarashorin_Commercebug_Model_Observer_Dot::renderGraph();
        // $filename = preg_replace('%[^a-z0-9-]%','_',date(DATE_ATOM));
        // $filename = "$filename" . ".dot";
        // file_put_contents('/Users/foo/Documents/magento-layouts' . '/' . $filename,
        // $data->dot);
    }
}