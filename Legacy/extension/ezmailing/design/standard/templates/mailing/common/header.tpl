{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{ezcss_require(array('ezmailing.css'))}
{ezscript_require(array('ezmailing.js'))}
{include uri="design:mailing/common/validation.tpl"}
<div id="ezmailing">
	<div class="context-block content-dashboard">
	{* DESIGN: Header START *}
    {def    $title  =   ''
            $first  =   true()  }
    {foreach $path as $key => $components}
        {if $components.text|ne('')}
            {if $first}
                {set $first = false()}
            {else}
                {set $title = concat($title, ' - ')}
            {/if}
            {set $title = concat($title, $components.text|wash)}
        {/if}
    {/foreach}
    
    {if is_set( $page )|not()}
    	{include uri=concat( "design:mailing/headers/unset.tpl" )}
    {else}
    	{include uri=concat( "design:mailing/headers/", module_params().function_name, "/", $page, ".tpl" )}
    {/if}

        {* DESIGN: Content START *}
        <div class="box-bc">
            <div class="box-ml">
                <div class="box-mr">
                    <div class="box-bl">
                        <div class="box-br">
                            <div class="box-content">
                            	<div class="block">
    {undef  $title
            $first}