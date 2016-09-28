Ext.define('Shopware.apps.Convertizer.store.Contentstore', {
    
   extend: 'Ext.data.Store',

   proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="Convertizer" action="getStoreData"}'
        },
        reader: {
            type: 'json',
        },
    },
    
    model: 'Shopware.apps.Convertizer.model.Contentmodel',
});