{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def $lists  = fetch('mailing','fetch',hash(
                                                   'type','registredusers',
                                                   'limit', 10,
                                                   'sort_by', hash( 'registred', 'desc')
                                               )
                          )
     $action = cond( is_set($action),$action,true(),"mailing/users")
}
    {include uri="design:mailing/list/users.tpl" lists=$lists action=$action blockid="last_registred_users" show_search_form=false()}
{undef $lists $action} 