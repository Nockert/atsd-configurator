
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configurator"}



{* main container *}
<div class="atsd-configurator"
     data-atsd-configurator="true"
     data-atsd-configurator-rebate="{$configurator.rebate}"
     data-atsd-configurator-main-article-price="{$configurator['article']->getCheapestPrice()->getCalculatedPrice()}"
     data-atsd-configurator-main-article-weight="{$configurator['article']->getWeight()}"
     data-atsd-configurator-main-article-stock="{$configurator['article']->getStock()}"
>

    {* loop the fieldsets *}
    {foreach $configurator.fieldsets as $fieldset}

        {* main container *}
        <div class="atsd-configurator--fieldset">

            {* header *}
            <h1 class="atsd-configurator--fieldset-description">{$fieldset.description}</h1>



            {* component overview *}
            <div class="atsd-configurator--fieldset-summary block-group">

                {* image container *}
                <div class="atsd-configurator--fieldset-summary--left block" style="">

                    <img src="{$fieldset.mediaFile}" alt="" style="" />

                </div>

                {* summary container *}
                <div class="atsd-configurator--fieldset-summary--right block" style="">

                    {* description *}
                    <div class="atsd-configurator--fieldset-summary--header">Gewählte Konfiguration</div>

                    {* we need a container to color every 2nd item *}
                    <div class="atsd-configurator--fieldset-summary--element-container">

                        {* loop every element *}
                        {foreach $fieldset.elements as $element}

                            {* element container *}
                            <div class="atsd-configurator--fieldset-summary--element block-group">

                                {* element name *}
                                <div class="column--element block" style="">
                                    {$element.description}:
                                </div>

                                {* selected article(s) *}
                                <div class="column--selection summary--selection--element-{$element.id} block"
                                     data-atsd-configurator-fieldset-summary="true"
                                     data-atsd-configurator-fieldset-id="{$fieldset.id}"
                                     data-atsd-configurator-element-id="{$element.id}"
                                >
                                </div>

                            </div>

                        {/foreach}

                    </div>

                </div>

            </div>



            {* all elements *}
            {foreach $fieldset.elements as $element}

                {* element container *}
                <div class="atsd-configurator--element"
                     data-atsd-configurator-element="true"
                     data-atsd-configurator-has-image="{if $element.template.key == "list"}true{else}false{/if}"
                     data-atsd-configurator-fieldset-id="{$fieldset.id}"
                     data-atsd-configurator-element-id="{$element.id}"
                >

                    {* load the element by template key *}
                    {include file="frontend/atsd-configurator/elements/template_{$element.template.key}.tpl" element=$element fieldset=$fieldset configurator=$configurator selection=$selection}

                </div>

            {/foreach}

        </div>

    {/foreach}



    {* our button action panel *}
    <div class="atsd-configurator--actions panel has--border is--rounded" style="padding: 10px;">

        {* inner container *}
        <div class="atsd-configurator--actions--container">

            {* save form *}
            <form method="post" action="{url controller='AtsdConfigurator' action='saveSelection'}">

                {* hidden values *}
                <input type="hidden" name="configuratorId" value="{$configurator.id}" />
                <input type="hidden" name="selection"  value="" data-atsd-configurator-hidden-selection="true" />

                {* show the button *}
                <button class="btn is--align-center is--icon-right atsd-configurator--actions--save">
                    {s name='ButtonSave'}Konfiguration speichern{/s} <i class="icon--disk"></i>
                </button>

            </form>

            {* price info *}
            <div class="atsd-configurator--actions--price" style="">

                {* price container - will be filled via jquery *}
                <div class="product--price price--default">
                </div>

                {* tax information *}
                <div class="tax-info" style="font-size: 10px;">
                    {s namespace="frontend/detail/data" name="DetailDataPriceInfo"}{/s}
                </div>

            </div>

            {* to basket form *}
            <form method="post" action="{url controller='AtsdConfigurator' action='addToBasket'}">

                {* hidden values *}
                <input type="hidden" name="configuratorId" value="{$configurator.id}" />
                <input type="hidden" name="selection"  value="" data-atsd-configurator-hidden-selection="true" />

                {* show the button *}
                <button class="btn is--align-center is--primary is--icon-right atsd-configurator--actions--submit">
                    {s name='ButtonToBasket'}In den Warenkorb{/s} <i class="icon--arrow-right"></i>
                </button>

            </form>

        </div>

    </div>

</div>


