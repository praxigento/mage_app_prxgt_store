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
 * @package    AW_Zblocks
 * @copyright  Copyright (c) 2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

var AWZBlock = Class.create({
    initialize:function (name) {
        window[name] = this;
        document.observe('dom:loaded', this.prepareSelf.bind(this));
    },

    prepareSelf:function () {
        if ($('block_position')) {
            $('block_position').observe('change', this.checkPosition.bind(this));
        }
        this.checkPosition();
    },

    checkPosition:function () {
        if ($('block_position') && typeof(aw_zblocks_categories) != 'undefined') {
            var needCategory = false;
            for (var i = 0; i < aw_zblocks_categories.length; i++) {
                if (aw_zblocks_categories[i] == $('block_position').value) {
                    needCategory = true;
                }
            }
            var categoryErrorDiv = $('awzb_categories_error');
            var categories = $('categories-fields');
            var categoriesIds = $('product_categories');
            if (needCategory && categories && categoryErrorDiv) {
                if(typeof(this._old_categories) != 'undefined') {
                    categoriesIds.value = this._old_categories;
                    var _catIds = categoriesIds.value.split(',');
                    if(typeof(_catIds) == 'object') {
                        _catIds.each(function(el) {
                            if(el) {
                                tree.getNodeById(el).getUI().check(1);
                            }
                        });
                    }
                }
                categoryErrorDiv.hide();
                categories.show();
            } else {
                if(categoriesIds.value) {
                    this._old_categories = categoriesIds.value;
                    categoriesIds.value = '';
                    var _catIds = categoriesIds.value.split(',');
                    if(typeof(_catIds) == 'object') {
                        _catIds.each(function(el) {
                            if(el) {
                                tree.getNodeById(el).getUI().check(0);
                            }
                        });
                    }
                }
                categoryErrorDiv.show();
                categories.hide();
            }
        }
    }
});

new AWZBlock('awzblock');
