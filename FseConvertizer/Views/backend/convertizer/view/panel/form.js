Ext.define('Shopware.apps.Convertizer.view.panel.Form', {
    extend: 'Ext.form.Panel',
    alias: 'widget.Convertizer-detail-container-exports',
    id: 'Convertizer-detail-container-Form',
    cls: 'convertizer-col-full',
    bodyPadding: 5,

    // The form will submit an AJAX request to this URL when submitted
    url: '{url action="createAccount"}',

    // Fields will be arranged vertically, stretched to full width
    layout: {
		type: 'vbox',
		align: 'center',
		pack: 'center'
	},
    defaults: {
        anchor: '100%'
    },
    // The fields
    defaultType: 'textfield',
    
    items: [
    	{
	        xtype: 'textfield',
	        name: 'email',
	        allowBlank: false
	    }, 
	    {
	        xtype: 'hidden',
	        name: 'shopurl',
	        allowBlank: false
	    },
	    {
	        xtype: 'hidden',
	        name: 'shopsystem',
	        allowBlank: false
	    },
	    {
	        xtype: 'checkboxgroup',
	        columns: 1,
	        vertical: true,
	        allowBlank: false,
	        items: [
	            { boxLabel: 'Ich stimme den AGB und Datenschutzbestimmungen zu', name: 'agb', inputValue: '1', checked: true},
	        ]
	    },
    ],
    // Reset and Submit buttons
    buttons: [{
        text: "<i class='fa fa-check-circle' aria-hidden='true'></i>Unverbindlich starten",
        formBind: true, //only enabled once the form is valid
        disabled: true,
        cls: 'convertizer-submit',
        height: 38,
        buttonAlign: 'center',
        handler: function() {
        		
            var form = this.up('form').getForm();
     		var $_oldStore = this.up('form').store;
     		var window = this.up('window');
     		
     		Ext.getCmp('convertizer-loader').show();
     	
     		var classLinks = Ext.query("*[class=loader]"); 
		    	console.log(classLinks);
				Ext.each(classLinks, function(item,index) {
					var elem = this;
					   // item.addCls('fa fa-check');
						//item.removeCls('loader');
					//setTimeout(function(elem){
					//	elem.addCls('fa fa-check');
					//	elem.removeCls('loader');
					//	},600); 
			});
     	
     	
            if (form.isValid()) {
                form.submit({
                    success: function(form, action) {
                    	//alert('success: ' + action.result.message);
                    	window.destroy();
                    	var $old_store = Ext.getStore('Shopware.apps.Convertizer.store.Contentstore').reload();
                    },
                    failure: function(form, action) {
                    	alert('error: ' + action.result.message);
                    	var $old_store = Ext.getStore('Shopware.apps.Convertizer.store.Contentstore').reload();
                    }
                });
            }
        }
    }],
    initComponent: function() {
    	
    	this.callParent(arguments);
        var me = this;
        var $_store = me.store;
    	var thisForm = me.getForm();
    	var $_email 	= $_store.data.items[0].raw.data.storeownermail;
    	var $_shopurl 	= $_store.data.items[0].raw.data.storehost;
    	thisForm.findField('email').setValue($_email);
    	thisForm.findField('shopsystem').setValue('shopwareplugin');
    	thisForm.findField('shopurl').setValue($_shopurl);

    },
    renderTo: Ext.getBody(),
    
});