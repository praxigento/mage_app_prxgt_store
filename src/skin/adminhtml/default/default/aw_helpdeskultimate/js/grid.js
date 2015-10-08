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
 * @package    AW_Helpdeskultimate
 * @copyright  Copyright (c) 2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

var AWHDUGrid = Class.create({
    initialize:function(name) {
        window[name] = this;
        document.observe('dom:loaded', this.init.bind(this));
    },

    init:function() {
        this.grid = $$('.grid table').first();
        if(this.grid != 'undefined') {
            this.gridCells = $$('#' + this.grid.identify() + ' tbody>tr>td');
            this.processCells();
        }
    },

    processCells: function() {
        this.gridCells.each(function(gridCell) {
            var nameAttr = this.detectNameInClass(gridCell.readAttribute('class'));
            if(nameAttr) {
                gridCell.writeAttribute('name', nameAttr);
            }
        }.bind(this));
    },

    detectNameInClass: function(classNames) {
        var classes = classNames.split(' ');
        var name = null;
        classes.each(function(className) {
            if(className.indexOf('aw-hduat-') == 0) {
                name = className.substring(9);
            }
        });
        return name;
    }
});

new AWHDUGrid('awhdugrid');
