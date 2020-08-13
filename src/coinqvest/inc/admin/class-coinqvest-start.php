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
					<h2><?php echo esc_html(__('Accept digital currencies from your clients and settle instantly in your preferred local payout currency.', 'coinqvest'))?></h2>
					<p class="fontSize16">
						<?php echo esc_html(__('Supported digital currencies: Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM), Litecoin (LTC)', 'coinqvest'))?>
                        <br />
						<?php echo esc_html(__('Supported payout currencies: USD, EUR, CAD, NGN, BRL', 'coinqvest'))?>
                    </p>
				</div>
			</div>

			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">

					<div id="postbox-container-1" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php echo esc_html(__('How it works:', 'coinqvest'))?></span></h2>
								<div class="inside">
                                    <ol>
                                        <li>
                                            <a href="https://www.coinqvest.com?utm_source=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank"><?php echo esc_html(sprintf(__('Log in or sign up on COINQVEST.', 'coinqvest')))?></a>
                                        </li>
                                        <li>
                                            <a href="https://www.coinqvest.com/en/api-settings?utm_source=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank"><?php echo esc_html(sprintf(__('Get your API Keys.', 'coinqvest')))?></a>
                                        </li>
                                        <li>
                                            <a href="<?=esc_url(admin_url('admin.php?page=coinqvest-settings'))?>"><?php echo esc_html(__('Enter API key and secret on the settings page.', 'coinqvest'))?></a>
                                        </li>
                                        <li>
	                                        <a href="<?=esc_url(admin_url('admin.php?page=coinqvest-add-payment-button'))?>"><?php echo esc_html(__('Create a new payment button', 'coinqvest'))?></a>.
                                        </li>
                                        <li>
	                                        <?php echo esc_html(__('Embed that payment button with the generated shortcode into your Wordpress page or theme.', 'coinqvest'))?>
                                        </li>
                                        <li>
	                                        <?php echo esc_html(__('Manage all payments in your merchant account on www.coinqvest.com. You will be notified by email about every new payment.', 'coinqvest'))?>
                                        </li>
                                    </ol>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<div id="dashboard_site_health" class="postbox ">
								<h2 class="hndle"><span><?php echo esc_html(__('COINQVEST Resources', 'coinqvest'))?></span></h2>
								<div class="inside">
                                    <ul>
                                        <li>- <?php echo esc_html(__('API documentation', 'coinqvest'))?> -> <a href="https://www.coinqvest.com/en/api-docs#post-checkout-hosted?utm_source=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank"><?php echo esc_html(__('Hosted Checkouts', 'coinqvest'))?></a></li>
                                        <li>- <a href="https://www.coinqvest.com/en/blog?utm_source=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank"><?php echo esc_html(__('Blog', 'coinqvest'))?></a></li>
                                        <li>- <a href="https://www.coinqvest.com/en/help-center#overview?utm_source=<?php echo esc_html($_SERVER['SERVER_NAME'])?>" target="_blank"><?php echo esc_html(__('Help Center', 'coinqvest'))?></a></li>
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