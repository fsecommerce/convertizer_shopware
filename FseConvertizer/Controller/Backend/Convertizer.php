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

class Shopware_Controllers_Backend_Convertizer extends Shopware_Controllers_Backend_ExtJs
{
	
	/**
    * get all data to fill the extjs store
    *
	* @return customer_id and if customer exists at all
    */
	public function getStoreDataAction(){
		
		$data = array();
		
		$_shopware_config = Shopware()->Config();
		
		$data['storeownermail'] = $_shopware_config->Mail;
		
		$builder = Shopware()->Models()->createQueryBuilder();
		$builder->select(array('config'))
         ->from('Shopware\Models\Shop\Shop', 'config')
		 ->setFirstResult(0)
         ->setMaxResults(1);
		 
	  	$query = $builder->getQuery();
		
		$result = $query->getOneOrNullResult(
      		\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY
   		);
		
		/* get the landingpage url */
		$config = Shopware()->Plugins()->Frontend()->FseConvertizer()->Config();
		if($config->landingpage_id){
			$_langinpageId  = $config->landingpage_id;
			$checksql = "SELECT * FROM s_cms_static WHERE id = '" . $_langinpageId . "'";
			$query = Shopware()->Db()->fetchRow($checksql, array(1));
			$data['landingpageurl'] = $query['description'];
		}else{
			$data['landingpageurl'] = 'convertizer';
		}

		$data['storehost'] = $result['host']; 
		
		
		$checkCustomerExistsUrl = 'https://sandbox.convertizer.com/thor_api/index/getAccountExistbyEmail/email/' . urlencode($data['storeownermail']);
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $checkCustomerExistsUrl);
	    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	    $output = curl_exec($ch);
	    curl_close($ch);
		
		$result = json_decode($output,true);
	
		$data['customerexists'] = $result['exists'];
		$data['customer_id'] 	= $result['customer_id'];
		$data['is_remote'] 		= $result['is_remote'];
		$data['trackingid'] 	= $result['trackingid'];
		$data['feed_url'] 		= $result['feed_url'];
		
		/* check if is not remote and if not set tracking id */
		
