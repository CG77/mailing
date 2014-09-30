{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page="search"}

{def $filter = array()}
{if is_unset($sort_by)}
    {def $sort_by = hash( 'state_updated', 'desc')}
{/if}
{foreach $search_fields as $search_field}
    {set $filter =  $filter|merge(  hash(   $search_field, 
                                            array(
                                                'like', 
                                                concat('%', $search_text, '%')
                                            )
                                    )   )}
{/foreach}
{set    $filter = hash( 'or', $filter)}

{def
    $page_limit     =   ezini('PaginationSettings', 'MailingLimit', 'ezmailing.ini')
    $page_uri       =   concat( '/mailing/registrations', 
                                cond(   $subaction|ne(''), concat('/', $subaction),
                                        true(), '' 
                                ), 
                                '/', 
                                $search_text|urlencode 
                        )
    $lists_count    =   fetch(  'mailing', 'fetch_count',   hash(
                                                                'type','registrations',
                                                                'filter',$filter
                                                            )
                        )
    $lists          =   fetch(  'mailing', 'fetch',     hash(
                                                            'type', 'registrations',
                                                            'filter', $filter,
                                                            'limit', $page_limit,
                                                            'offset', $view_parameters.offset,
                                                            'sort_by', $sort_by
                                                        )
                        )}
    {include uri="design:mailing/list/registrations.tpl" lists=$lists searched_text=$search_text|wash edit=true()}
{undef $lists $lists_count $page_limit $page_uri $filter}
{include uri="design:mailing/common/footer.tpl"}