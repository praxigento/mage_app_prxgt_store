var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('wishlist', ['.my-account']);
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,

    beforeUpdate: function(html){
        return;
    },

    update: function(html) {
        this.beforeUpdate(html);
        var selector = this.selectors[0];
        $$(selector)[0].innerHTML = html;
        this._evalScripts(html);
        this.afterUpdate(html, this.selectors);
        return true;
    },

    afterUpdate: function(html, selectors){
        return;
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;