# COINQVEST WordPress Plugin

This is the official WordPress Plugin for COINQVEST. Accept and settle payments in digital currencies on your WordPress site.

This WordPress plugin implements the PHP REST API documented at https://www.coinqvest.com/en/api-docs

Key Features
------------

* Accepts Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM) and Litecoin (LTC) payments from customers.
* Instantly settles in your preferred national currency (USD, EUR, CAD, NGN).
* Doesn't require any e-commerce setup on your WordPress site.
* Embeds a payment button in any page, post or widget of your WordPress site.
* Sets the product price in your national currency.
* Eliminates chargebacks and gives you control over refunds.
* Eliminates currency volatility risks due to instant conversions and settlement.
* Controls tax compliance levels (none, minimal, compliant).
* Provides custom payment button text.
* Provides custom payment button CSS classes available.
* Translates the plugin into any required language.

Requirements
------------
* WordPress >= 4.9
* PHP >= 5.6


Installation as Plugin
---------------------
**Requirements**

* A COINQVEST merchant account -> Sign up [here](https://www.coinqvest.com)

**Plugin installation**

1. Copy the entire `coinqvest` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
1. Find the **COINQVEST** menu in your WordPress admin screen.

**Plugin configuration**

1. Get your [API key and secret](https://www.coinqvest.com/en/api-settings) from your COINQVEST merchant account.
1. Enter API key and secret into the COINQVEST plugin settings page.
1. Create a new payment button and copy the generated shortcode into your page, post or widget.
1. Manage all payments in your [merchant account](https://www.coinqvest.com). You will be notified by email about every new payment.

Please inspect our [API documentation](https://www.coinqvest.com/en/api-docs) for more info or send us an email to service@coinqvest.com.

Support and Feedback
--------------------
Your feedback is appreciated! If you have specific problems or bugs with this WordPress plugin, please file an issue on Github. For general feedback and support requests, send an email to service@coinqvest.com.

Contributing
------------

1. Fork it ( https://github.com/COINQVEST/wordpress-plugin/fork )
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create a new Pull Request