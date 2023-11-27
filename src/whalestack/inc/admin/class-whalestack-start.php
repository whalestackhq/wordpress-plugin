<?php
namespace Whalestack\Inc\Admin;

class Whalestack_Start {

	private $plugin_name_url;

	public function __construct($plugin_name_url) {

		$this->plugin_name_url = $plugin_name_url;

	}

	public function render_whalestack_start_page() {

		?>
		<div class="wrap">

			<img class="responsive paddingTop10" src="<?=$this->plugin_name_url?>assets/images/admin-banner-very-thin.jpg">

			<div id="welcome-panel">
				<div class="">
					<h2><?php echo esc_html(__('Accept digital currencies from your clients and settle instantly in your preferred local payout currency.', 'whalestack'))?></h2>
					<p class="fontSize16">
						<?php echo esc_html(__('Supported currencies: BTC, Lightning, LTC, XLM, USDC, EURC', 'whalestack'))?>
                    </p>
				</div>
			</div>

			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">

					<div id="postbox-container-1" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php echo esc_html(__('How it works:', 'whalestack'))?></span></h2>
								<div class="inside">
                                    <ol>
                                        <li>
                                            <a href="https://www.whalestack.com" target="_blank"><?php echo esc_html(sprintf(__('Log in or sign up on Whalestack.', 'whalestack')))?></a>
                                        </li>
                                        <li>
                                            <a href="https://www.whalestack.com/en/api-settings" target="_blank"><?php echo esc_html(sprintf(__('Get your API Keys.', 'whalestack')))?></a>
                                        </li>
                                        <li>
                                            <a href="<?=esc_url(admin_url('admin.php?page=whalestack-settings'))?>"><?php echo esc_html(__('Enter API key and secret on the settings page.', 'whalestack'))?></a>
                                        </li>
                                        <li>
	                                        <a href="<?=esc_url(admin_url('admin.php?page=whalestack-add-payment-button'))?>"><?php echo esc_html(__('Create a new payment button', 'whalestack'))?></a>.
                                        </li>
                                        <li>
	                                        <?php echo esc_html(__('Embed that payment button with the generated shortcode into your Wordpress page or theme.', 'whalestack'))?>
                                        </li>
                                        <li>
	                                        <?php echo esc_html(__('Manage all payments in your merchant account on www.whalestack.com. You will be notified by email about every new payment.', 'whalestack'))?>
                                        </li>
                                    </ol>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php echo esc_html(__('Walestack Resources', 'whalestack'))?></span></h2>
								<div class="inside">
                                    <ul>
                                        <li>- <?php echo esc_html(__('API Documentation', 'whalestack'))?> -> <a href="https://www.whalestack.com/en/api-docs#post-checkout-hosted" target="_blank"><?php echo esc_html(__('Hosted Checkouts', 'whalestack'))?></a></li>
                                        <li>- <a href="https://www.whalestack.com/en/wordpress" target="_blank"><?php echo esc_html(__('Installation Guide', 'whalestack'))?></a></li>
                                        <li>- <a href="https://www.whalestack.com/en/help-center#overview" target="_blank"><?php echo esc_html(__('Help Center', 'whalestack'))?></a></li>
                                    </ul>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>

	<?php

	}

}