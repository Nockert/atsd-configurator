
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

                    {* quantity selector name *}
                    {assign var="quantitySelectorName" value="configurator-fieldset-{$fieldset.id}-element-{$element.id}-article-{$elementArticle.id}-quantity"}



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



                    {* currently selected? *}
                    {assign var="isSelected" value=( in_array( $elementArticle.id, array_keys( $selection ) ) )}

                    {* default selected quantity based on default quantity or selection *}
                    {assign var="selectedQuantity" value={"{if $isSelected == true}{$selection[$elementArticle.id]}{else}{$elementArticle.quantity}{/if}"|intval}}

                    {* do we want a select element *}
                    {assign var="hasSelectableQuantity" value=$elementArticle.quantitySelect}

                    {* do we want to show quantity *}
                    {assign var="outputQuantity" value=( ( $elementArticle.quantity > 1 ) or ( $hasSelectableQuantity == true ) )}



                    {* the item container *}
                    <div class="product-slider--item{if $outputQuantity == true} has--quantity-output{/if}"
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

                                    {* article name with modal popup *}
                                    <span style="cursor: pointer;" class="product--title"
                                          data-atsd-configurator-selector-info-button="true"
                                          data-atsd-configurator-selector-info-button-article-id="{$elementArticle.id}"
                                    >

                                        {* the article name *}
                                        {$article->getName()|truncate:50}

                                    </span>

                                    {* product price *}
                                    <div class="product--price-info">
                                        <div class="product--price">
                                            <span class="price--default is--nowrap">

                                                {* output delivery status depending on available stock *}
                                                <span class="delivery-status--placeholder">&nbsp;</span>

                                                {* and the price *}
                                                <span class="price--placeholder">&nbsp;</span>

                                            </span>
                                        </div>
                                    </div>

                                    {* radio or checkbox selector - hide it and use the button *}
                                    <div style="display: none;">

                                        {* hidden input field *}
                                        <input type="{if $element.multiple == true}checkbox{else}radio{/if}"
                                               name="{$name}"
                                               {if $isSelected == true}checked="checked"{/if}
                                               data-atsd-configurator-selector="true"
                                               data-atsd-configurator-selector-fieldset-id="{$fieldset.id}"
                                               data-atsd-configurator-selector-element-id="{$element.id}"
                                               data-atsd-configurator-selector-article-id="{$elementArticle.id}"
                                        />

                                    </div>

                                    {* visual selector *}
                                    <div style="margin-top: 12px;" class="product--visual-selector">



                                        {* do we went to output any form of quantity? *}
                                        {if $outputQuantity == true}

                                            {* do we want to show a select field? *}
                                            {if $hasSelectableQuantity == true}

                                                {* set variables *}
                                                {assign var="step" value={"{if $elementArticle.quantityMultiply == true}{$elementArticle.quantity|intval}{else}1{/if}"}|intval}
                                                {assign var="min" value=$step}
                                                {assign var="max" value={"{if $atsdConfiguratorConfigSaleType == 0}99{else}{if $atsdConfiguratorConfigSaleType == 1}{if $article->isCloseouts() == true}{$article->getStock()}{else}99{/if}{else}{$article->getStock()}{/if}{/if}"}|intval}
                                                {assign var="selected" value={$selectedQuantity}}

                                                {if $atsdConfiguratorIsShopware53 == true}<div class="select-field quantity-select">{/if}

                                                {* create the select *}
                                                <select name="{$quantitySelectorName}"
                                                        data-atsd-configurator-article-quantity-selector="true"
                                                        data-atsd-configurator-article-quantity-selector-article-id="{$elementArticle.id}"
                                                >

                                                    {* loop for options *}
                                                    {for $i=$min to $max step $step}
                                                        <option value="{$i}"{if $i == $selected} selected{/if}>
                                                            {$i}x
                                                        </option>
                                                    {/for}

                                                </select>

                                                {if $atsdConfiguratorIsShopware53 == true}</div>{/if}

                                            {else}

                                                {* append the quantity as hidden field *}
                                                <input type="hidden"
                                                       name="{$quantitySelectorName}"
                                                       value="{$selectedQuantity}"
                                                       data-atsd-configurator-article-quantity-selector="true"
                                                       data-atsd-configurator-article-quantity-selector-article-id="{$elementArticle.id}"
                                                />

                                                {* just show the quantity as button *}
                                                <button class="quantity--button btn is--align-center">
                                                    {$elementArticle.quantity}x
                                                </button>

                                            {/if}

                                        {else}

                                            {* append the quantity as hidden field *}
                                            <input type="hidden"
                                                   name="{$quantitySelectorName}"
                                                   value="{$selectedQuantity}"
                                                   data-atsd-configurator-article-quantity-selector="true"
                                                   data-atsd-configurator-article-quantity-selector-article-id="{$elementArticle.id}"
                                            />

                                        {/if}



                                        {* show the button *}
                                        <button class="selector--button btn is--align-center {if $isSelected == true}is--primary{/if} {if $element.multiple == true}is--multiple{else}is--not-multiple{/if}"
                                                style=""
                                                data-atsd-configurator-selector-button="true"
                                                data-atsd-configurator-selector-button-article-id="{$elementArticle.id}"
                                                data-atsd-configurator-selector-button-element-id="{$element.id}"
                                                data-atsd-configurator-selector-button-is-multiple="{if $element.multiple == true}true{else}false{/if}"
                                        >

                                            {* button text *}
                                            {if $isSelected == true}
                                                {s name="SelectorButtonSelected" namespace="frontend/AtsdConfigurator/configuration"}Ausgewählt{/s}
                                            {else}
                                                {s name="SelectorButtonSelectable" namespace="frontend/AtsdConfigurator/configuration"}Wählen{/s}
                                            {/if}

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


