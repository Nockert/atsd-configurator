
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/info"}



{* the detail *}
<div class="atsd-configurator-quickview"
     data-atsd-configurator-quickview="true" xmlns="http://www.w3.org/1999/html">

    {* do we have scaled prices? *}
    {if $article.sBlockPrices}

        {* header *}
        <div style="font-weight: bold;">
            {s name='ProductBlockPricesHeader'}Staffelpreise (ohne Rabatt){/s}
        </div>

        {* just for default details css *}
        <div class="product--details">

            {* include default block prices *}
            {include file="frontend/detail/block_price.tpl" sArticle=$article}

        </div>

    {/if}

    {* should show quickview? *}
    {if isset($quickview) && $quickview == true }
        <div class="atsd-configurator-quickview-images {if $showDescription == false && $showAttributes == false} only-images {/if}">
            <div class="atsd-configurator-quckview-slider-thumbnails">

                {* Thumbnail - Main image *}
                {if $article.image.thumbnails}

                    {$alt = $article.articleName|escape}

                    {if $article.image.description}
                        {$alt = $article.image.description|escape}
                    {/if}

                    <div class="atsd-configurator-quickview-thumbnail-div" data-atsd-quickview-thumbnail="{$article.image.thumbnails[1].sourceSet}">

                        <img srcset="{$article.image.thumbnails[0].sourceSet}"
                             alt="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt}"
                             title="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt|truncate:160}"
                             class="atsd-configurator-quickview-thumbnail-image"
                             />

                    </div>

                {/if}

                {* Thumbnails *}
                {foreach $article.images as $image}
                    {if $image.thumbnails}

                        {$alt = $sArticle.articleName|escape}

                        {if $image.description}
                            {$alt = $image.description|escape}
                        {/if}

                        <div class="atsd-configurator-quickview-thumbnail-div" data-atsd-quickview-thumbnail="{$image.thumbnails[1].sourceSet}">

                            <img srcset="{$image.thumbnails[0].sourceSet}"
                                 alt="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt}"
                                 title="{s name="DetailThumbnailText" namespace="frontend/detail/index"}{/s}: {$alt|truncate:160}"
                                 class="atsd-configurator-quickview-thumbnail-image"
                                 />

                        </div>

                    {/if}
                {/foreach}
            </div>

            {* article image *}
            <div class="atsd-configurator-quickview-image">

                {if $article.image.thumbnails}
                    <img class="atsd-configurator--quickview-images-image" srcset="{$article.image.thumbnails[1].sourceSet}" alt="{$alt}"/>
                {else}

                    <img class="atsd-configurator--quickview-images-image" src="{link file='frontend/_public/src/img/no-picture.jpg'}" alt="{$alt}"/>
                {/if}


                {* link to the article? *}
                {if $atsdConfiguratorConfig.articleLinkStatus == true}


                        {* link to the article *}
                <a href="{$article.linkDetails}" class="action--details btn is--small is--align-center is--icon-right block" style=" width: 35%; float: right;" target="_blank">
                    {s name='ButtonToProduct'}Zum Produkt{/s} <i class="icon--arrow-right"></i>
                </a>

                    <div style="clear: both"></div>

                {/if}
            </div>
        </div>

        {* show article attributes/ description? *}
        {if $showDescription == true || $showAttributes == true}

        <div class="atsd-configurator-quickview-additional">

            {* do we have an attribute and is it filled? *}
            {if ($atsdConfiguratorConfig.articleInfoAttribute != "" && $article[$atsdConfiguratorConfig.articleInfoAttribute] != "" ) || ( $quickview ==true && ($showDescription == true || $showAttributes == true ) ) }

                {* header *}
                <div style="font-weight: bold; margin-bottom: 8px;">
                    {s name='ProductInfoHeader'}Produktinformationen{/s}
                </div>


                {* the attribute *}
                <p>
                    {$article[$atsdConfiguratorConfig.articleInfoAttribute]}
                </p>

            {/if}

            {* show description? *}
            {if isset($showDescription) && $showDescription == true}

                <div class="">

                    {$article.description_long}

                </div>

            {/if}

            {* show attributes? *}
            {if isset($showAttributes) and ($showAttributes == true) }
                {* do we have properties? *}
                {if count( $article.sProperties ) > 0 }

                    {* header *}
                    <div style="font-weight: bold; margin-bottom: 14px;">
                        {s name='ProductPropertiesHeader'}Eigenschaften{/s}
                    </div>

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
                        {$article.image.description|escape}
                    </div>
                {/if}

            {/if}
        {/if}


        </div>
    {/if}

    {* quickview is not selected? *}
    {if !isset($quickview) || $quickview == false }

        {if count( $article.sProperties ) > 0 }

            {* header *}
            <div style="font-weight: bold; margin-bottom: 14px;">
                {s name='ProductPropertiesHeader'}Eigenschaften{/s}
            </div>

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
                {$article.image.description|escape}
            </div>

        {/if}
    {/if}



    {* clear floating *}
    <div style="clear: both;"></div>

</div>

