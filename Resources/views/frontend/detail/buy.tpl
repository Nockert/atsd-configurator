
{* file to extend *}
{extends file='parent:frontend/detail/buy.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/buy"}



{* replace the complete form *}
{block name='frontend_detail_buy'}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus == true}

        {* container *}
        <div class="buybox--form">
            <div class="buybox--button-container block-group">

                {* replace quantity with save button *}
                <div class="buybox--quantity block">

                    {* save button form *}
                    <form method="post" action="{url controller='AtsdConfigurator' action='saveSelection'}">

                        {* hidden values *}
                        <input type="hidden" name="configuratorId" value="{$atsdConfigurator.id}" />
                        <input type="hidden" name="selection" value="" data-atsd-configurator-hidden-selection="true" />

                        {* the button *}
                        <button class="btn is--align-center is--icon-right is--large" style="width: 100%;" onclick="$.loadingIndicator.open( { 'closeOnClick': false } );">
                            {s name='ButtonSave'}Speichern{/s} <i class="icon--disk"></i>
                        </button>

                    </form>

                </div>

                {* to basket form *}
                <form method="post" action="{url controller='AtsdConfigurator' action='addToBasket'}">

                    {* hidden values *}
                    <input type="hidden" name="configuratorId" value="{$atsdConfigurator.id}" />
                    <input type="hidden" name="selection" value="" data-atsd-configurator-hidden-selection="true" />

                    {* the button *}
                    <button class="buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAdd"}In den Warenkorb{/s}" onclick="$.loadingIndicator.open( { 'closeOnClick': false } );">
                        {s name="DetailBuyActionAdd"}In den Warenkorb{/s} <i class="icon--arrow-right"></i>
                    </button>

                </form>

            </div>
        </div>

    {* no configurator *}
    {else}

        {* just the parent *}
        {$smarty.block.parent}

    {/if}

{/block}




