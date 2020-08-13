=== COINQVEST - Bitcoin and other cryptocurrency payment processing ===
Contributors: coinqvest
Tags: crypto, cryptocurrency, payments, payment gateway, payment processing, digital currencies, bitcoin, stellar, lumens, xlm, btc, eth, xrp, ltc, EUR, USD, CAD, NGN, BRL
Requires at least: 3.9
Tested up to: 5.5
Stable tag: 0.1
Requires PHP: 5.6
License: Apache 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

Accept digital currencies from your clients and settle instantly in your preferred local payout currency.

== Description ==

COINQVEST provides digital currency checkouts that automatically go from Bitcoin to your bank account, in minutes. COINQVEST helps you programmatically accept and settle payments in new digital currencies while staying compliant, keeping your accountants and tax authorities happy. With COINQVEST, sales can be denominated and settled in your local fiat currency (e.g. EUR, USD, CAD, BRL or NGN) regardless of whether your customers pay in Bitcoin, Ethereum or Stellar Lumens.

= Key features =

* Accepts Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM) and Litecoin (LTC) payments from customers.
* Instantly settles in your preferred national currency (USD, EUR, CAD, NGN, BRL).
* Doesn't require any e-commerce setup on your WordPress site.
* Embeds a payment button in any page, post or widget of your WordPress site.
* Sets the product price in your national currency.
* Eliminates chargebacks and gives you control over refunds.
* Eliminates currency volatility risks due to instant conversions and settlement.
* Controls tax compliance levels (none, minimal, compliant).
* Automatically generates invoices if tax compliance level is set to compliant.
* Provides custom payment button text.
* Provides custom payment button CSS classes available.
* Translates the plugin into any required language.

= Use case =

Example: You sell an e-book for 20 USD on your website. Your user pays in Bitcoin and you will receive 20 USD in your bank account. Within minutes. All you need is to implement the COINQVEST checkout button into your WordPress site.

= Docs and support =

You can find the [plugin guide](https://www.coinqvest.com/en/blog/a-guide-to-cryptocurrency-payment-processing-with-coinqvest-and-wordpress-b0f7246517c9?utm_source=wordpress.org), [API documentation](https://www.coinqvest.com/en/api-docs#post-checkout-hosted?utm_source=wordpress.org), [Help Center](https://www.coinqvest.com/en/help-center#overview?utm_source=wordpress.org) and more detailed information about COINQVEST on [coinqvest.com](https://www.coinqvest.com/?utm_source=wordpress.org).

== Installation ==

= Requirements =

* A COINQVEST merchant account -> Sign up [here](http://www.coinqvest.com?utm_source=wordpress.org)

= Plugin installation =

1. Copy the entire `coinqvest` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
1. Find the **COINQVEST** menu in your WordPress admin screen.

= Plugin configuration =

1. Get your [API key and secret](https://www.coinqvest.com/en/api-settings?utm_source=wordpress.org) from your COINQVEST merchant account.
1. Enter API key and secret into the COINQVEST plugin settings page.
1. Create a new payment button and copy the generated shortcode into your page, post or widget.
1. Manage all payments in your [merchant account](https://www.coinqvest.com?utm_source=wordpress.org). You will be notified by email about every new payment.

== Frequently Asked Questions ==

Do you have questions or issues with COINQVEST? Feel free to contact us anytime!

* [Plugin Guide](https://www.coinqvest.com/en/blog/a-guide-to-cryptocurrency-payment-processing-with-coinqvest-and-wordpress-b0f7246517c9?utm_source=wordpress.org)
* [API Docs](https://www.coinqvest.com/en/api-docs#post-checkout-hosted?utm_source=wordpress.org)
* [Help Center](https://www.coinqvest.com/en/help-center#overview?utm_source=wordpress.org)

== Screenshots ==

1. Checkout Modal Options (Minimal and Tax Compliant)
2. Hosted Checkout Page
3. COINQVEST Merchant Dashboard
4. COINQVEST Transaction Records and Invoicing
5. COINQVEST Instant Withdrawals

== Changelog ==

= 0.0.6 =

* Tested for WordPress version 5.5

= 0.0.5 =

* Added Brazilian Real (BRL) as settlement currency
* Removed trailing zeros when displaying digital currencies
* Added CSS style `height: 100%` for modal input fields

= 0.0.4 =

* Fixed version number
* Code cleanup

= 0.0.3 =

* Fixed incompatibility with Bootstrap modal
* Added plugin settings link

= 0.0.2 =

* Field mobile_number changed to phone_number
