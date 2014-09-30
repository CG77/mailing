{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page='list'}
    {if is_unset($sort_by)}
        {def $sort_by = hash( 'state_updated', 'desc')}
    {/if}
    {def 
        $page_limit = ezini('PaginationSettings','RegistrationsLimit','ezmailing.ini')
        $page_uri = concat( '/mailing/registrations', cond( $subaction|ne(''), concat('/', $subaction),true(),'' ) )
        $lists_count = fetch('mailing','fetch_count',hash(
                                                       'type','registrations',
                                                       'filter',cond( is_set($state_filter), hash( 'state',$state_filter), true(), hash() ),
                                                   )
                              )
        $lists = fetch('mailing','fetch',hash(
                                                       'type','registrations',
                                                       'filter',cond( is_set($state_filter), hash( 'state',$state_filter), true(), hash() ),
                                                       'offset', $view_parameters.offset,
                                                       'sort_by', $sort_by,
                                                       'limit', $page_limit            
                                                   )
                              )
    }
         {include uri="design:mailing/list/registrations.tpl" lists=$lists blockedInfos=cond( is_set($state_filter),false(),true(),true()) edit=true()}
    
    {undef $lists $lists_count $page_limit}
{include uri="design:mailing/common/footer.tpl"}