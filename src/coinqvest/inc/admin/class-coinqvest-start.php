<?php
namespace COINQVEST\Inc\Admin;

class Coinqvest_Start {

	private $plugin_name_url;

	public function __construct($plugin_name_url) {

		$this->plugin_name_url = $plugin_name_url;

	}

	public function render_coinqvest_start_page() {

		?>
		<div class="wrap">

			<img class="responsive paddingTop10" src="<?=$this->plugin_name_url?>assets/images/admin-banner-very-thin.jpg">

			<div id="welcome-panel" class="welcome-panel">
				<div class="welcome-panel-content">
					<h2><?php esc_attr(_e('Accept digital currencies from your clients and settle instantly in your preferred local payout currency.', 'coinqvest'))?></h2>
					<p class="fontSize16">
						<?php esc_attr(_e('Supported digital currencies: Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM), Litecoin (LTC)', 'coinqvest'))?>
                        <br />
						<?php esc_attr(_e('Supported payout currencies: USD, EUR, CAD, NGN', 'coinqvest'))?>
                    </p>
				</div>
			</div>


			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">

					<div id="postbox-container-1" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php esc_attr(_e('How it works:', 'coinqvest'))?></span></h2>
								<div class="inside">
                                    <ol>
                                        <li>
	                                        <?php echo sprintf(esc_attr(__('Go to %s and log in or sign up.', 'coinqvest')), '<a href="https://www.coinqvest.com" target="_blank">www.coinqvest.com</a>')?>
                                        </li>
                                        <li>
	                                        <?php esc_attr(_e('Find your API keys here', 'coinqvest'))?>: <a href="https://www.coinqvest.com/en/api-settings" target="_blank">www.coinqvest.com/en/api-settings</a>
                                        </li>
                                        <li>
	                                        <?php echo sprintf(esc_attr(__('Enter API key and secret on the %1$s settings page. %2$s', 'coinqvest')), '<a href="' . esc_url(admin_url('admin.php?page=coinqvest-settings')) . '">', '</a>')?>
                                        </li>
                                        <li>
	                                        <a href="<?=esc_url(admin_url('admin.php?page=coinqvest-add-payment-button'))?>"><?php esc_attr(_e('Create a new payment button', 'coinqvest'))?></a>.
                                        </li>
                                        <li>
	                                        <?php esc_attr(_e('Embed that payment button with the generated shortcode into your Wordpress page or theme.', 'coinqvest'))?>
                                        </li>
                                        <li>
	                                        <?php echo esc_attr(sprintf(__('Manage all payments in your merchant account on %s. You will be notified by email about every new payment.', 'coinqvest'), '<a href="https://www.coinqvest.com" target="_blank">www.coinqvest.com</a>'))?>
                                        </li>
                                    </ol>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php esc_attr(_e('COINQVEST Resources', 'coinqvest'))?></span></h2>
								<div class="inside">
                                    <ul>
                                        <li>- <?php esc_attr(_e('API documentation', 'coinqvest'))?> -> <a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted" target="_blank"><?php esc_attr(_e('Hosted Checkouts', 'coinqvest'))?></a></li>
                                        <li>- <a href="https://www.coinqvest.com/en/blog" target="_blank"><?php esc_attr(_e('Blog', 'coinqvest'))?></a></li>
                                        <li>- <a href="https://www.coinqvest.com/en/help-center#overview" target="_blank"><?php esc_attr(_e('Help Center', 'coinqvest'))?></a></li>
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