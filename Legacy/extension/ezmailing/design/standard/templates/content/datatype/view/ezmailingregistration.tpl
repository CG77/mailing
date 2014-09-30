{* DO NOT EDIT THIS FILE! Use an override template instead. *}

{if $attribute.has_content}
    {def $registrations = $attribute.content}
        <div class="block">
            <ul>
                {foreach $registrations as $registration}
                    {if $registration.is_active}
                        <li>{$registration.mailing_list.name|wash}</li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    {undef $registrations}
{/if}