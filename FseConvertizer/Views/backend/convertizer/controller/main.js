Ext.define('Shopware.apps.Convertizer.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
        
        $_mainStore = me.getStore('Shopware.apps.Convertizer.store.Contentstore');
        $_mainStore.load(
            	{
            		callback: function (records, options, success){
            			if (success){
            				me.mainWindow = me.getView('Window').create({ 
            					contentStore : $_mainStore,
        					}).show();  
            			}
            		}
            	}
           );
        
         
    },
});