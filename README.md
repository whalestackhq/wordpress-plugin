# COINQVEST WordPress Plugin

WordPress Plugin for COINQVEST. Accept and settle payments in digital currencies on your WordPress site.

This WordPress plugin implements the PHP REST API documented at https://www.coinqvest.com/en/api-docs

Read our Merchant API [development guide](https://www.coinqvest.com/en/blog/guide-mastering-cryptocurrency-checkouts-with-coinqvest-merchant-apis-321ac139ce15) and the examples below to help you get started.

Key features
------------

* Accept Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM) and Litecoin (LTC) payments from your users.
* Get instantly settled in your preferred local currency (USD, EUR, CAD, NGN). View all supported [fiat currencies](https://www.coinqvest.com/en/fiat-currency-integrations)
* No e-commerce setup required to integrate directly on your Wordpress site.
* Create a custom shortcode and embed in any page, post or widget
* Price in your local currency
* No chargebacks, you control refunds
* No currency volatility risks due to instant conversions and settlement
* Control tax compliance levels (none, minimal, compliant)
* Custom payment button text available
* Custom payment button CSS class available

Requirements
------------
* Wordpress >= 4.9
* PHP >= 5.6


Installation as Plugin
---------------------
**Requirements**

* A COINQVEST merchant account -> [Signup here](http://www.coinqvest.com)

**Plugin installation**

* Upload the entire `coinqvest` folder to the `/wp-content/plugins/` directory.
* Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
* You will find the **COINQVEST** menu in your WordPress admin screen.

**Plugin configuration**

* Get [API key and API secret](https://www.coinqvest.com/en/api-settings) from your COINQVEST merchant account.
* Enter API key and secret into the COINQVEST plugin settings page.
* Create a new payment button and copy the generated shortcode into your page, post or widget.
* Manage all payments in your [merchant account](https://www.coinqvest.com). You will be notified by email about every new payment.

Please inspect https://www.coinqvest.com/en/api-docs for detailed API documentation or send us an email to service@coinqvest.com.

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