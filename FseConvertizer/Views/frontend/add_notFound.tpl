{extends file="frontend/index/index.tpl"}
{block name="frontend_index_content"}
    <fieldset>
        <h2>{s namespace=Convertizer name='Header_notFound'}Der Artikel ist leider nicht mehr vorhanden{/s}</h2>
        {s namespace=Convertizer name='Header_notFound_redirect_home'}Sie werden in 5 Sekunden auf die Startseite weitergeleitet{/s}
        
        <script type="text/javascript">
            setTimeout(function(){ window.location.href = "{url controller='index'}"; },5000);
        </script>
        
    </fieldset>
    
{/block}