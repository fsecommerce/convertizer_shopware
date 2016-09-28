Ext.define('Shopware.apps.Convertizer.view.Window', {
	id: 'mainwindow',
    extend: 'Enlight.app.Window',
    host:'www.yourdomain.com',
    feedurl : '',
    landingpageurl : '',
    customerId : '0',
    alias: 'widget.Convertizer-list-window',
    height: '100%',
    width: 800,
    cls:'convertizer-window',
	/**
     * Set no border for the window
     * @boolean
     */
    border:false,
    /**
     * True to automatically show the component upon creation.
     * @boolean
     */
    autoShow:false,
    /**
     * True to display the 'maximize' tool button and allow the user to maximize the window, false to hide the button and disallow maximizing the window.
     * @boolean
     */
    maximizable:true,
    /**
     * True to display the 'minimize' tool button and allow the user to minimize the window, false to hide the button and disallow minimizing the window.
     * @boolean
     */
    minimizable:true,
    
    layout: {
         type: 'vbox',
        align: 'stretch'
    },
    
    initComponent:function () {
    	
        var me = this;
        var $_store = me.contentStore;
        me.customerId = $_store.data.items[0].raw.data.customer_id;
        me.host = $_store.data.items[0].raw.data.storehost;
        me.landingpageurl = $_store.data.items[0].raw.data.landingpageurl;
        me.feedurl = $_store.data.items[0].raw.data.feed_url;
        var customer_exists = $_store.data.items[0].raw.data.customerexists;
		var customer_is_remote = $_store.data.items[0].raw.data.is_remote;

		

        if(customer_exists !== 1){
        	me.createItemsNew();
        }else{
        	if (customer_is_remote == 1){
        		me.createItemsExists();
        	}else{
        		me.createItemsNoRemote();
        	}
        	
        }
        me.title = "convertizer Dynamische Landingpage";
        me.callParent(arguments);
       
    },
    
    createItemsNew: function() {
        
        var me = this;
	    me.Panel = Ext.create('Ext.panel.Panel', {
	    	id : 'convertizer-panel',
        	height: 870,
         	items: [
         		me.createHeader(),
         		me.createTitleCol(),
         		me.createColLeftNew(),
         		me.createColCenterNew(),
         		me.createColRightNew(),
         		me.createFormTitle(),
            	me.createForm(),
            	me.createFooterLeftNew(),
            	me.createFooterRightNew(),
            	me.createLoader(),
        	],
		});
        me.items = [ me.Panel ];
        
    },
    createItemsExists : function(){
    	
    	var me = this;
	    me.Panel = Ext.create('Ext.panel.Panel', {
	    	id : 'convertizer-panel',
        	height: 850,
         	items: [
         		me.createHeader(),
         		me.createTitleCol(),
         		me.createColLeftExists(),
         		me.createColRightExists(),
         		me.createFooterTitleExists(),
         		me.createFooterExists(),
        	],
		});
        me.items = [ me.Panel ];
  
    },
    createItemsNoRemote : function(){
    	var me = this;
	    me.Panel = Ext.create('Ext.panel.Panel', {
	    	id : 'convertizer-panel',
        	height: 850,
         	items: [
         		me.createHeader(),
         		me.createTitleCol(),
         		me.createColLeftNoRemote(),
         		me.createColRightNoRemote(),
         		me.createColFullNoRemote(),
         		me.createFooterTitleExists(),
         		me.createFooterExists(),
        	],
		});
        me.items = [ me.Panel ];
    },
    createHeader: function(){
    	
        return {
            html: '<div class="convertizer-window-header">\n\
                     <div class="convertizer-logo"></div>  \n\
                       <div class="convertizer-contact-right">Fragen?<br/><strong>Tel: 0341 221 70 853</strong></div>\n\
                   </div>',
        }
        
    },
    createTitleCol: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-title-col', 
		    height: 50,
		    html: '<h2>Dynamische Adwords Landingpage</h2>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createColLeftNew: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-col-left', 
		    height: 320,
		    html: '<span class="convertizer-col-title">Das bringt Dir Convertizer</span> \n\
		    		<p>Stell Dir vor ein User klickt auf Deine Adwords Anzeige:</p> \n\
		    		<span class="convertizer-quest seperator"><i class="fa fa-question-circle" aria-hidden="true"></i>Sieht er auf Deinem Shop genau die Produkte, die zur Anzeige und zum Keyword passen?</span> \n\
		    		<span class="convertizer-attention seperator"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><strong>Für die maximale Conversionrate müssen User exakt passende Produkte zur Anzeige sehen.</strong></span> \n\
		    		<p>Wie stellst Du das heute sicher?</p> \n\
		    		<ul> \n\
		    			<li><span class="convertizer-quest"><i class="fa fa-question-circle" aria-hidden="true"></i>Startseite?</span></li> \n\
		    			<li><span class="convertizer-quest"><i class="fa fa-question-circle" aria-hidden="true"></i>Suchseite?</span></li> \n\
		    			<li><span class="convertizer-quest"><i class="fa fa-question-circle" aria-hidden="true"></i>Manuelle Kategorieseiten</span></li> \n\
		    		</ul> \n\
	    			<p class="seperator">Alles Mist, aber <strong>wir haben die Lösung!</strong></p>', 
		    renderTo: Ext.getBody(),
		});
		return content;
    },
 	createColCenterNew: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-col-center', 
		    height: 320,
		    html: '<span class="convertizer-col-title">So funktioniert Convertizer</span> \n\
		    		<p>Convertizer erweitert deinen Shop um eine dynamische Landingpage, die automatisch passende Produkte zu Deinen Adwordsanzeigen darstellt</p> \n\
		    		<span class="convertizer-check seperator"><i class="fa fa-check-circle" aria-hidden="true"></i>Denn wenn User sehen, was eine Anzeige versprochen hat, kaufen sie häufiger!</span> \n\
		    		<p>Starten ist kinderleicht:</p> \n\
		    		<ol> \n\
		    			<li>1.Account anlegen: <strong>Klick unten auf \"unverbindlich starten\"</strong></li> \n\
		    			<li>2.Dieses Plugin installiert die dynamische Landingpage</li> \n\
		    			<li>3.Landingpage bei ausgewählten Adwords Kampagnen direkt mit convertizer hinterlegen</li> \n\
		    		</ol>', 
		    renderTo: Ext.getBody(),
		});
		return content;
    },
    createColRightNew: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-col-right', 
		    height: 320,
		    html: '<span class="convertizer-col-title">Beispiel</span> \n\
		    		<img src="https://app.convertizer.com/media/how-convertizer-works.png" alt="" width="100%"/>', 
		    renderTo: Ext.getBody(),
		});
		return content;
    },
    createFormTitle: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-form-title', 
		    height: 50,
		    html: '<h2>Einrichtung jetzt starten!</h2>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createForm: function(){
    	
        var me = this;
        return Ext.create('Shopware.apps.Convertizer.view.panel.Form', {
            store: me.contentStore,
        });
        
    },
    createFooterLeftNew: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-left', 
		    height: 100,
		    html: '<h3>Diese Kunden vertrauen Convertizer</h3> \n\
		    		<img src="https://app.convertizer.com/media/welcome_left-col.png" alt="" width="100%" />', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createFooterRightNew: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-right', 
		    height: 100,
		    html: '<h3>Pressestimmen</h3> \n\
		    		<img src="https://app.convertizer.com/media/welcome_right-col.png" alt="" width="100%" />', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createLoader : function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-content-loader', 
		    id : 'convertizer-loader',
		    hidden: true,
		    html: '<div class="inner"><span class="convertizer-loader-title">Deine dynamische Landingpage wird eingerichtet</span> \n\
		    		<ul id="convertizer-progress"> \n\
		    			<li><div class="icon"><i class="loader" aria-hidden="true"></div></i><div class="progress-note"><span>Dein Account wird erstellt</span></div></li> \n\
		    			<li><div class="icon"><i class="loader" aria-hidden="true"></div></i><div class="progress-note"><span>Landingpage wird angelegt</span></div></li> \n\
		    			<li><div class="icon"><i class="loader" aria-hidden="true"></div></i><div class="progress-note"><span>Produktdaten werden der Landingpage hinzugefügt</span></div></li> \n\
		    			<li><div class="icon"><i class="loader" aria-hidden="true"></div></i><div class="progress-note"><span>Tracking wird aktiviert</span></div></li> \n\
		    		</ul></div>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createColLeftExists: function(){
    	
		var content =  new Ext.Component({
			id:	 'convertizer-content-col-left-exists',
		    cls: 'convertizer-content-col-left-exists', 
		    height: 450,
		    html: '<span class="convertizer-col-title-inline">Status</span><span class="convertizer-activity-indicator">aktiv</span> \n\
		    		<p>Deine Landingpage ist eingerichtet<br/> \n\
		    		Die URL Deiner Landingpage ist: </p> \n\
					<div id="convertizer-landingpage-url">' + this.host + '/' + this.landingpageurl + '</div> \n\
					<span class="convertizer-col-title seperator">Ausprobieren!</span> \n\
					<p>Probier Deine neue Landingpage aus:</p> \n\
					<p class="seperator">Theoretisches Keyword eingeben und sehen, dass jetzt jedes Deiner Keywords und jede Deiner Anzeigen eine passende Landingpage haben kann</p>\n\
					<div class="keywordform"> \n\
					<form class="convertizer-keywordtest" target="_blank" method="GET" action="http://' + this.host + '/' + this.landingpageurl + '" > \n\
					  <input name="q" id="keyword-input" type="text" placeholder="Keyword eingeben" value=""/> \n\
					  <div class="keyword-down"><i class="fa fa-angle-double-down" aria-hidden="true"></i></div> \n\
					  <button type="submit">Jetzt <span id="convertizer-keyword-buy-action-name"><i>keyword</i></span> kaufen</button> \n\
					 </form> \n\
					 <div class="convertizer-ga-line-green"></div> \n\
					 <div class="convertizer-ga-line-learnmore">Mehr über <span id="convertizer-learn-more-value"><i>keyword</i></span> erfahren</div> \n\
					 <div class="convertizer-ga-line-last"></div>', 
		    renderTo: Ext.getBody(),
		});

		Ext.get('keyword-input').addListener('keyup', function() { 
			var thisVal = this.getValue();
			Ext.get('convertizer-learn-more-value').update(thisVal);
			Ext.get('convertizer-keyword-buy-action-name').update(thisVal);
		});

		return content;
    	
    },
    createColRightExists: function(){
    	
    	var content =  new Ext.Component({
    		id:	 'convertizer-content-col-right-exists',
		    cls: 'convertizer-content-col-right-exists', 
		    height: 450,
		    html: '<span class="convertizer-col-title">Dein Convertizer Account</span> \n\
		    		<p><strong>Konto: ID: </strong>' + this.customerId + '<br/> \n\
		    		<strong>Dein Accountmanager: </strong>Ole</p> \n\
					<div id="convertizer-login-button"><i class="fa fa-key" aria-hidden="true"></i><a target="_blank" href="https://app.convertizer.com/customer/account/login" >bei Convertizer einloggen</a></div> \n\
					<div id="convertizer-manager-contact"><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:ole@convertizer.com" >Accountmanager kontaktieren</a></div> \n\
					<span class="convertizer-col-title">Entfache das volle Potential bei Convertizer</span> \n\
					<img class="seperator" src="https://app.convertizer.com/media/plugins/shopware/banner/marketing_right-col_1.png" alt="" width="100%"/> \n\
					<img class="seperator" src="https://app.convertizer.com/media/plugins/shopware/banner/marketing_right-col_2.png" alt="" width="100%"/>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createColLeftNoRemote: function(){
    	
    	var content =  new Ext.Component({
    		id:	 'convertizer-content-col-left-no-remote',
		    cls: 'convertizer-content-col-left-no-remote', 
		    height: 200,
		    html: '<span class="convertizer-col-title">Dein Convertizer Account</span> \n\
		    		<p><strong>Konto: ID: </strong>' + this.customerId + '<br/> \n\
		    		<strong>Dein Accountmanager: </strong>Ole</p> \n\
					<div id="convertizer-login-button"><i class="fa fa-key" aria-hidden="true"></i><a target="_blank" href="https://app.convertizer.com/customer/account/login" >bei Convertizer einloggen</a></div> \n\
					<div id="convertizer-manager-contact"><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:ole@convertizer.com" >Accountmanager kontaktieren</a></div> \n\
					<span class="convertizer-col-title">Entfache das volle Potential bei Convertizer</span>', 
		    renderTo: Ext.getBody(),
		});
		return content;
  	},
	createColRightNoRemote: function(){
    	
    	var content =  new Ext.Component({
    		id:	 'convertizer-content-col-right-no-remote',
		    cls: 'convertizer-content-col-right-no-remote', 
		    height: 200,
		    html: '<span class="convertizer-col-title">Deine Feed URL</span> \n\
		    		<input class="convertizer-feedurl" value="' + this.feedurl + '" /> \n\
		    		<p>Kopiere diese URL und speichere sie in deinem convertizer Account als Feed Url.</p>', 
		    renderTo: Ext.getBody(),
		});
		return content;
    },
    createColFullNoRemote: function(){
    	
    	var content =  new Ext.Component({
    		id:	 'convertizer-content-col-full-no-remote',
		    cls: 'convertizer-content-col-full-no-remote', 
		    height: 200,
		    html: '<img class="banner-img" src="https://app.convertizer.com/media/plugins/shopware/banner/shopware_account_1.png" alt=""/> \n\
		    <img class="banner-img" src="https://app.convertizer.com/media/plugins/shopware/banner/shopware_account_2.png" alt=""/>', 
		    renderTo: Ext.getBody(),
		});
		return content;
    },
     createFooterTitleExists: function(){
     	
    	var content =  new Ext.Component({
		    cls: 'convertizer-footer-title-col', 
		    height: 50,
		    html: '<h2>Fragen?</h2>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
    createFooterExists: function(){
    	
    	var content =  new Ext.Component({
		    cls: 'convertizer-footer-exists', 
		    height: 300,
		    html: '<div class="convertizer-ole-img"></div> \n\
		    		<div class="convertizer-account-manager-data"><p>Dein Accountmanager Ole</p> \n\
		    		<p>ole@convertizer.com<br/> \n\
		    		0341 221 70 851 </p></div>', 
		    renderTo: Ext.getBody(),
		});
		return content;
		
    },
});