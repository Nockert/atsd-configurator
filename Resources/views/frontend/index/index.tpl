
{* file to extend *}
{extends file="parent:frontend/index/index.tpl"}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configuration"}



{* append our javascript *}
{block name='frontend_index_header_javascript_jquery'}

    {* only if active *}
    {if $atsdConfiguratorShopStatus == true}

        {* our plugin configuration *}
        <script type="text/javascript">

            {* javascript variables *}
            var atsdConfiguratorConfiguration = {
                priceTemplate:            '{"12345.99"|currency|escape:"quotes"} {s name="Star" namespace="frontend/listing/box_article"}{/s}',
                priceDefault:             '<span class="price--content content--default">#price#</span>',
                pricePseudo:              '<span class="price--content content--default">#price#</span><span class="price--discount-icon"><i class="icon--percent2"></i></span><span class="content--discount"><span class="price--line-through">#pseudoPrice#</span> <span class="price--discount-percentage">(#rebate#% gespart)</span></span>',
                surchargePre:             '{s name="SurchargePre"}+ {/s}',
                surchargePost:            '{s name="SurchargePost"}%{/s}',
                stockAvailable:           '<link itemprop="availability" href="http://schema.org/InStock"><p class="delivery--information"><span class="delivery--text delivery--text-available"><i class="delivery--status-icon delivery--status-available"></i>{s name="stockAvailable"}Sofort versandfertig, Lieferzeit ca. 1-3 Werktage{/s}</span></p>',
                stockNotAvailable:        '<link itemprop="availability" href="http://schema.org/LimitedAvailability"><p class="delivery--information"><span class="delivery--text delivery--text-not-available"><i class="delivery--status-icon delivery--status-not-available"></i>{s name="stockNotAvailable"}Die Konfiguration ist nicht vollständig ab Lager verfügbar{/s}</span></p>',
                selectorButtonSelected:   '{s name="SelectorButtonSelected"}Ausgewählt{/s}',
                selectorButtonSelectable: '{s name="SelectorButtonSelectable"}Wählen{/s}',
                weightUnit:               '{s name="BaseInfoWeightUnit" namespace="frontend/detail/index"}kg{/s}',
                articleInfoAjaxUrl:       '{url controller="AtsdConfigurator" action="getArticleInfo" articleId="__articleId__"}',
                infoModalTitle:           '{s name="InfoModalTitle"}Produktinformationen{/s}'
            };

        </script>

    {/if}

    {* smarty parent *}
    {$smarty.block.parent}

{/block}
