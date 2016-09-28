<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Martin Ketelhut
 *  @copyright FS eCommerce GmbH
 *  @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 *  @version 1.0.2
 */

class Shopware_Plugins_Frontend_FseConvertizer_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

	/**
	* Install Index Routine
	*
	* @return array
	*/
	public function getCapabilities()
	{
	    return array('install' => true, 'enable' => true, 'update' => true);
	}
	
	/**
	* Plugin Label
	*
	* @return string
	*/
    public function getLabel()
    {
        return 'Convertizer';
    }
	
	/**
	* Install Methods
	*
	* @return boolean
	*/
    public function install()
    {
    	$this->registerEvents();
        $this->createProductExport();
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch', 'onPostDispatch');
        $this->createConfigForm();
        $this->createMenuItems();
        return true;
    }

    /**
    * Returns the version of this plugin
    *
    * @return string
    */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
    * Define template and variables
    *
    * @param Enlight_Event_EventArgs $args
    */
    public static function onPostDispatch(Enlight_Event_EventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();
        $view = $args->getSubject()->View();
        self::addTracking($request, $response, $view);
    }
	
	/**
    * adds the tracking code to success page
    *
    * @param $request, $response, $view
    */
    private static function addTracking($request, $response, $view)
    {
        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend' || $request->isXmlHttpRequest() || !$view->hasTemplate()) {
            return;
        }

        $config = Shopware()->Plugins()->Frontend()->FseConvertizer()->Config();

        if (empty($config->trackingId)) {
            return;
        }

        if (!$request->getControllerName() == "checkout") {
            return;
        }

        if (!$request->getActionName() == "finish") {
            return;
        }

        $view->extendsTemplate(dirname(__FILE__) . '/Views/frontend/tracking.tpl');
        $view->ConvertizerTrackingId = $config->trackingId;
    }

    /**
    * standard meta description
    *
    * @return plugin data
    */
    public function getInfo()
    {
        return array('version' => $this->getVersion(), 'copyright' => 'Copyright (c) 2015, FS eCommerce GmbH', 'autor' => 'FS eCommerce GmbH', 'label' => $this->getLabel(), 'description' => file_get_contents($this->Path() . 'info.txt'), 'support' => 'http://www.fs-ecommerce.com', 'link' => 'http://www.fs-ecommerce.com', 'revision' => '1');
    }

	/**
    * create the menu item under 'Marketing'
    *
    */
    private function createMenuItems()
    {
    	$this->createMenuItem(array(
		     'label' => 'Convertizer',
		     'controller' => 'Convertizer',
		     'class' => 'convertizer-icon',
		     'action' => 'Index',
		     'active' => 1,
		     'parent' => $this->Menu()->findOneBy(['label' => 'Marketing'])
		));
    }
	
 	/**
    * create plugin configuration form
    *
    */
    protected function createConfigForm()
    {
        $form = $this->Form();
        $form->setElement('text', 'trackingId', array('label' => 'Tracking-Id', 'required' => true, 'value' => '', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->setElement('text', 'customerId', array('label' => 'Kontonummer', 'required' => true, 'value' => '', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->setElement('text', 'landingpage_id', array('label' => 'LandingpageId', 'required' => true, 'value' => '', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
        return true;
    }

 	/**
    * create the product export template for convertizer feed
    *
    */
    private function createProductExport()
    {
        $checksql = "SELECT * FROM s_export WHERE name = 'convertizer'";
        $query = Shopware()->Db()->fetchRow($checksql, array(1));
		$id = 0;
        if ($query) {
            return true;
        } else {

            $head = "{strip}\nid{#S#}\nsystem_id{#S#}\nmanufacturer{#S#}\ntitle{#S#}\ncategory{#S#}\ndescription{#S#}\nimage_link{#S#}\nadditional_image_link_1{#S#}\nadditional_image_link_2{#S#}\nadditional_image_link_3{#S#}\nadditional_image_link_4{#S#}\nadditional_image_link_5{#S#}\nlink{#S#}\nprice{#S#}\nsale_price{#S#}\ncurrency{#S#}\ndelivery_time{#S#}\ngewicht{#S#}\nmodell_nr{#S#}\navailability{#S#}\nfacts_attr1{#S#}\nfacts_attr2{#S#}\nfacts_attr3{#S#}\nfacts_attr4{#S#}\nfacts_attr5{#S#}\nfacts_attr6{#S#}\nfacts_attr7{#S#}\nfacts_attr8{#S#}\nfacts_attr9{#S#}\nfacts_attr10{#S#}\nfacts_attr11{#S#}\nfacts_attr12{#S#}\nfacts_attr13{#S#}\nfacts_attr14{#S#}\nfacts_attr15{#S#}\nfacts_attr16{#S#}\nfacts_attr17{#S#}\nfacts_attr18{#S#}\nfacts_attr19{#S#}\nfacts_attr20{#S#}\nAdditionaltext{#S#}\nStock{#S#}\nprop_01{#S#}\nprop_02{#S#}\nprop_03{#S#}\nprop_04{#S#}\nprop_05{#S#}\nprop_06{#S#}\nprop_07{#S#}\nprop_08{#S#}\n{/strip}{#L#}";

            $body = "{assign var=\"string\" value=\$sArticle.articleID|articleImages:\$sArticle.ordernumber:2:\"##\"}\n{assign var=\"productVariantImage\" value=\"##\"|explode:\$string}\n{assign var=\"properties\" value=\$sArticle.articleID|property:\$sArticle.filtergroupID}\n{if \$sArticle.configurator && substr_count(\$sArticle.ordernumber,\'.\') > 0}\n{strip}\n{\$sArticle.ordernumber|escape}{#S#}\n{\$sArticle.articleID}{#S#}\n{\$sArticle.supplier|escape}{#S#}\n{\$sArticle.name|strip_tags|strip|trim|truncate:60:\"...\":true|escape|regex_replace:\"#[^\\\\w\\\\.%&\\\\-+ öüäÖÜÄß]#iu\"}{#S#}\n{\$sArticle.articleID|category:\" > \"|escape}{#S#}\n{\$sArticle.description_long|strip_tags|strip|trim|truncate:500:\"...\":true|escape|regex_replace:\"#[^\\\\w\\\\.%&\\\\-+ öüäÖÜÄß]#iu\"}{#S#}\n{\$sArticle.image|image:1}{#S#}\n{\$productVariantImage[1]}{#S#}\n{\$productVariantImage[2]}{#S#}\n{\$productVariantImage[3]}{#S#}\n{\$productVariantImage[4]}{#S#}\n{\$productVariantImage[5]}{#S#}\n{\$sArticle.articleID|link:\$sArticle.name}?number={\$sArticle.ordernumber}{#S#}\n{if \$sArticle.pseudoprice != \'0\' &&  \$sArticle.pseudoprice > \$sArticle.price}{\$sArticle.pseudoprice|replace:\",\":\"\.\"}{else}{\$sArticle.price|replace:\",\":\"\.\"}{/if}{#S#}\n{if \$sArticle.pseudoprice != \'0\' &&  \$sArticle.pseudoprice > \$sArticle.price}{\$sArticle.price|replace:\",\":\"\.\"}{/if}{#S#}\n{\$sCurrency.currency}{#S#}\n{if \$sArticle.instock}1-3 Werktage{elseif \$sArticle.shippingtime}{\$sArticle.shippingtime} Tage{else}10 Tage{/if}{#S#}\n{if \$sArticle.weight}{\$sArticle.weight|replace:\",\":\"\.\"}{\" kg\"}{/if}{#S#}\n{\$sArticle.suppliernumber|escape}{#S#}\n{if \$sArticle.instock}Auf Lager{else}Nicht auf Lager{/if}{#S#}\n{\$sArticle.swag_attr1|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr2|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr3|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr4|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr5|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr6|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr7|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr8|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr9|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr10|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr11|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr12|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr13|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr14|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr15|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr16|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr17|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr18|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr19|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr20|strip|trim|escape}{#S#}\n{\$sArticle.additionaltext}{#S#}\n{\$sArticle.instock}{#S#}\n{if \$properties[1].name}{\$properties[1].name}:{\$properties[1].value}{else}{/if}{#S#}\n{if \$properties[2].name}{\$properties[2].name}:{\$properties[2].value}{else}{/if}{#S#}\n{if \$properties[3].name}{\$properties[3].name}:{\$properties[3].value}{else}{/if}{#S#}\n{if \$properties[4].name}{\$properties[4].name}:{\$properties[4].value}{else}{/if}{#S#}\n{if \$properties[5].name}{\$properties[5].name}:{\$properties[5].value}{else}{/if}{#S#}\n{if \$properties[6].name}{\$properties[6].name}:{\$properties[6].value}{else}{/if}{#S#}\n{if \$properties[7].name}{\$properties[7].name}:{\$properties[7].value}{else}{/if}{#S#}\n{if \$properties[8].name}{\$properties[8].name}:{\$properties[8].value}{else}{/if}{#S#}\n{/strip}\n{elseif !\$sArticle.configurator}\n{strip}\n{\$sArticle.ordernumber|escape}{#S#}\n{\$sArticle.articleID}{#S#}\n{\$sArticle.supplier|escape}{#S#}\n{\$sArticle.name|strip_tags|strip|trim|truncate:60:\"...\":true|escape|regex_replace:\"#[^\\\\w\\\\.%&\\\\-+ öüäÖÜÄß]#iu\"}{#S#}\n{\$sArticle.articleID|category:\" > \"|escape}{#S#}\n{\$sArticle.description_long|strip_tags|strip|trim|truncate:500:\"...\":true|escape|regex_replace:\"#[^\\\\w\\\\.%&\\\\-+ öüäÖÜÄß]#iu\"}{#S#}\n{\$sArticle.image|image:1}{#S#}\n{\$productVariantImage[1]}{#S#}\n{\$productVariantImage[2]}{#S#}\n{\$productVariantImage[3]}{#S#}\n{\$productVariantImage[4]}{#S#}\n{\$productVariantImage[5]}{#S#}\n{\$sArticle.articleID|link:\$sArticle.name}?number={\$sArticle.ordernumber}{#S#}\n{if \$sArticle.pseudoprice != \'0\' &&  \$sArticle.pseudoprice > \$sArticle.price}{\$sArticle.pseudoprice|replace:\",\":\"\.\"}{else}{\$sArticle.price|replace:\",\":\"\.\"}{/if}{#S#}\n{if \$sArticle.pseudoprice != \'0\' &&  \$sArticle.pseudoprice > \$sArticle.price}{\$sArticle.price|replace:\",\":\"\.\"}{/if}{#S#}\n{\$sCurrency.currency}{#S#}\n{if \$sArticle.instock}1-3 Werktage{elseif \$sArticle.shippingtime}{\$sArticle.shippingtime} Tage{else}10 Tage{/if}{#S#}\n{if \$sArticle.weight}{\$sArticle.weight|replace:\",\":\"\.\"}{\" kg\"}{/if}{#S#}\n{\$sArticle.suppliernumber|escape}{#S#}\n{if \$sArticle.instock}Auf Lager{else}Nicht auf Lager{/if}{#S#}\n{\$sArticle.swag_attr1|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr2|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr3|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr4|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr5|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr6|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr7|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr8|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr9|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr10|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr11|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr12|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr13|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr14|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr15|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr16|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr17|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr18|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr19|strip|trim|escape}{#S#}\n{\$sArticle.swag_attr20|strip|trim|escape}{#S#}\n{\$sArticle.additionaltext}{#S#}\n{\$sArticle.instock}{#S#}\n{if \$properties[1].name}{\$properties[1].name}:{\$properties[1].value}{else}{/if}{#S#}\n{if \$properties[2].name}{\$properties[2].name}:{\$properties[2].value}{else}{/if}{#S#}\n{if \$properties[3].name}{\$properties[3].name}:{\$properties[3].value}{else}{/if}{#S#}\n{if \$properties[4].name}{\$properties[4].name}:{\$properties[4].value}{else}{/if}{#S#}\n{if \$properties[5].name}{\$properties[5].name}:{\$properties[5].value}{else}{/if}{#S#}\n{if \$properties[6].name}{\$properties[6].name}:{\$properties[6].value}{else}{/if}{#S#}\n{if \$properties[7].name}{\$properties[7].name}:{\$properties[7].value}{else}{/if}{#S#}\n{if \$properties[8].name}{\$properties[8].name}:{\$properties[8].value}{else}{/if}{#S#}\n{/strip}\n{/if}";

            $sql = "SELECT MAX(id) FROM s_export";
            $id = Shopware()->Db()->fetchOne($sql);
            $id = $id + 1;
            $sql = "INSERT INTO `s_export` (`id`, `name`, `last_export`, `active`, `hash`, `show`, `count_articles`, `expiry`, `interval`, `formatID`, `last_change`, `filename`, `encodingID`, `categoryID`, `currencyID`, `customergroupID`, `partnerID`, `languageID`, `active_filter`, `image_filter`, `stockmin_filter`, `instock_filter`, `price_filter`, `own_filter`, `header`, `body`, `footer`, `count_filter`, `multishopID`, `variant_export`) VALUES
            (" . $id . ", 'convertizer', '2000-01-01 00:00:00', 1, '5ac98a759a6f392ea0065a500acf82e6', 1, 0, '2000-01-01 00:00:00', 0, 1, '0000-00-00 00:00:00', 'convertizer.csv', 2, NULL, 1, 1, '', NULL, 1, 1, 0, 1, 0, '', '" . $head . "', '" . $body . "', '', 0, 1, 2);";
            Shopware()->Db()->query($sql);
        }

    }
    
  	public static function onGetControllerPathBackend(Enlight_Event_EventArgs $args)
    {
        return dirname(__FILE__) . '/Controller/Backend/Convertizer.php';
    }
	
	
	

	public function onPostDispatchBackendIndex(Enlight_Event_EventArgs $args){
			
		$request = $args->getSubject()->Request();
		$response = $args->getSubject()->Response();
		$view = $args->getSubject()->View();
		$args->getSubject()->View()->addTemplateDir($this->Path() . 'Views/');
		$view->extendsTemplate('backend/index/convertizer/index.tpl');
		
	}
	
	public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args)
	{
        return dirname(__FILE__) . '/Controller/Frontend/Convertizer.php';
	}

    
    /**
    * register the events
    *
    */
    private function registerEvents()
    {
    	$this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Backend_Convertizer', 'onGetControllerPathBackend');
    	$this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Backend_Index','onPostDispatchBackendIndex');
		$this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_Convertizer', 'onGetControllerPathFrontend');
    }
   
}
