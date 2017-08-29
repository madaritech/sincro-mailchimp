(function( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $("#sm_result").hide();
    $("#mc_subscribe").change(
        function() {

            $("#spinner").show();
            
            var checked = 0;

            if(this.checked) {
                   checked = 1;
            }

            var data = {
                'action': 'esegui_iscrizione',
                'check_status': checked,
                'user_email' : sm.user_email,
                'user_role' : sm.user_role,
                '_wpnonce' : sm._wpnonce
            };

            $.ajax({
                type: "POST",
                url: ajaxurl,
                timeout: 10000,
                data: data, 
                success: function(response) {
                        if (response.success) { 
                            $("#spinner").hide();
                            $("#chk_block").hide();
                            $("#sm_result").fadeIn();
                            setTimeout(
                                function(){
                                    $("#sm_result").hide();
                                    $("#chk_block").fadeIn();
                                }, 5000
                            );
                        } else {
                            $("#spinner").hide();
                            if (checked == 0) 
                                $("#mc_subscribe").prop('checked', true);
                            else
                                $("#mc_subscribe").prop('checked', false);
                            alert(response.data);
                        }
                    },
                error: function() {
                    $("#spinner").hide();
                    if (checked == 0) 
                        $("#mc_subscribe").prop('checked', true);
                    else
                        $("#mc_subscribe").prop('checked', false);
                    alert(data);
                }
            });
        }
    );

})( jQuery );
