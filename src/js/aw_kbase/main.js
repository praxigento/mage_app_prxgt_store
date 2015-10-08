/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Kbase
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

openGridRow = function (event) {
    var element = Event.findElement(event, 'tr');
    if(['a', 'input', 'select', 'option'].indexOf(Event.element(event).tagName.toLowerCase()) != -1) {
        return;
    }

    if(element.title){
        setLocation(element.title);
    }
};

initGrid = function(containerId) {
    if($(containerId)) {
        rows = $$('#'+containerId+' tbody tr');
        for (var row=0; row<rows.length; row++) {
            Event.observe(rows[row], 'click', openGridRow);
        }
    }
};
