
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configurator/template/slider"}



{* our main container *}
<div class="atsd-configurator--article--slider panel has--border is--rounded">

    {* header *}
    <div class="panel--title is--underline">

        {* element description*}
        {$element.description}

        {* is this a mandatory element? *}
        {if $element.mandatory == true}

            {* output a message *}
            <span style="float: right;">
                {s name="IsMandatory"}Pflichtfeld{/s}
            </span>

        {/if}

    </div>



    {* article slider container *}
    <div class="article--slider--container" style="">

        {* shopware product slider *}
        <div class="product-slider" data-atsd-configurator-product-slider="true">
            <div class="product-slider--container">

                {* is this an optional fieldset with a radio selection *}
                {if $element.multiple == false && $element.mandatory == false && $atsdConfiguratorConfigNoChoicePosition == 0}

                    {* include our no-choice option *}
                    {include file="frontend/atsd-configurator/elements/no-choice/template_{$element.template.key}.tpl" element=$element elementArticle=$elementArticle fieldset=$fieldset configurator=$configurator selection=$selection}

                {/if}



                {* loop every article *}
                {foreach $element.articles as $elementArticle}

                    {* get short variable for article *}
                    {assign var="article" value=$elementArticle.article}

                    {* set the name by radio or checkbox *}
                    {assign var="name" value="configurator-{if $element.multiple == true}checkbox{else}radio{/if}-fieldset-{$fieldset.id}-element-{$element.id}{if $element.multiple == true}[]{/if}"}



                    {* every calculated price *}
                    {assign var="prices" value=[]}

                    {* loop every available price and save it *}
                    {foreach $article->getPrices() as $price}

                        {assign var="currentPrice" value=[]}

                        {append var='currentPrice' index='from' value=$price->getRule()->getFrom()|intval}
                        {append var='currentPrice' index='to' value={$price->getRule()->getTo()|intval}}
                        {append var='currentPrice' index='price' value=$price->getCalculatedPrice()|floatval}

                        {$prices[] = $currentPrice}

                    {/foreach}



                    {* the item container *}
                    <div class="product-slider--item"
                         data-atsd-configurator-article="true"
                         data-atsd-configurator-fieldset-id="{$fieldset.id}"
                         data-atsd-configurator-element-id="{$element.id}"
                         data-atsd-configurator-article-id="{$elementArticle.id}"
                         data-atsd-configurator-article-price="{$article->getCheapestPrice()->getCalculatedPrice()}"
                         data-atsd-configurator-article-prices='{$prices|json_encode}'
                         data-atsd-configurator-article-quantity="{$elementArticle.quantity}"
                         data-atsd-configurator-article-name="{$article->getName()|escape}"
                         data-atsd-configurator-article-stock="{$article->getStock()}"
                         data-atsd-configurator-article-weight="{$article->getWeight()}"
                         data-atsd-configurator-article-image="{if is_object( $article->getCover() )}{$article->getCover()->getThumbnail( 0 )->getSource()}{else}{link file='frontend/_public/src/img/no-picture.jpg'}{/if}"
                         data-atsd-configurator-article-selector="slider"
                    >

                        {* default shopware product box *}
                        <div class="product--box box--slider">

                            {* inner container *}
                            <div class="box--content is--rounded">

                                {* product details *}
                                <div class="product--info">

                                    {* image *}
                                    <div class="product--image">

                                        {* container for the image *}
                                        <span class="image--element">
                                            <span class="image--media">

                                                {* do we have an image? *}
                                                {if is_object( $article->getCover() )}

                                                    {* show it *}
                                                    <img srcset="{$article->getCover()->getThumbnail( 0 )->getSource()}"
                                                         alt="{$article->getName()|escape}"
                                                         title="{$article->getName()|escape|truncate:25:""}" />

                                                {else}

                                                    {* no image available *}
                                                    <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                                                         alt="{$article->getName()|escape}"
                                                         title="{$article->getName()|escape|truncate:25:""}" />

                                                {/if}

                                            </span>
                                        </span>

                                    </div>

                                    {* article name - with link or without *}
                                    {if $atsdConfiguratorConfigArticleLinkStatus == true}

                                        {* article name with link *}
                                        <a href="{url controller="detail" action="index" sArticle=$article->getId()}"
                                           class="product--title"
                                           target="_blank"
                                           title="{$article->getName()|escape}">

                                            {* prepend quantity if > 1 *}
                                            {if $elementArticle.quantity > 1}<span class="article--quantity">({$elementArticle.quantity}x)</span>{/if}

                                            {* the article name *}
                                            {$article->getName()|truncate:50}

                                        </a>

                                    {else}

                                        {* article name without link *}
                                        <span style="cursor: pointer;" class="product--title">

                                            {* prepend quantity if > 1 *}
                                            {if $elementArticle.quantity > 1}<span class="article--quantity">({$elementArticle.quantity}x)</span>{/if}

                                            {* the article name *}
                                            {$article->getName()|truncate:50}

                                        </span>

                                    {/if}

                                    {* product price *}
                                    <div class="product--price-info">
                                        <div class="product--price">
                                            <span class="price--default is--nowrap">

                                                {* output delivery status depending on available stock *}
                                                <i class="delivery--status-icon delivery--status-{if ( $article->getStock() / $elementArticle.quantity ) < 1}not-{/if}available"></i>

                                                {* and the price *}
                                                <span class="price--placeholder">
                                                    &nbsp;
                                                    {* {( $article->getCheapestPrice()->getCalculatedPrice() * $elementArticle.quantity * ( ( 100 - $configurator.rebate ) / 100 ) )|currency} {s name="Star"}*{/s} *}
                                                </span>

                                            </span>
                                        </div>
                                    </div>

                                    {* radio or checkbox selector - hide it and use the button *}
                                    <div style="display: none;">

                                        {* hidden input field *}
                                        <input type="{if $element.multiple == true}checkbox{else}radio{/if}"
                                               name="{$name}"
                                               {if in_array( $elementArticle.id, $selection )}checked="checked"{/if}
                                               data-atsd-configurator-selector="true"
                                               data-atsd-configurator-selector-fieldset-id="{$fieldset.id}"
                                               data-atsd-configurator-selector-element-id="{$element.id}"
                                               data-atsd-configurator-selector-article-id="{$elementArticle.id}"
                                        />

                                    </div>

                                    {* visual selector *}
                                    <div style="margin-top: 12px;">

                                        {* show the button *}
                                        <button class="btn is--align-center {if in_array( $elementArticle.id, $selection )}is--primary{/if} {if $element.multiple == true}is--multiple{else}is--not-multiple{/if}"
                                                style="width: 85%;"
                                                data-atsd-configurator-selector-button="true"
                                                data-atsd-configurator-selector-button-article-id="{$elementArticle.id}"
                                                data-atsd-configurator-selector-button-element-id="{$element.id}"
                                                data-atsd-configurator-selector-button-is-multiple="{if $element.multiple == true}true{else}false{/if}"
                                        >

                                            {* button text *}
                                            {if in_array( $elementArticle.id, $selection )}
                                                {s name="SelectorButtonSelected" namespace="frontend/AtsdConfigurator/configuration"}Ausgewählt{/s}
                                            {else}
                                                {s name="SelectorButtonSelectable" namespace="frontend/AtsdConfigurator/configuration"}Wählen{/s}
                                            {/if}

                                        </button>

                                        {* info button *}
                                        <button class="btn is--align-center"
                                                style="width: 10%; padding-left: 0; padding-right: 0;"
                                                data-atsd-configurator-selector-info-button="true"
                                                data-atsd-configurator-selector-info-button-article-id="{$elementArticle.id}"
                                        >

                                            {* button value *}
                                            {s name="SelectorInfoButtonValue"}i{/s}

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                {/foreach}



                {* is this an optional fieldset with a radio selection *}
                {if $element.multiple == false && $element.mandatory == false && $atsdConfiguratorConfigNoChoicePosition == 1}

                    {* include our no-choice option *}
                    {include file="frontend/atsd-configurator/elements/no-choice/template_{$element.template.key}.tpl" element=$element elementArticle=$elementArticle fieldset=$fieldset configurator=$configurator selection=$selection}

                {/if}

            </div>
        </div>

    </div>

</div>


