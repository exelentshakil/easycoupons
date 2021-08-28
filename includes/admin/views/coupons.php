<h1>All Coupons</h1>
<?php //var_dump( ec_delete_by('2021-08-28') );?>
<form id="coupon-delete-by-date">
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-">
            <th valign="top" scope="row">
                <label for="expire_date"><?php _e('Delete By Date', 'easycoupons')?></label>
            </th>
            <td>
                <input type="hidden" name="action" value="delete_coupon_by_expiry_date">
                <input id="expire_date" name="expire_date" type="date" class="regular-text" min="<?php echo date('Y-m-d'); ?>" required>
                <input type="submit" value="Delete">
            </td>
        </tr>
        </tbody>
    </table>
</form>

<table id="coupons-table" class="display">
    <thead>
    <tr>
        <th>Coupon</th>
        <th>Expiry Date</th>
        <th>Used</th>
        <th>Created By</th>
        <th>Actions</th>
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
        <td>
            <a href="#" id="delete-coupon" data-id="<?php echo $coupon->id; ?>">Delete</a>
        </td>
    </tr>
   <?php
            endforeach;
        endif;
   ?>
    </tbody>
</table>