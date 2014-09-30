{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page='list'}
    {if is_unset($sort_by)}
        {def $sort_by = hash( 'registred', 'desc')}
    {/if}

    {def
        $orphanMode = cond( $fetchkey|eq('orphanusers'),true(),true(),false() )
        $page_limit_b = ezini('PaginationSettings','UsersLimit','ezmailing.ini')
        $lists = fetch('mailing','fetch',hash(
                                                       'type',$fetchkey,
                                                       'offset', $view_parameters.offset,
                                                       'limit', $page_limit_b,
                                                       'sort_by', $sort_by      
                                                   )
                              )
        $page_uri_b = concat( '/mailing/users', 	 cond( $subaction|ne(''), concat('/', $subaction),true(),'' ))

        $lists_count_b = fetch('mailing','fetch_count',hash(
                                                            'type', cond( and( is_set( $orphanMode ), $orphanMode ), 'orphanusers', true(), 'registredusers' ),
                                                            'filter',cond( is_set($state_filter), hash( 'state',$state_filter), is_set($filter), $filter, true(), hash() )
                                                            )
                              )
    }

    {include   uri="design:mailing/list/users.tpl"
                    lists=$lists
                    orphanMode=$orphanMode
                    lists_count_b=$lists_count_b
                    page_limit_b=$page_limit_b
                    page_uri_b=$page_uri_b
    }

    {undef $lists $page_limit}

{include uri="design:mailing/common/footer.tpl"}
