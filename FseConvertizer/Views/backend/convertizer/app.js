Ext.define('Shopware.apps.Convertizer', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.Convertizer',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 
    	'Main' 
    ],

    views: [
        'Window'
    ],

    launch: function() {
       return this.getController('Main').mainWindow;
    }
});