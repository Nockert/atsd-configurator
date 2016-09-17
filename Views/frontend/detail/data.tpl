
{* file to extend *}
{extends file='parent:frontend/detail/data.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/detail"}



{* remove the default price - we will update it via jquery *}
{block name='frontend_detail_data_price_configurator'}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus == true}

        {* the default price container *}
        <span class="price--content content--default">

            {* fill it *}
            &nbsp;

        </span>

    {* no configurator *}
    {else}

        {* just the parent *}
        {$smarty.block.parent}

    {/if}

{/block}



