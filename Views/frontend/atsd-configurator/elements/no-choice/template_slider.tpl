
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configurator/template/slider/no-choice"}



{* the item container *}
<div class="product-slider--item"
     data-atsd-configurator-empty-choice="true"
     data-atsd-configurator-fieldset-id="{$fieldset.id}"
     data-atsd-configurator-element-id="{$element.id}"
     data-atsd-configurator-empty-choice-image="{link file='frontend/_public/src/img/no-picture.jpg'}"
     data-atsd-configurator-empty-choice-selector="slider"
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

                             {* no image available *}
                            <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                                 alt="{s name="EmptyChoice"}Keine Auswahl{/s}"
                                 title="{s name="EmptyChoice"}Keine Auswahl{/s}"
                            />

                        </span>
                    </span>

                </div>

                {* article name *}
                <span class="product--title" style="cursor: pointer;">
                    {s name="EmptyChoice"}Keine Auswahl{/s}
                </span>

                {* product price *}
                <div class="product--price-info">
                    <div class="product--price">
                        <span class="price--default is--nowrap">

                            {* the price *}
                            {"0.00"|currency} {s name="Star"}*{/s}

                        </span>
                    </div>
                </div>

                {* visual selector *}
                <div style="margin-top: 12px;">

                    {* show the button *}
                    <button class="btn is--align-center {if $element.multiple == true}is--multiple{else}is--not-multiple{/if}"
                            style="width: 100%;"
                            data-atsd-configurator-empty-choice-selector-button="true"
                            data-atsd-configurator-empty-choice-selector-button-element-id="{$element.id}"
                    >

                        {* button text *}
                        {s name="SelectorButtonSelectable" namespace="frontend/AtsdConfigurator/configuration"}Wählen{/s}

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

