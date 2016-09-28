{block name='frontend_index_header_javascript' append}  
    {if $sBasket.content && $sOrderNumber}
        {assign var="ConvertizerValue" value=($sBasket['AmountNetNumeric']-$sBasket['sShippingcostsNet'])}
        <script type="text/javascript">
            /* Convertizer Tracking Code*/
            //<![CDATA[
                    var trackingid 	= '{$ConvertizerTrackingId|escape:'javascript'}'; //CUSTOMER TRACKING ID
                    var amount		= '{$ConvertizerValue|replace:',':'.'|round:2}'; //GRAND TOTAL - TAX AMOUNT - SHIPPING AMOUNT
                    var ordertype	= 1; //ORDER TYPE
                    var orderid = '{$sOrderNumber}'; //ORDER ID
                    (function(){
                            var js = document.createElement('script');
                                    js.type = 'text/javascript';
                                    js.async = true;
                                    js.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'app.convertizer.com/static/convertizer.js';
                            var tag = document.getElementsByTagName('script')[0];
                            tag.parentNode.insertBefore(js, tag);
                    })();
            /* Convertizer Tracking Code*/
        </script>
    {/if}
{/block}