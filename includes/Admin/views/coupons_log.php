<h1>Coupons Log</h1>
<?php //var_dump( ec_delete_by('2021-08-28') );?>
<form id="coupon-log-delete-by-date">
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-">
            <th valign="top" scope="row">
                <label for="expire_date"><?php _e('Delete By Date', 'easycoupons')?></label>
            </th>
            <td>
                <input type="hidden" name="action" value="delete_coupon_log_by_expiry_date">
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
        <th>Video Title</th>
        <th>Type</th>
        <th>Date Time</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php
        if (ec_coupons_logs()) :
            foreach( ec_coupons_logs() as $coupon) :
    ?>
    <tr>
        <td><?php echo $coupon->coupon; ?></td>
        <td><?php echo $coupon->video_title; ?></td>
        <td>
            <?php
                $status = '';
                if ( $coupon->status == 1) {
                    $status .= '<span style="color:#00a32a;">Valid</span>';
                } else if ( $coupon->status == 2) {
                $status .= '<span style="color:#b32d2e;">Already Used</span>';
            } else if ( $coupon->status == 4) {
                $status .= '<span style="color:#b32d2e;">Expired</span>';
            }
                echo $status;
            ?>
        </td>
        <td><?php echo $coupon->created_at; ?></td>
        <td>
            <a href="#" id="delete-coupon-log" data-id="<?php echo $coupon->id; ?>">Delete</a>
        </td>
    </tr>
   <?php
            endforeach;
        endif;
   ?>
    </tbody>
</table>