var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('options', ['#acp-configurable-block']);
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: false,
    updateOnActionRequest: true,

    beforeUpdate: function(html){
        var el = $$(this.selectors[0])[0];
        if (el && el.down()) {
            el.down().remove();
        }
        return;
    },
    update: function(html) {
        this.beforeUpdate(html);
        var el = new Element('div');
        el.innerHTML = html;
        $$(this.selectors[0])[0].appendChild(el.down());
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