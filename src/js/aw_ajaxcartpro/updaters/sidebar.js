var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('sidebar');
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,
    
    beforeUpdate: function(html){
        return;
    },
    
    afterUpdate: function(html, selectors){
        var me = this;
        //call mage function
        if (typeof(truncateOptions) === 'function') {
            truncateOptions();
        }

        selectors.each(function(selector){
            me._effect(selector);
        });
        return;
    },

    _effect: function(obj) {
        var el = $$(obj)[0];
        switch(this.config.cartAnimation) {
            case 'opacity':
                el.hide();
                new Effect.Appear(el);
                break;
            case 'grow':
                el.hide();
                new Effect.BlindDown(el);
                break;
            case 'blink':
                new Effect.Pulsate(el);
                break;
            default:
        }
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;