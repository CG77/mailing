{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{set scope=global persistent_variable=hash('mailing_subject', $node.name|wash, 'mailing_date',  'date format', 'mailing_type', 'article'  )}
<div class="content-view-full">
    <div class="class-article">

        <div class="attribute-header">
            <h1 style="margin-top: 0; padding-top: 0;">{$node.data_map.title.content|wash()}</h1>
        </div>

        <div class="attribute-byline">
        {if $node.data_map.author.content.is_empty|not()}
            <p class="author">
                {attribute_view_gui attribute=$node.data_map.author}
            </p>
        {/if}
            <p class="date">
            {$node.object.published|l10n(shortdatetime)}
            </p>
        </div>

    {if eq( ezini( 'article', 'ImageInFullView', 'content.ini' ), 'enabled' )}
        {if $node.data_map.image.has_content}
            <div class="attribute-image">
                {attribute_view_gui attribute=$node.data_map.image image_class=medium}

                {if $node.data_map.caption.has_content}
                    <div class="caption" style="width: {$node.data_map.image.content.medium.width}px">
                        {attribute_view_gui attribute=$node.data_map.caption}
                    </div>
                {/if}
            </div>
        {/if}
    {/if}

    {if eq( ezini( 'article', 'SummaryInFullView', 'content.ini' ), 'enabled' )}
        {if $node.data_map.intro.content.is_empty|not}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.intro}
            </div>
        {/if}
    {/if}

    {if $node.data_map.body.content.is_empty|not}
        <div class="attribute-long">
            {attribute_view_gui attribute=$node.data_map.body}
        </div>
    {/if}

    {include uri='design:parts/related_content.tpl'}

    {def $tipafriend_access=fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'tipafriend' ) )}
    {if and( ezmodule( 'content/tipafriend' ), $tipafriend_access )}
        <div class="attribute-tipafriend">
            <p>
                <a href={concat( "/content/tipafriend/", $node.node_id )|ezurl} title="{'Tip a friend'|i18n( 'design/ezwebin/full/article' )}">{'Tip a friend'|i18n( 'design/ezwebin/full/article' )}</a></p>
        </div>
    {/if}
    {undef $tipafriend_access}
    </div>
</div>