		if ( $data['is_remote'] !== 1 && $data['trackingid'] !== ''){
			$shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('default' => true));
	    	$pluginManager  = Shopware()->Container()->get('shopware.plugin_Manager');
	    	$plugin = $pluginManager->getPluginByName('FseConvertizer');
	    	$pluginManager->saveConfigElement($plugin, 'trackingId', $data['trackingid'], $shop);
			$pluginManager->saveConfigElement($plugin, 'customerId', $data['customer_id'], $shop);
		}
		
		/* if there is no feed url set yet in convertizer account*/
		
		/* get Convertizer Feed URL */
		
		$checksql = "SELECT * FROM s_export WHERE name = 'convertizer'";
		$query = Shopware()->Db()->fetchRow($checksql, array(1));
		$feedurl = urlencode('http://' . $data['storehost'] . '/backend/export/index/convertizer.csv?feedID=' . $query['id'] . '&hash=' . $query['hash']);
		
		if ( $data['feed_url'] == '' ){
			
			$data['feed_url'] = $feedurl;
			
			$updateData = array();
			$updateData['feed_url'] = $feedurl;
			
			$updateData = urlencode(json_encode($updateData));
			
			/* update Account */
			
			$updateAccountUrl  = 'https://sandbox.convertizer.com/thor_api/index/updateAccountRemotely/';
			$updateAccountUrl .= 'customer_id/' . $data['customer_id'] . '/update_data/' . $updateData;
			
			
	    	$ch_update = curl_init();
		    curl_setopt($ch_update, CURLOPT_URL, $updateAccountUrl);
		    curl_setopt($ch_update, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		    curl_setopt($ch_update, CURLOPT_HEADER, 0);
		    curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch_update, CURLOPT_TIMEOUT, 60);
		    $update_output = curl_exec($ch_update);
		    curl_close($ch_update);
		}

		/* Set Convertizer product export uRL to return it to backend plugin overview */

		$data['feed_url'] = urldecode($feedurl);
		
		$this->View()->assign(array('success' => true, 'data' => $data));
		
	}
	
	/**
    * create the convertizer Account
    *
	* @return success or error with details
    */
	public function createAccountAction(){
		
		
		$email 		= $this->Request()->getParam('email');
		$shopsystem = $this->Request()->getParam('shopsystem');
		$shopurl	= $this->Request()->getParam('shopurl');
		$agb		= $this->Request()->getParam('agb');
		
		
		$string = $email;
		$pass = 'c0nv3rt1z3r';
		$method = 'aes128';

		$key = md5(openssl_encrypt ($string, $method, $pass));
		
		/* get Convertizer Feed URL */
		
		$checksql = "SELECT * FROM s_export WHERE name = 'convertizer'";
        $query = Shopware()->Db()->fetchRow($checksql, array(1));
		$feedurl = urlencode('http://' . $shopurl . '/backend/export/index/convertizer.csv?feedID=' . $query['id'] . '&hash=' . $query['hash']);
		
		$mapperconfig = urlencode('{"settings":{"0":"id","3":"title","5":"description","6":"image_link","7":"additional_image_link_1","8":"additional_image_link_2","9":"additional_image_link_3","10":"additional_image_link_4","11":"additional_image_link_5","12":"link","13":"price","14":"sale_price","16":"delivery_time","19":"availability","42":"keywords"},"variants":{"parent":"0","separator":"."},"positions":{"40":"40"},"facets":{"2":"Hersteller","4":"Kategorie"},"customphp":""}');
		
	
		$createAccountUrl  = 'https://sandbox.convertizer.com/thor_api/index/createAccountRemotely/';
		$createAccountUrl .= 'email/' . urlencode($email) . '/shopsystem/' . urlencode($shopsystem) . '/shopurl/' . urlencode('http://' . $shopurl) . '/feed_url/' . $feedurl . '/agb/' . $agb . '/pluginkey/'. $key . '/mapperconfig/' . $mapperconfig;
    	
    	$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $createAccountUrl);
	    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	    $output = curl_exec($ch);
	    curl_close($ch);
 
 		$outputData = json_decode($output,true);

		$response = $output;
		
		if ( $outputData['success']  ){
			$this->createCampaignAction($outputData);
			$this->View()->assign(array('success' => true, 'data' => $output, 'message' => $outputData['message']));
		}else{
			$this->View()->assign(array('error' => true, 'data' => $output, 'message' => $outputData['message']));
		}
	
	}

	/**
    * create the campaign
    *
	* @return success or error with details
    */
	public function createCampaignAction($campaignData = null)
	{
		
		$createCampaignUrl = 'https://sandbox.convertizer.com/thor_api/index/createCampaignRemotely';
		
		$tracking_id = 'notset';
		
		if ( $campaignData  ){
			$customer_id = $campaignData['data']['customerId'];
			$tracking_id = $campaignData['data']['trackingId'];
		}
		
		if ($customer_id && $customer_id != '') {
			$createCampaignUrl .= '/customerId/' . $customer_id;
			
			$chl = curl_init();
		    curl_setopt($chl, CURLOPT_URL, $createCampaignUrl);
		    curl_setopt($chl, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		    curl_setopt($chl, CURLOPT_HEADER, 0);
		    curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($chl, CURLOPT_TIMEOUT, 60);
		    $output = curl_exec($chl);
		    curl_close($chl);
			
			$result = json_decode($output,true);
			$this->indexCampaign($customer_id , $result['data']['campaignId']);
		}
		

		$shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('default' => true));
    	$pluginManager  = Shopware()->Container()->get('shopware.plugin_Manager');
    	$plugin = $pluginManager->getPluginByName('FseConvertizer');
    	$pluginManager->saveConfigElement($plugin, 'trackingId', $tracking_id, $shop);
		$pluginManager->saveConfigElement($plugin, 'customerId', $customer_id, $shop);
		
		$this->createCmsPage($tracking_id);
		
		return true; 

	}

	/**
    * create the landingpage
	* 
    * @param $_tracking_id
    */
	public function createCmsPage($_tracking_id)
    {
        $checksql = "SELECT * FROM s_cms_static WHERE description = 'Convertizer'";
        $query = Shopware()->Db()->fetchRow($checksql, array(1));
        if ($query) {
            return true;
        } else {

            $content = "<div id=\"convertizerContainer\" data-trackingid=\"" . $_tracking_id ."\"></div><script type=\"text/javascript\">
                   (function()
                    {var js = document.createElement('script');
                    js.type = 'text/javascript';
        	        js.async = true;
        	        js.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'sandbox.convertizer.com/static/api.js';
        	        var tag = document.getElementsByTagName('body')[0]; tag.parentNode.insertBefore(js, tag); })(); 
                    </script>";
            $content = addslashes($content);

            $sql = "SELECT MAX(id) FROM s_cms_static";
            $id = Shopware()->Db()->fetchOne($sql);
            $id = $id + 1;

            $sql = "INSERT INTO `s_cms_static` (`id`, `tpl1variable`, `tpl1path`, `tpl2variable`, `tpl2path`, `tpl3variable`, `tpl3path`, `description`, `html`, `grouping`, `position`, `link`, `target`, `parentID`, `page_title`, `meta_keywords`, `meta_description`) VALUES
            (" . $id . ", '', '', '', '', '', '', 'Convertizer', '" . $content . "', 'gLeft|gBottom2', 6, '', '', 0, '', '', '');";
            Shopware()->Db()->query($sql);
            
            
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('default' => true));
	    	$pluginManager  = Shopware()->Container()->get('shopware.plugin_Manager');
	    	$plugin = $pluginManager->getPluginByName('FseConvertizer');
	    	$pluginManager->saveConfigElement($plugin, 'landingpage_id', $id, $shop);
        }
    }

	/**
    * index the product data in solr
	*
    * @param $customerId, $campaignId
    */
	public function indexCampaign($customerId, $campaignId){
		
		$indexCampaignUrl  = 'https://sandbox.convertizer.com/thor_api/index/indexCampaignRemotely';
		
		if ($customerId && $customerId != '' && $campaignId && $campaignId != '') {
			$indexCampaignUrl .= '/customerId/' . $customerId . '/campaignId/' . $campaignId;
			
			$chl = curl_init();
		    curl_setopt($chl, CURLOPT_URL, $indexCampaignUrl);
		    curl_setopt($chl, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		    curl_setopt($chl, CURLOPT_HEADER, 0);
		    curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($chl, CURLOPT_TIMEOUT, 60);
		    $output = curl_exec($chl);
		    curl_close($chl);
		}
		
		$outputData = json_decode($output,true);
		
		if ( $outputData['success']  ){
			$this->View()->assign(array('success' => true, 'data' => $output, 'message' => $outputData['message']));
		}else{
			$this->View()->assign(array('error' => true, 'data' => $output, 'message' => $outputData['message']));
		}
	}


    public function init()
    {
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
        parent::init();
    }
    
    public function indexAction()
    {
        $this->View()->loadTemplate("backend/convertizer/app.js");
    }
    
}