{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div class="border-box">
    <div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
    <div class="border-ml"><div class="border-mr"><div class="border-mc">
    {include uri='design:mailing_registration/validation.tpl'}
    {if is_set($confirmation)|not}
        {include uri='design:mailing_registration/block_register.tpl'}
    {/if}
    </div></div></div>
    <div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>