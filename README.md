# Whalestack WordPress Plugin for Bitcoin & Stablecoin (USDC, EURC) Payments

This is the official WordPress Plugin for Whalestack, a leading cryptocurrency payment processor. Add cryptocurreny payment options like Bitcoin, Litecoin, USDC, EURC, and Lightning to your WordPress website using Whalestack. Settle in fiat or stablecoin with ease. Integrate checkouts supporting Bitcoin, USDC, EURC, and Lightning, directly into your WordPress site. Leverage our unique bank payout feature, ensuring you can effortlessly convert cryptocurrency payments into fiat or stablecoins.

This WordPress plugin implements the PHP REST API documented at https://www.whalestack.com/en/api-docs

Key Features
------------

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

Supported Currencies
------------

Argentine Peso (ARS), Australian Dollar (AUD), Bahraini Dinar (BHD), Bangladeshi Taka (BDT), Bermudian Dollar (BMD), Bitcoin (BTC), Brazilian Real (BRL), British Pound (GBP), Canadian Dollar (CAD), Chilean Peso (CLP), Chinese Yuan (CNY), Czech Koruna (CZK), Danish Krone (DKK), Emirati Dirham (AED), Ethereum (ETH), Euro (EUR), Hong Kong Dollar (HKD), Hungarian Forint (HUF), Indian Rupee (INR), Indonesian Rupiah (IDR), Israeli Shekel (ILS), Japanese Yen (JPY), Korean Won (KRW), Kuwaiti Dinar (KWD), Litecoin (LTC), Malaysian Ringgit (MYR), Mexican Peso (MXN), Myanmar Kyat (MMK), New Zealand Dollar (NZD), Nigerian Naira (NGN), Norwegian Krone (NOK), Pakistani Rupee (PKR), Philippine Peso (PHP), Polish Zloty (PLN), Ripple (XRP), Russian Ruble (RUB), Saudi Arabian Riyal (SAR), Singapore Dollar (SGD), South African Rand (ZAR), Sri Lankan Rupee (LKR), Stellar (XLM), Swedish Krona (SEK), Swiss Franc (CHF), Taiwan Dollar (TWD), Thai Baht (THB), Turkish Lira (TRY), Ukrainian Hryvnia (UAH), US Dollar (USD), Venezuelan Bolivar (VEF), Vietnamese Dong (VND)

Requirements
------------
* WordPress >= 4.9
* PHP >= 5.6

Installation as Plugin
---------------------
**Requirements**

* A Whalestack merchant account -> Sign up [here](https://www.whalestack.com)

**Plugin installation**

1. Copy the entire `whalestack` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).
1. Find the **Whalestack** menu in your WordPress admin screen.

**Plugin configuration**

1. Get your [API key and secret](https://www.whalestack.com/en/api-settings) from your Whalestack merchant account.
1. Enter API key and secret into the Whalestack plugin settings page.
1. Create a new payment button and copy the generated shortcode into your page, post or widget.
1. Manage all payments in your merchant account. You will be notified by email about every new payment.

Please inspect our [API documentation](https://www.whalestack.com/en/api-docs) for more info or send us an email to service@whalestack.com.

Support and Feedback
--------------------
Your feedback is appreciated! If you have specific problems or bugs with this WordPress plugin, please file an issue on Github. For general feedback and support requests, send an email to service@whalestack.com.

Contributing
------------

1. Fork it ( https://github.com/whalestackhq/wordpress-plugin/fork )
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create a new Pull Request