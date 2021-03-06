{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{if and( is_set( $edit ), $edit|eq( true() ) )}
    <div class="block">
        <label for="{$identifier}">{$label|i18n( 'extension/ezmailing/text' )}{if $requires|contains( $identifier )}<span class="required">*</span>{/if}</label>
       	<input type="text" id="{$identifier}" name="{$identifier}" value="{$item.$identifier|wash}" />
        <div class="clear"></div>
    </div>
{else}
    <div class="block">
        <label>{$label|i18n( 'extension/ezmailing/text' )}:</label>
    	<p>{$item.$identifier|wash}</p>
        <div class="clear"></div>
    </div>
{/if}
