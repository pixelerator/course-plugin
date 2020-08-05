
    jQuery(document).ready(function($) {
        $('body').on('focus',".start_date", function(){
            $(this).datepicker();
        });
        $('body').on('focus',".end_date", function(){
             $(this).datepicker();
         });$(".add_venue").on("click", function() {
           var temp_clone = $(".course_template").clone();
           $(temp_clone).removeClass("course_template");
            var rand_id = "ven_"+makeid(7);
            $(temp_clone).addClass(rand_id);

            $(temp_clone).find(".start_date").attr("name",rand_id+"['start_date']");
            $(temp_clone).find(".end_date").attr("name",rand_id+"['end_date']");
            $(temp_clone).find(".all_venues").attr("name",rand_id+"['venue']");
            $(temp_clone).find(".t_status").attr("name",rand_id+"['t_status']");
            $(temp_clone).find(".to_delete").attr("data-id",rand_id);



            $(temp_clone).find(".fees").attr("name",rand_id+"['fees']");



            console.log(temp_clone);
            $(temp_clone).insertAfter(".cssglobal:last").show("slow");

        });
        $(document).on('click', '.to_delete', function() {
            // Do something on an existent or future .dynamicElement
            if (confirm("Are you sure you want to delete this venue")) {
                var id = $(this).attr('data-id');
                $("."+id+" input.t_status").val(1);
                $("."+id).hide("fast");
            } else {
                //txt = "You pressed Cancel!";
            }

        });

    });


    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
