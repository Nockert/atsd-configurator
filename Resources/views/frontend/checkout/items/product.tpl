
{* file to extend *}
{extends file='parent:frontend/checkout/items/product.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/checkout/product"}





{* set a new produkt link to load the selection *}
{block name='frontend_checkout_cart_item_name'}

    {* configurator? *}
    {if $sBasketItem.atsdConfiguratorHasSelection == true && $sBasketItem.atsdConfiguratorSelection.valid == true }

        {* update the product url *}
        {$detailLink={url controller="detail" sArticle=$sBasketItem.articleID atsdConfiguratorKey=$sBasketItem.atsdConfiguratorSelection.key}}

    {/if}

    {* just use the parent*}
    {$smarty.block.parent}

{/block}



{* deny quantity update for configurator *}
{block name='frontend_checkout_cart_item_quantity_selection'}

    {* configurator? *}
    {if $sBasketItem.atsdConfiguratorHasSelection == true}

        {* just show the quantity *}
        <div style="line-height: 42px;">
            1
        </div>

    {* every other article *}
    {else}

        {* just use the parent*}
        {$smarty.block.parent}

    {/if}

{/block}



{* replace item price *}
{block name='frontend_checkout_cart_item_price'}

    {* is this a valid configurator? *}
    {if $sBasketItem.atsdConfiguratorHasSelection == true && $sBasketItem.atsdConfiguratorSelection.valid == true && $sBasketItem.atsdConfiguratorSelection.hasPseudoPrice == true }

        {* open container *}
        <div class="panel--td column--unit-price is--align-right atsd-configurator--checkout--item-price">

            {* label *}
            <div class="column--label unit-price--label">
                {s name="CartColumnPrice" namespace="frontend/checkout/cart_header"}{/s}
            </div>

            {* old price *}
            <span class="atsd-configurator--checkout--price-pseudo">
                Statt {$sBasketItem.atsdConfiguratorSelection.pseudoPrice|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}<br />
            </span>

            {* new price *}
            <span class="atsd-configurator--checkout--price">
                {$sBasketItem.price|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}<br />
            </span>

        </div>

    {else}

        {* default smarty block *}
        {$smarty.block.parent}

    {/if}

{/block}



{* replace item price sum *}
{block name='frontend_checkout_cart_item_total_sum'}

    {* is this a valid configurator? *}
    {if $sBasketItem.atsdConfiguratorHasSelection == true && $sBasketItem.atsdConfiguratorSelection.valid == true && $sBasketItem.atsdConfiguratorSelection.hasPseudoPrice == true }

        {* open container *}
        <div class="panel--td column--total-price is--align-right atsd-configurator--checkout--item-price">

            {* label *}
            <div class="column--label total-price--label">
                {s name="CartColumnPrice" namespace="frontend/checkout/cart_header"}{/s}
            </div>

            {* old price *}
            <span class="atsd-configurator--checkout--price-pseudo">
                Statt {$sBasketItem.atsdConfiguratorSelection.pseudoPrice|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}<br />
            </span>

            {* new price *}
            <span class="atsd-configurator--checkout--price">
                {$sBasketItem.amount|currency}{s name="Star" namespace="frontend/listing/box_article"}{/s}<br />
            </span>

        </div>

    {else}

        {* default smarty block *}
        {$smarty.block.parent}

    {/if}

{/block}



{* append some details for /checkout/ after the delivery informations *}
{block name='frontend_checkout_cart_item_delivery_informations'}

    {* always show the parent *}
    {$smarty.block.parent}

    {* do we have a selection? *}
    {if $sBasketItem.atsdConfiguratorHasSelection == true}

        {* is our configurtor valid?! *}
        {if $sBasketItem.atsdConfiguratorSelection.valid == true}

            {* clear *}
            <div style="clear: both;"></div>

            {* loop every fieldset *}
            {foreach $sBasketItem.atsdConfiguratorSelection.fieldsets as $fieldset}

                {* main container *}
                <div class="panel has--border is--rounded" style="border: 1px solid #dadae5; margin-top: 10px;">

                    {* fieldset description *}
                    <div class="panel--title is--underline" style="line-height: 16px; font-size: 12px;">
                        {$fieldset.description}
                    </div>

                    {* articles container *}
                    <div style="padding: 20px;">

                        {* loop the elements *}
                        {foreach $fieldset.elements as $element}

                            {* element container *}
                            <div class="block-group" style="line-height: 16px;">

                                {* element name *}
                                <div class="block" style="width: 35%;">
                                    {$element.description}:
                                </div>

                                {* selected article(s) *}
                                <div class="block" style="width: 65%;">

                                    {* loop all articles *}
                                    {foreach $element.articles as $article}

                                        {* more than 1 article? *}
                                        {if $article.quantity > 1}
                                            {$article.quantity}x
                                        {/if}

                                        {* article name *}
                                        {$article.name}

                                        {* 1 line per article *}
                                        <br />

                                    {foreachelse}

                                        {* no choice for this element *}
                                        <span style="color: gray;">---</span>

                                    {/foreach}

                                </div>

                            </div>

                        {/foreach}

                    </div>

                </div>

            {/foreach}

        {* invalid configurator *}
        {else}

        {/if}

    {/if}

{/block}



