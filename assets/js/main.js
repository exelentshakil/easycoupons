(function ($) {
    "use strict";
    $('#coupons-table').DataTable();

    $(document).ready(function () {

        var coupons_table = $('#coupons-table');

        coupons_table.on('click', '#delete-coupon', function() {
            var id = $(this).data('id');
            var data = {id: id, action: 'delete_coupon'};

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Deleting coupons... Are you sure?',
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

    }) // on ready function



})(jQuery);
