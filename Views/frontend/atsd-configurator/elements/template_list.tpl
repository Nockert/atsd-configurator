
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configurator/template/list"}



{* our main container *}
<div class="atsd-configurator--article--list panel has--border is--rounded">

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



    {* article list container *}
    <div class="article--list--container" style="">

        {* inner container for floating *}
        <div class="block-group">

            {* container for image *}
            <div class="block article--list--image--container element--image--container"
                 data-atsd-configurator-element-image="true"
                 data-atsd-configurator-element-id="{$element.id}"
                 data-atsd-configurator-element-switch-image-on-selection="{if $element.multiple == true}false{else}true{/if}"
                 data-atsd-configurator-element-default-image="{if $element.mediaFile != ""}{$element.mediaFile}{else}{link file='frontend/_public/src/img/no-picture.jpg'}{/if}"
            >

                {* inner container *}
                <div style="margin-right: 20px; border: 1px solid black; text-align: center; padding: 10px;">

                    {* the image if we have one *}
                    <img src="{if $element.mediaFile != ""}{$element.mediaFile}{else}{link file='frontend/_public/src/img/no-picture.jpg'}{/if}" style="margin: auto;" />

                </div>

            </div>



            {* container for all articles *}
            <div class="block article--list--articles--container">

                {* is this an optional fieldset with a radio selection *}
                {if $element.multiple == false && $element.mandatory == false && $atsdConfiguratorConfigNoChoicePosition == 0}

                    {* include our no-choice option *}
                    {include file="frontend/atsd-configurator/elements/no-choice/template_{$element.template.key}.tpl" element=$element fieldset=$fieldset configurator=$configurator selection=$selection}

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



                    {* the article row *}
                    <div class="block-group article--row"
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
                         data-atsd-configurator-article-selector="list"
                    >

                        {* the input field *}
                        <div class="block article--column--input">
                            <input type="{if $element.multiple == true}checkbox{else}radio{/if}"
                                   name="{$name}"
                                   {if $isSelected == true}checked="checked"{/if}
                                   data-atsd-configurator-selector="true"
                                   data-atsd-configurator-selector-fieldset-id="{$fieldset.id}"
                                   data-atsd-configurator-selector-element-id="{$element.id}"
                                   data-atsd-configurator-selector-article-id="{$elementArticle.id}"
                            />
                        </div>



                        {* article name and number *}
                        <div class="block article--column--name" style="cursor: pointer;"
                             data-atsd-configurator-article-row="true"
                             data-atsd-configurator-article-row-article-id="{$elementArticle.id}"
                        >

                            {* output delivery status depending on available stock *}
                            <span class="delivery-status--placeholder">&nbsp;</span>

                            {* and the article name *}
                            {$article->getName()}

                        </div>



                        {* quantity selector *}
                        <div class="block article--column--quantity">

                            {* do we went to output any form of quantity? *}
                            {if $outputQuantity == true}

                                {* do we want to show a select field? *}
                                {if $hasSelectableQuantity == true}

                                    {* set variables *}
                                    {assign var="step" value={"{if $elementArticle.quantityMultiply == true}{$elementArticle.quantity|intval}{else}1{/if}"}|intval}
                                    {assign var="min" value=$step}
                                    {assign var="max" value={"{if $atsdConfiguratorConfigSaleType == 0}99{else}{if $atsdConfiguratorConfigSaleType == 1}{if $article->isCloseouts() == true}{$article->getStock()}{else}99{/if}{else}{$article->getStock()}{/if}{/if}"}|intval}
                                    {assign var="selected" value={$selectedQuantity}}

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

                                {* force % width *}
                                &nbsp;

                            {/if}

                        </div>



                        {* the price *}
                        <div class="block article--column--price price--placeholder" style="">&nbsp;</div>

                        {* info image *}
                        <div class="block article--column--info-icon" style=""
                             data-atsd-configurator-selector-info="true"
                             data-atsd-configurator-selector-info-article-id="{$elementArticle.id}"
                        >
                            <img src="{link file='frontend/_public/src/img/atsd-configurator/info-icon.v2.png'}" style="" />
                        </div>



                        {* clear floating for our detail panel *}
                        <div style="clear: both;"></div>

                        {* the hidden detail panel which will be filled via ajax request *}
                        <div style="width: 100%; margin-top: 20px; margin-bottom: 20px; display: none;"
                             data-atsd-configurator-selector-info-panel="true"
                             data-atsd-configurator-selector-info-panel-loaded="false"
                             data-atsd-configurator-selector-info-panel-article-id="{$elementArticle.id}"
                        >

                            {* default height so the animation is fluid *}
                            <div class="atsd-configurator--info-panel--loading-indicator"></div>

                        </div>

                    </div>

                {/foreach}



                {* is this an optional fieldset with a radio selection *}
                {if $element.multiple == false && $element.mandatory == false && $atsdConfiguratorConfigNoChoicePosition == 1}

                    {* include our no-choice option *}
                    {include file="frontend/atsd-configurator/elements/no-choice/template_{$element.template.key}.tpl" element=$element fieldset=$fieldset configurator=$configurator selection=$selection}

                {/if}

            </div>

        </div>

    </div>

</div>



