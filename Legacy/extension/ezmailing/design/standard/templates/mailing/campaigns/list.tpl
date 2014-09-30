{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page='list'}
    {if is_unset($sort_by)}
        {def 	$sort_by 		= hash( 'state_updated', 'desc')
        		$declared_sort 	= true()}
    {/if}
    
    {def 
        $page_limit = ezini('PaginationSettings','MailingLimit','ezmailing.ini')
        $lists = fetch('mailing','fetch',hash(
                                                       'type','campaigns',
                                                       'filter',cond( is_set($state_filter), hash( 'state',$state_filter), true(), hash() ),
                                                       'limit',$page_limit,
                                                       'offset', $view_parameters.offset,
                                                       'sort_by', $sort_by
                                                   )
                              )
    }
         
         {include uri="design:mailing/list/campaigns.tpl" lists=$lists}
    {if is_set( $declared_sort )}
    	{undef $sort_by}
    {/if} 
    {undef $lists $page_limit} 
{include uri="design:mailing/common/footer.tpl"}