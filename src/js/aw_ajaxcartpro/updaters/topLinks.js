var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('topLinks', null, ['.header .quick-access>', '.toplinks-bar']);
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,
    
    beforeUpdate: function(html){
        return;
    },
    afterUpdate: function(html, selectors){
        return;
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;