<h1>All Coupons</h1>
<table id="table_id" class="display">
    <thead>
    <tr>
        <th>Coupon</th>
        <th>Expiry Date</th>
        <th>Used</th>
        <th>Created By</th>
    </tr>
    </thead>
    <tbody>
    <?php
        if (ec_coupons()) :
            foreach( ec_coupons() as $coupon) :
                $user = get_user_by( 'id', $coupon->created_by );
    ?>
    <tr>
        <td><?php echo $coupon->coupon; ?></td>
        <td><?php echo $coupon->expiry_date; ?></td>
        <td><?php echo $coupon->is_used; ?></td>
        <td><?php echo $user->user_login; ?></td>
    </tr>
   <?php
            endforeach;
        endif;
   ?>
    </tbody>
</table>