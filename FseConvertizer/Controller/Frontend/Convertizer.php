<?php
class Shopware_Controllers_Frontend_Convertizer extends Enlight_Controller_Action
{
    public function init()
    {
       $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
    }
 

    /*
     * Fügt Artikel zum Warenkorb hinzu
     * Aufruf über:
     * Convertizer/add/sku/<ordnernumber>
     * Convertizer/add/did/<Detail-id>
     * Convertizer/add/id/<Article-Id>
     * Convertizer/add/id/<Article-Id>.<Detail-id>
     * Optionaler paramter /quantity/<quantity>
     */
    public function addAction()
    {
        $ordernumber = $this->Request()->getParam('sku');
        $quantity = $this->Request()->getParam('quantity');
        if(!$quantity){$quantity = 1;}

        if($ordernumber){
            $articleID = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($ordernumber);
            if (!$articleID){
                $ordernumber = null;
            }
        } elseif ($this->Request()->getParam('did')){
            //ArticleDetailId
            $ArticleDetailId = intval($this->Request()->getParam('did'));
            $result = Shopware()->Db()->query("SELECT ordernumber FROM s_articles_details WHERE id = ".$ArticleDetailId);
            if ($result!==false) {
                $row = $result->fetch();
                $ordernumber = $row["ordernumber"];
            }
        } else {
            $articleID = $this->Request()->getParam('id');
            $ArticleDetailId = 0;

            if(strpos($articleID, ".")){
                 $ArticleDetailId = intval(substr($articleID, 1 + strpos($articleID, ".")));
                $articleID = substr($articleID, 0, strpos($articleID, "."));
            }

            $articleID = intval($articleID);

            if($ArticleDetailId){
                $result = Shopware()->Db()->query("SELECT ordernumber FROM s_articles_details WHERE id = ".$ArticleDetailId);
            } else {
                $result = Shopware()->Db()->query("SELECT ordernumber FROM s_articles_details WHERE articleID = ".$articleID);
            }

            if ($result!==false) {
                $row = $result->fetch();
                $ordernumber = $row["ordernumber"];
            }
        }

        if (!empty($ordernumber)) { 
            $this->forward("addArticle", "checkout", 'frontend', array('sAdd' => $ordernumber, 'sQuantity' => $quantity));
        } else {
            //Artikel nicht gefunden
             $this->View()->loadTemplate("frontend/add_notFound.tpl");
        }            
    }

	/*
     * Changes the url of internal convertizer Landingpage
     * Call Example:
     * Convertizer/setPageUrl/pluginkey/[pluginkey]/url/[newurl]
     * @param pluginkey, url
     */
	public function setPageUrlAction()
	{
		/* Set no frontend template needed */
		Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
		
		/* set empty response data */
		$data = array();
		
		/* get pluginkey from request */
		$pluginkey = $this->Request()->getParam('pluginkey');
		
		/* get storeowner mail*/
		$email  = Shopware()->Config()->Mail;
		
		/* encrypt params */
		
		$string = $email;
		$pass = 'c0nv3rt1z3r';
		$method = 'aes128';

		/* generate key to compare with pluginkey */
		
		$key = trim(md5(openssl_encrypt ($string, $method, $pass)));
		
		if ( $pluginkey !== $key ){
			$data['success'] = false;
			$data['error'] = true;
			$data['message'] = 'pluginkey wrong or missing';
			echo json_encode($data);
			exit();
		}else{

			/* get new landingpage url from request */
			
			$url = $this->Request()->getParam('url');
			
			if(!isset($url) || $url == ''){
				$data['error'] = true;
				$data['success'] = false;
				$data['message'] = 'no url given';
				echo json_encode($data);
			}
			
			/* get landingpage ID */
			$config = Shopware()->Plugins()->Frontend()->FseConvertizer()->Config();
			
			if (empty($config->landingpage_id)) {
            	$data['success'] = false;
				$data['error'] = true;
				$data['message'] = 'landingpage seems not to exist';
				echo json_encode($data);
				exit();
        	}else{
        		
        		$id = $config->landingpage_id;
				
				
        		$checksql = "SELECT * FROM s_cms_static WHERE id = $id";
				$query = Shopware()->Db()->fetchRow($checksql, array(1));
				
				/* if the landingpage already exists */
		        if ($query) {
		        	
					/* Update the landingpage url */
		            $sql = 'UPDATE `s_cms_static` 
		                    SET `description` = "' . $url . '" 
		                    WHERE `id` = ' . $id . ';';
					Shopware()->Db()->query($sql);
					$data['success'] = true;
					$data['error'] = false;
					$data['message'] = 'page url has been changed to ' . $url;
					
					
					/* Refresh SEO Index */
					$org_path = 'sViewport=custom&sCustom=' . $config->landingpage_id;
					
					$sql = 'UPDATE `s_core_rewrite_urls` 
		                    SET `main` = 0
		                    WHERE `org_path` = "' . $org_path . '";';
							
					Shopware()->Db()->query($sql);
					
					$sql = "SELECT MAX(id) FROM s_core_rewrite_urls";
		            $id = Shopware()->Db()->fetchOne($sql);
		            $id = $id + 1;
		
					$shop_id = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('default' => true))->getID();
		
					/* check if there already was a page with this url */
					
					$checksql = "SELECT * FROM s_core_rewrite_urls WHERE path = '" . $url . "'";
        			$query = Shopware()->Db()->fetchRow($checksql, array(1));
					
					 if ($query) {
            				
						$sql = 'UPDATE `s_core_rewrite_urls` 
	                    	SET `main` = 1
		                    WHERE `path` = "' . $url . '";';
							
						Shopware()->Db()->query($sql);	

            			$data['error'] = false;
						$data['success'] = true;
						$data['message'] = 'page url has been changed back to ' . $url;
						
						/* return json */
						echo json_encode($data);
						exit();
					
					 }else{
					 	/* insert a new seo index */
			            $sql = "INSERT INTO `s_core_rewrite_urls` (`id`, `org_path`, `path`, `main`, `subshopID`) VALUES
			            (" . $id . ", '" . $org_path . "', '" . $url . "' , 1 , " . $shop_id . ");";
			           
			            Shopware()->Db()->query($sql);
						
						/* return json */
			        	echo json_encode($data);
						exit();
					 }
					
				/* if the page does not exist or an error occured */
		        } else {
		        	$data['error'] = true;
					$data['success'] = false;
					$data['message'] = 'Error setting page url';
					
					/* return json */
		        	echo json_encode($data);
					exit();
				}
        	}
			
		}

	}
         
}