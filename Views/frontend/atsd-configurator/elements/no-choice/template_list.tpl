
{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/configurator/template/list/no-choice"}



{* the article row *}
<div class="block-group article--row"
     data-atsd-configurator-empty-choice="true"
     data-atsd-configurator-fieldset-id="{$fieldset.id}"
     data-atsd-configurator-element-id="{$element.id}"
     data-atsd-configurator-empty-choice-image="{link file='frontend/_public/src/img/no-picture.jpg'}"
     data-atsd-configurator-empty-choice-selector="list"
>

    {* the input field *}
    <div class="block article--column--input">
        <input type="radio"
               name="configurator-empty-choice-fieldset-{$fieldset.id}-element-{$element.id}"
               data-atsd-configurator-empty-choice-selector="true"
               data-atsd-configurator-empty-choice-selector-fieldset-id="{$fieldset.id}"
               data-atsd-configurator-empty-choice-selector-element-id="{$element.id}"
        />
    </div>

    {* article name and number *}
    <div class="block article--column--name" style="cursor: pointer;"
         data-atsd-configurator-empty-choice-row="true"
         data-atsd-configurator-empty-choice-row-fieldset-id="{$fieldset.id}"
         data-atsd-configurator-empty-choice-row-element-id="{$element.id}"
    >

        <i class="delivery--status-icon delivery--status-none" style="background: #999999;"></i>
        {s name="EmptyChoice"}Keine Auswahl{/s}

    </div>

    {* the price *}
    <div class="block article--column--price" style="width: 15%">
        {"0.00"|currency} {s name="Star"}*{/s}
    </div>

    {* info image *}
    <div class="block article--column--price" style="width: 5%; text-align: right;">
    </div>

    {* clear floating *}
    <div style="clear: both;"></div>

</div>

