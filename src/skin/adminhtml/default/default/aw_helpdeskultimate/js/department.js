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
 * @copyright  Copyright (c) 2009-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

var AWHDUDepartment = Class.create({
    initialize:function (name) {
        window[name] = this;
        document.observe('dom:loaded', this.init.bind(this));
    },

    init:function () {
        this.selectUseNotifications = $('notify');
        this.inputEmail = $('contact');
        this.starSpan = $$('#edit_form label[for="contact"]').length ? $$('#edit_form label[for="contact"] span').first() : null;
        console.log(this.selectUseNotifications, this.inputEmail);
        if (this.selectUseNotifications && this.inputEmail) {
            this.selectUseNotifications.observe('change', this.checkUseNotifications.bind(this));
            this.checkUseNotifications();
        }
    },

    checkUseNotifications:function () {
        if (parseInt(this.selectUseNotifications.value)) {
            this.makeEmailRequired();
        } else {
            this.unrequireEmailField();
        }
    },

    makeEmailRequired:function () {
        this.inputEmail.addClassName('required-entry');
        this.inputEmail.addClassName('validate-uniq-email');
        if (this.starSpan) {
            this.starSpan.show();
        }
    },

    unrequireEmailField:function () {
        this.inputEmail.removeClassName('required-entry');
        this.inputEmail.removeClassName('validate-uniq-email');
        if (this.starSpan) {
            this.starSpan.hide();
        }
    }
});

new AWHDUDepartment('awhdudepartment');
