<div class="wrap">

    <h2><?php _e('Payment Buttons', 'coinqvest')?></h2>

    <p><a href="/wp-admin/admin.php?page=coinqvest-add-payment-button"><?php _e('Create new payment button', 'coinqvest')?></a></p>

    <p><?php _e('Copy and paste the Shortcode below to a Post, Page or Widget', 'coinqvest')?></p>

    <div id="payment-buttons-list-table">
        <div id="payment-buttons-post-body">
            <form id="payment-buttons-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php
				$this->payment_buttons_list_table->display();
				?>
            </form>
        </div>
    </div>
</div>