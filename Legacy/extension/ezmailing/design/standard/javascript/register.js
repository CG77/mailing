$( function() {

    $( "div.ezmailing_registration form" ).submit( function(dataForm) {
        $.ez( "mailingregister::register", $( this ).serialize(), function( data ) {
            if ( data.content.confirmation ) {
                alert( data.content.confirmation );
            } else {
                alert( data.content.errors.join( "\n" ) );
            }
        } );
        return false;
    } );

} );