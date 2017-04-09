
{* file to extend *}
{extends file='parent:frontend/detail/index.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/detail"}



{* we just use the bundle block *}
{block name="frontend_detail_index_bundle"}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus == true}

        {* did we just save a selection? *}
        {if $atsdConfiguratorSelectionSaved == true}

            {* show a success message *}
            {include file="frontend/_includes/messages.tpl" type="success" content="{s force name="SelectionSaved"}Die gewählte Konfiguration wurde gespeichert.<br />Sie können diese Konfiguration über Ihr Kundenkonto oder über folgenden Link erneut laden:{/s}<br /><a href='{url controller='AtsdConfigurator' action='loadSelection' key=$atsdConfiguratorSelectionSavedKey}'>{url controller='AtsdConfigurator' action='loadSelection' key=$atsdConfiguratorSelectionSavedKey}</a>"}

        {/if}

        {* did we just load a selection? *}
        {if $atsdConfiguratorSelectionLoaded == true}

            {* show a s success message *}
            {include file="frontend/_includes/messages.tpl" type="success" content="{s name="SelectionLoaded"}Die gespeicherte Konfiguration wurde erfolgreich geladen.{/s}"}

        {/if}

        {* is the current selection invalid? *}
        {if $atsdConfiguratorSelectionError == true}

            {* show an error message *}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name="InvalidSelection"}Die gewählte Konfiguration ist ungültig.{/s}"}

        {/if}



        {* is the configurator invalid?! *}
        {if $atsdConfiguratorInvalid == true}

            {* show an error message *}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name="InvalidConfigurator"}Der Konfigurator ist aktuell nicht verfügbar.{/s}"}

        {* we have a valid configurator *}
        {else}

            {* show default article description *}
            <div class="panel has--border is--rounded">
                <div class="panel--title is--underline">
                    {s name="ArticleDescription"}Beschreibung{/s}
                </div>
                <div style="padding: 20px;">
                    {$sArticle.description_long}
                </div>
            </div>

            {* include our configurator *}
            {include file="frontend/atsd-configurator/configurator.tpl" configurator=$atsdConfigurator selection=$atsdConfiguratorSelection}

        {/if}

    {/if}



    {* append the parent *}
    {$smarty.block.parent}

{/block}



{* remove tabs for configurators *}
{block name="frontend_detail_index_tabs"}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus != true}

        {* insert parent *}
        {$smarty.block.parent}

    {/if}

{/block}



{* remove tabs for shopware 5.2 *}
{block name="frontend_detail_index_detail"}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus != true}
        {$smarty.block.parent}
    {/if}

{/block}

{* remove tabs for shopware 5.2 *}
{block name="frontend_detail_index_tabs_cross_selling"}

    {* do we even have a configurator? *}
    {if $atsdConfiguratorStatus != true}
        {$smarty.block.parent}
    {/if}

{/block}




