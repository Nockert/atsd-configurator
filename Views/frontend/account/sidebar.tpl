
{* file to extend *}
{extends file="parent:frontend/account/sidebar.tpl"}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/account"}



{* append our main menu to the sidebar *}
{block name='frontend_account_menu_link_orders' append}

    {* activated for this shop? *}
    {if $atsdConfiguratorShopStatus == true}

        <li class="navigation--entry">
            <a href="{url controller='AtsdConfigurator' action='index'}" title="{s name="SidebarLink"}Gespeicherte Konfigurationen{/s}" class="navigation--link{if $atsdConfiguratorActive == true} is--active{/if}">
                {s name="SidebarLink"}Gespeicherte Konfigurationen{/s}
            </a>
        </li>

    {/if}

{/block}


