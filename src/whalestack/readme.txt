=== Whalestack - Bitcoin & Stablecoin (USDC, EURC) Payments Plugin for WordPress ===
Contributors: whalestack
Tags: crypto, cryptocurrency, stablecoins, USDC, EURC, payments, payment gateway, payment processing, digital currencies, bitcoin, lightning, ethereum, ether, litecoin, stellar, lumens, xlm, btc, eth, ltc, EUR, USD, BRL
Requires at least: 3.9
Tested up to: 6.4
Stable tag: 2.0
Requires PHP: 5.6
License: Apache 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

== Description ==

Add cryptocurreny payment options like Bitcoin, Litecoin, USDC, EURC, and Lightning to your WordPress website using Whalestack. Settle in fiat or stablecoin with ease. Integrate checkouts supporting Bitcoin, USDC, EURC, and Lightning, directly into your WordPress site. Leverage our unique bank payout feature, ensuring you can effortlessly convert cryptocurrency payments into fiat or stablecoins.

With the WordPress plugin for Bitcoin and stablecoin payments you go global from day one. Enjoy built-in multi-currency support and seamless adaptability to diverse languages, ensuring a globally accessible and user-friendly shopping experience for your customers.

Boost your sales and attract a broader customer base within the thriving demographic of crypto and stablecoin enthusiasts who prefer cutting-edge payment methods.O ptimize conversion rates and enhance customer satisfaction by optimizing conversion rates through the implementation of innovative payment methods tailored to specific buyer preferences.

= About Whalestack =

Whalestack stands as a leader in the digital currency payment sector, offering innovative solutions designed to streamline financial operations for modern businesses. By integrating blockchain technology with traditional banking systems, Whalestack provides a unique blend of services tailored to meet the evolving needs of digitally-focused businesses and entrepreneurs.

= Key features =

* Accepts cryptocurrencies (BTC, Lightning, LTC, XLM) and stablecoins (USDC, EURC) on your WordPress website from customers.
* Instantly settles in your preferred national currency (USD, EUR, BRL) or above cryptocurrencies.
* Sets the product price in your national currency - 45 fiat currencies are available, see full list [here](https://www.whalestack.com/en/currencies).
* Doesn't require any e-commerce setup on your WordPress website.
* Embeds a payment button in any page, post or widget of your WordPress website.
* Sets the checkout language in your preferred language.
* Eliminates chargebacks and gives you control over refunds.
* Eliminates currency volatility risks due to instant conversions and settlement.
* Controls tax compliance levels.
* Automatically generates invoices if tax compliance level is set to compliant.
* Provides custom payment button text.
* Provides custom payment button CSS classes available.
* Translates the plugin into any required language.

= Supported Currencies =

Argentine Peso (ARS), Australian Dollar (AUD), Bahraini Dinar (BHD), Bangladeshi Taka (BDT), Bermudian Dollar (BMD), Bitcoin (BTC), Brazilian Real (BRL), British Pound (GBP), Canadian Dollar (CAD), Chilean Peso (CLP), Chinese Yuan (CNY), Czech Koruna (CZK), Danish Krone (DKK), Emirati Dirham (AED), Ethereum (ETH), Euro (EUR), Hong Kong Dollar (HKD), Hungarian Forint (HUF), Indian Rupee (INR), Indonesian Rupiah (IDR), Israeli Shekel (ILS), Japanese Yen (JPY), Korean Won (KRW), Kuwaiti Dinar (KWD), Litecoin (LTC), Malaysian Ringgit (MYR), Mexican Peso (MXN), Myanmar Kyat (MMK), New Zealand Dollar (NZD), Nigerian Naira (NGN), Norwegian Krone (NOK), Pakistani Rupee (PKR), Philippine Peso (PHP), Polish Zloty (PLN), Ripple (XRP), Russian Ruble (RUB), Saudi Arabian Riyal (SAR), Singapore Dollar (SGD), South African Rand (ZAR), Sri Lankan Rupee (LKR), Stellar (XLM), Swedish Krona (SEK), Swiss Franc (CHF), Taiwan Dollar (TWD), Thai Baht (THB), Turkish Lira (TRY), Ukrainian Hryvnia (UAH), US Dollar (USD), Venezuelan Bolivar (VEF), Vietnamese Dong (VND)

== Installation ==

= Requirements =

* A Whalestack merchant account -> Sign up [here](http://www.whalestack.com)

= Plugin installation =

1. Copy the entire `whalestack` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
1. Find the **Whalestack** menu in your WordPress admin screen.

= Plugin configuration =

1. Get your [API key and secret](https://www.whalestack.com/en/api-settings) from your Whalestack merchant account.
1. Enter API key and secret into the Whalestack plugin settings page.
1. Create a new payment button and copy the generated shortcode into your page, post or widget.
1. Manage all payments in your merchant account. You will be notified by email about every new payment.

== Frequently Asked Questions ==

Do you have questions or issues with Whalestack? Feel free to contact us anytime!

* [Plugin Guide](https://www.whalestack.com/en/wordpress)
* [API Docs](https://www.whalestack.com/en/api-docs#post-checkout-hosted)
* [Help Center](https://www.whalestack.com/en/help-center#overview)

== Screenshots ==

1. Checkout Modal Options (Minimal and Tax Compliant)
2. Hosted Checkout Page
3. Whalestack Merchant Dashboard
4. Whalestack Transaction Records and Invoicing
5. Whalestack Instant Withdrawals

== Changelog ==

= 2.0.0 =

* Rebranded from COINQVEST to Whalestack

= 1.1.9 =

* Updated settlement currency to reflect new API response from /asset endpoint (asset.id)

= 1.1.8 =

* Minor text updates

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
