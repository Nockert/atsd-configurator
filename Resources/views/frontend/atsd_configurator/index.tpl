
{* file to extend *}
{extends file='frontend/account/index.tpl'}

{* set our namespace *}
{namespace name="frontend/AtsdConfigurator/overview"}



{* append breadcrumb *}
{block name='frontend_index_start' append}
    {$sBreadcrumb[] = ['name'=>"{s name='BreadcrumbTitle'}Gespeicherte Konfigurationen{/s}", 'link'=>{url}]}
{/block}



{* set our complete content *}
{block name='frontend_index_content'}

    {* main wrapper *}
    <div class="content account--content atsd-configurator--account">

        {* welcome message *}
        <div class="account--welcome panel">
            <h1 class="panel--title">{s name='Title'}Meine gespeicherten Konfigurationen{/s}</h1>
            <div class="panel--body is--wide">
                <p>{s name='Message'}Hier finden Sie eine Übersicht Ihrer letzten 10 gespeicherten Konfigurationen.{/s}</p>
            </div>
        </div>

        {* any saved baskets? *}
        {if is_array( $atsdConfiguratorSelections ) && $atsdConfiguratorSelections|@count > 0}

            <div class="account--orders-overview panel is--rounded">

                <div class="panel--table">

                    {* table header *}
                    <div class="orders--table-header panel--tr">
                        <div class="panel--th column--status" style="width: 25%">{s name='ColumnDate'}Datum{/s}</div>
                        <div class="panel--th column--status" style="width: 50%">{s name='ColumnArticles'}Artikel{/s}</div>
                        <div class="panel--th column--actions is--align-right" style="width: 25%"></div>
                    </div>

                    {* loop every subscribe*}
                    {foreach $atsdConfiguratorSelections as $selection}

                        <div class="order--item panel--tr">

                            <div class="order--status panel--td column--status" style="width: 25%">

                                <div class="column--label">
                                    {s name='ColumnDateLabel'}Datum{/s}:
                                </div>

                                <div class="column--value" style="font-weight: normal;">
                                    {s name='ColumnDateValue'}{$selection.date|date_format:"%d.%m.%Y - %R"} Uhr{/s}
                                </div>

                            </div>

                            <div class="order--status panel--td column--status" style="width: 50%">

                                <div class="column--label">
                                    {s name='ColumnLabelArticles'}Artikel{/s}:
                                </div>

                                <div class="column--value">

                                    <strong>{$selection.article.name}</strong><br />

                                    {* loop every fieldset *}
                                    {foreach $selection.fieldsets as $fieldset}

                                        {* main container *}
                                        <div class="panel has--border is--rounded" style="border: 1px solid #dadae5; margin-top: 10px;">

                                            {* fieldset description *}
                                            <div class="panel--title is--underline" style="line-height: 16px; font-size: 12px;">
                                                {$fieldset.description}
                                            </div>

                                            {* articles container *}
                                            <div style="padding: 20px; font-size: 12px;">

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

                                                            {/foreach}

                                                        </div>

                                                    </div>

                                                {/foreach}

                                            </div>

                                        </div>

                                    {/foreach}

                                </div>

                            </div>

                            <div class="order--actions panel--td column--actions" style="width: 25%;">

                                <a href="{url controller='AtsdConfigurator' action='loadSelection' key={$selection.key}}"
                                   title="{"{s name="ActionButtonLoadBasket"}Konfiguration laden{/s}"|escape}"
                                   class="btn is--small">
                                    {s name="ActionButtonLoadBasket"}Konfiguration laden{/s}
                                </a>

                            </div>

                        </div>

                    {/foreach}

                </div>

            </div>

        {* none *}
        {else}

            {* show a message *}
            <div class="account--no-orders-info">
                {include file="frontend/_includes/messages.tpl" type="warning" content="{s name="InfoEmpty"}Sie haben noch keine Konfigurationen gespeichert.{/s}"}
            </div>

        {/if}

    </div>

{/block}
