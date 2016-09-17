
{* file to extend *}
{extends file='parent:frontend/listing/product-box/box-basic.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/product-box/box-basic"}



{* check if we have a configurator and replace the default price *}
{block name='frontend_listing_box_article_price_info'}

    {* do we have a configurator for this article? *}
    {if isset( $sArticle.attributes.atsd_configurator ) && is_object( $sArticle.attributes.atsd_configurator ) && $sArticle.attributes.atsd_configurator->get( "hasConfigurator" ) == true}

        {* get the configurator *}
        {assign var="configurator" value=$sArticle.attributes.atsd_configurator->get( "defaultConfigurator" )}

        {* reset the article *}
        {$sArticle.price             = $configurator.price}
        {$sArticle.pseudoprice       = $configurator.pseudoPrice}
        {$sArticle.instock           = $configurator.stock}
        {$sArticle.has_pseudoprice   = $configurator.hasPseudoPrice}
        {$sArticle.priceStartingFrom = true}

    {/if}



    {* just the parent *}
    {$smarty.block.parent}

{/block}



