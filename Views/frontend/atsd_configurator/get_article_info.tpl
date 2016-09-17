
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/info"}



{* the detail *}
<div class="" style="border: 1px solid #dadae5; padding: 20px;">


    {* do we have an attribute and is it filled? *}
    {if $atsdConfiguratorConfig.articleInfoAttribute != "" && $article[$atsdConfiguratorConfig.articleInfoAttribute] != ""}

        {* header *}
        <div style="font-weight: bold;">
            {s name='ProductInfoHeader'}Produktinformationen{/s}
        </div>

        {* the attribute *}
        <p>
            {$article[$atsdConfiguratorConfig.articleInfoAttribute]}
        </p>

    {/if}



    {* do we have properties? *}
    {if count( $article.sProperties ) > 0 }

        {* container *}
        <div class="product--properties panel has--border{if $atsdConfiguratorConfig.articleLinkStatus == false} no--article-link{/if}" style="font-size: 12px;">
            <table class="product--properties-table">

                {* loop all properties*}
                {foreach $article.sProperties as $sProperty}

                    {* one row *}
                    <tr class="product--properties-row">

                        {* property label *}
                        <td class="product--properties-label is--bold">{$sProperty.name}:</td>

                        {* property content *}
                        <td class="product--properties-value">{$sProperty.value}</td>

                    </tr>

                {/foreach}

            </table>
        </div>

    {/if}

    {* link to the article? *}
    {if $atsdConfiguratorConfig.articleLinkStatus == true}

        {* link to the article *}
        <a href="{$article.linkDetails|rewrite:$article.articleName}" class="action--details btn is--small is--align-center is--icon-right block" style="width: 35%; float: right;" target="_blank">
            {s name='ButtonToProduct'}Zum Produkt{/s} <i class="icon--arrow-right"></i>
        </a>

    {/if}

    {* clear floating *}
    <div style="clear: both;"></div>

</div>
