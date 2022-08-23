=== COINQVEST - Cryptocurrency Payment Gateway for Bitcoin ===
Contributors: coinqvest
Tags: crypto, cryptocurrency, payments, payment gateway, payment processing, digital currencies, bitcoin, ethereum, ether, litecoin, ripple, stellar, lumens, xlm, btc, eth, xrp, ltc, EUR, USD, NGN, BRL
Requires at least: 3.9
Tested up to: 6.0
Stable tag: 1.1
Requires PHP: 5.6
License: Apache 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

== Description ==

COINQVEST is a [cryptocurrency payment processor](https://www.coinqvest.com) and provides digital currency checkouts that automatically go from Bitcoin to your bank account or crypto wallet. COINQVEST helps online merchants and e-commerce shops programmatically accept and settle payments in new digital currencies while staying compliant, keeping their accountants and tax authorities happy. With COINQVEST, online businesses can denominate and settle sales in a national currency (e.g. EUR, USD, ARS, BRL or NGN) regardless of whether their customers pay in Bitcoin, Ethereum or Stellar Lumens.

The COINQVEST crypto payment gateway supports 45 billing currencies and easily lets you add a crypto payment option to your website or online shop to sell digital content, services, products and much more in your national currency.

= Key features =

* Accepts Bitcoin (BTC), Ethereum (ETH), Ripple (XRP), Stellar Lumens (XLM) and Litecoin (LTC) payments on your WordPress website from customers.
* Instantly settles in your preferred national currency (USD, EUR, ARS, BRL, NGN) or above crypto currencies.
* Sets the product price in your national currency - 45 fiat currencies are available, see full list [here](https://www.coinqvest.com/en/api-docs#get-exchange-rate-global).
* Doesn't require any e-commerce setup on your WordPress website.
* Embeds a payment button in any page, post or widget of your WordPress website.
* Sets the checkout language in your preferred language.
* Eliminates chargebacks and gives you control over refunds.
* Eliminates currency volatility risks due to instant conversions and settlement.
* Controls tax compliance levels (none, minimal, compliant).
* Automatically generates invoices if tax compliance level is set to compliant.
* Provides custom payment button text.
* Provides custom payment button CSS classes available.
* Translates the plugin into any required language.

= Supported Currencies =

Argentine Peso (ARS), Australian Dollar (AUD), Bahraini Dinar (BHD), Bangladeshi Taka (BDT), Bermudian Dollar (BMD), Bitcoin (BTC), Brazilian Real (BRL), British Pound (GBP), Canadian Dollar (CAD), Chilean Peso (CLP), Chinese Yuan (CNY), Czech Koruna (CZK), Danish Krone (DKK), Emirati Dirham (AED), Ethereum (ETH), Euro (EUR), Hong Kong Dollar (HKD), Hungarian Forint (HUF), Indian Rupee (INR), Indonesian Rupiah (IDR), Israeli Shekel (ILS), Japanese Yen (JPY), Korean Won (KRW), Kuwaiti Dinar (KWD), Litecoin (LTC), Malaysian Ringgit (MYR), Mexican Peso (MXN), Myanmar Kyat (MMK), New Zealand Dollar (NZD), Nigerian Naira (NGN), Norwegian Krone (NOK), Pakistani Rupee (PKR), Philippine Peso (PHP), Polish Zloty (PLN), Ripple (XRP), Russian Ruble (RUB), Saudi Arabian Riyal (SAR), Singapore Dollar (SGD), South African Rand (ZAR), Sri Lankan Rupee (LKR), Stellar (XLM), Swedish Krona (SEK), Swiss Franc (CHF), Taiwan Dollar (TWD), Thai Baht (THB), Turkish Lira (TRY), Ukrainian Hryvnia (UAH), US Dollar (USD), Venezuelan Bolivar (VEF), Vietnamese Dong (VND)

= Use case =

Example: You sell an e-book for 20 USD on your website. Your user pays in Bitcoin and you will receive 20 USD in your bank account. Within minutes. All you need is to implement the COINQVEST checkout button into your WordPress website.

= Docs and support =

You can find the [plugin guide](https://www.coinqvest.com/en/blog/how-to-accept-bitcoin-stellar-lumens-and-other-cryptocurrencies-with-coinqvest-for-wordpress-b0f7246517c9), [API documentation](https://www.coinqvest.com/en/api-docs#post-checkout-hosted), [Help Center](https://www.coinqvest.com/en/help-center#overview) and more detailed information about COINQVEST on [coinqvest.com](https://www.coinqvest.com/).

== Installation ==

= Requirements =

* A COINQVEST merchant account -> Sign up [here](http://www.coinqvest.com)

= Plugin installation =

1. Copy the entire `coinqvest` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
1. Find the **COINQVEST** menu in your WordPress admin screen.

= Plugin configuration =

1. Get your [API key and secret](https://www.coinqvest.com/en/api-settings) from your COINQVEST merchant account.
1. Enter API key and secret into the COINQVEST plugin settings page.
1. Create a new payment button and copy the generated shortcode into your page, post or widget.
1. Manage all payments in your merchant account. You will be notified by email about every new payment.

== Frequently Asked Questions ==

Do you have questions or issues with COINQVEST? Feel free to contact us anytime!

* [Plugin Guide](https://www.coinqvest.com/en/blog/a-guide-to-cryptocurrency-payment-processing-with-coinqvest-and-wordpress-b0f7246517c9)
* [API Docs](https://www.coinqvest.com/en/api-docs#post-checkout-hosted)
* [Help Center](https://www.coinqvest.com/en/help-center#overview)

== Screenshots ==

1. Checkout Modal Options (Minimal and Tax Compliant)
2. Hosted Checkout Page
3. COINQVEST Merchant Dashboard
4. COINQVEST Transaction Records and Invoicing
5. COINQVEST Instant Withdrawals

== Changelog ==

= 1.1.7 =

* Adoption of new checkout parameter naming

= 1.1.6 =

* Minor code changes

= 1.1.5 =

* Reduced number of API calls
* CSS height fix for modal overlay
* Tested for WordPress version 6.0

= 1.1.4 =

* Added 'ORIGIN' as settlement currency option
* Text changes
* Tested for WordPress version 5.9
* Minor fixes

= 1.1.3 =

* Added checkout support for 50 currencies (45 fiat currencies and 5 cryptocurrencies), see full list [here](https://www.coinqvest.com/en/api-docs#get-exchange-rate-global)
* Cleaned up code

= 1.0.0 =

* Added cryptocurrencies as settlement currencies
* Added language selector for checkout page

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
