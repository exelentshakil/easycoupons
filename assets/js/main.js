(function ($) {
    "use strict";
    $('#coupons-table').DataTable({
        scrollY:        '50vh',
        scrollCollapse: true,
    });

    $(document).ready(function () {

        var coupons_table = $('#coupons-table');
        var coupon_delete_by_date = $('#coupon-delete-by-date');
        var coupon_log_delete_by_date = $('#coupon-log-delete-by-date');

        coupons_table.on('click', '#delete-coupon', function() {
            var id = $(this).data('id');
            var data = {id: id, action: 'delete_coupon'};

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleting coupon... Are you sure?',
                showDenyButton: true,
                showConfirmButton: true,
                denyButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: EasyCoupons.ajaxurl,
                        method: 'POST',
                        beforeSend: function(xhr) {
                            // Set nonce here
                            xhr.setRequestHeader('X-WP-Nonce', EasyCoupons.nonce);

                        },
                        data: data
                    }).done(function(response) {

                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Your coupon has been deleted',
                            showConfirmButton: true,
                        });

                        location.reload();

                    }).fail(function(response) {

                        console.log(response);

                    }).always(function() {

                    });


                } else if (result.isDenied) {
                    Swal.fire('Coupon not deleted', '', 'info')
                }
            });

        }) // on click event
        coupons_table.on('click', '#delete-coupon-log', function() {
            var id = $(this).data('id');
            var data = {id: id, action: 'delete_coupon_log'};

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleting coupon... Are you sure?',
                showDenyButton: true,
                showConfirmButton: true,
                denyButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: EasyCoupons.ajaxurl,
                        method: 'POST',
                        beforeSend: function(xhr) {
                            // Set nonce here
                            xhr.setRequestHeader('X-WP-Nonce', EasyCoupons.nonce);

                        },
                        data: data
                    }).done(function(response) {

                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Your coupon has been deleted',
                            showConfirmButton: true,
                        });

                        location.reload();

                    }).fail(function(response) {

                        console.log(response);

                    }).always(function() {

                    });


                } else if (result.isDenied) {
                    Swal.fire('Coupon not deleted', '', 'info')
                }
            });

        }) // on click event

        coupon_delete_by_date.on('submit', function(e) {
            e.preventDefault();

            var data = $(this).serialize();

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleting all coupons by your selected date... Are you sure?',
                showDenyButton: true,
                showConfirmButton: true,
                denyButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: EasyCoupons.ajaxurl,
                        method: 'POST',
                        beforeSend: function(xhr) {
                            // Set nonce here
                            xhr.setRequestHeader('X-WP-Nonce', EasyCoupons.nonce);

                        },
                        data: data
                    }).done(function(response) {

                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Your coupon has been deleted',
                            showConfirmButton: true,
                        });
                        console.log(response);
                        if ( response.success == false ) {

                            Swal.fire('Sorry no coupon found!', '', 'info')

                        } else {
                            location.reload();
                        }

                    }).fail(function(response) {

                        console.log(response);

                    }).always(function() {

                    });


                } else if (result.isDenied) {
                    Swal.fire('Coupon not deleted', '', 'info')
                }
            });

        }) // on click event
        coupon_log_delete_by_date.on('submit', function(e) {
            e.preventDefault();

            var data = $(this).serialize();

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleting all coupons by your selected date... Are you sure?',
                showDenyButton: true,
                showConfirmButton: true,
                denyButtonText: `Cancel`,
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: EasyCoupons.ajaxurl,
                        method: 'POST',
                        beforeSend: function(xhr) {
                            // Set nonce here
                            xhr.setRequestHeader('X-WP-Nonce', EasyCoupons.nonce);

                        },
                        data: data
                    }).done(function(response) {

                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Your coupon has been deleted',
                            showConfirmButton: true,
                        });
                        console.log(response);
                        if ( response.success == false ) {

                            Swal.fire('Sorry no coupon found!', '', 'info')

                        } else {
                            location.reload();
                        }

                    }).fail(function(response) {

                        console.log(response);

                    }).always(function() {

                    });


                } else if (result.isDenied) {
                    Swal.fire('Coupon not deleted', '', 'info')
                }
            });

        }) // on click event


        // Helper Function to process form data & make array
        $.fn.form = function() {
            var formData = {};
            this.find('[name]').each(function() {
                formData[this.name] = this.value;
            })
            return formData;
        };

        // Handle unlock button click. Show popup on click.
        $('[data-easy-coupon]').click(function(){
            var vidID = $(this).data('easy-coupon-id');

            //Add Form Markup;
            var form = '<div class="easy-coupon-coupon-input"><form><input type="hidden" value="'+vidID+'" name="vid_id"/> <input type="text" maxlength="4" name="coupon_code"/> <input type="submit" value="Unlock"/></form></div>';

            $( form ).insertAfter( $(this));
        });

        // Handle unlock form submittion.
        $('.easy-coupon').on('submit','form',function(e){
            e.preventDefault();
            var formData = $(this).form();

            $.ajax({
                url: EasyCoupons.ajaxurl,
                type: 'post',
                data: {
                    'action':'unlock_a_vid',
                    'vid_id': formData["vid_id"],
                    'coupon' : formData["coupon_code"]
                },
                success: function( response ) {
                    console.log(response);
                    if(response == "code_used"){
                        alert("Coupon Alredy Used");
                    }else if(response == "code_invalid"){
                        alert("Coupon Invalid");
                    }else{
                        //Replace to iframe
                        var url = response.replace("watch?v=", "embed/");
                        var iframe = "<iframe class='responsive-iframe' src='"+url+"'></iframe>";
                        $("#easy-coupon-"+formData["vid_id"]+" .vidcontainer").html(iframe);
                        alert("Video Unlocked!");
                    }
                    console.log(url);
                },
            });
        });



    }) // on ready function



})(jQuery);
