# Requirements

-   PHP >= 8.4
-   Laravel >= 12

# Installing

*   Via Composer (Recomended)

```bash
composer require chargily/chargily-pro-laravel
```
* The Service Provider will automatically be registered; however, if you wish to manually register it, you can manually add the `Chargily\ChargilyProLaravel\ChargilyProServiceProvider::class` service provider to the array in bootstrap/providers.php (config/app.php in Laravel 10 or older).

* You should publish the config/chargily-pro.php config file with:
```bash
php artisan vendor:publish --provider="Chargily\ChargilyProLaravel\ChargilyProServiceProvider::class"
```

# Configuration
* Add the following configurations to `.env` file

```env
# Chargily Pro
CHARGILY_PRO_MODE="live"
CHARGILY_PRO_NAME="username"
CHARGILY_PRO_PUBLIC_KEY="your-public-key"
CHARGILY_PRO_SECRET_KEY="your-secret-key"
```
# Usage

1. TopUps

* Modes List
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::modes()
```
* Retrieve Mode
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::getMode("5");
```
* Operators List
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::operators()
```
* Retrieve Operator
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::getOperator("Djezzy");
```
* Create new topup request
```php
    /**
     * Both returns:
     * 1. Related model on success.
     * 2. null
     * 3. Throw exceptions when an error occurs.
     */
    /**
     * Request via operator name,mode name and mode value 
     */
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::request("DZ", "0790000000", "Djezzy", "DJEZZY ZID 50", "50");
    /**
     * Also you can request via mode id only
     */
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::requestById("DZ", "0790000000", "203");
    //
```
* Retrieve TopUp request details
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProTopUp::getRequest("5");
```


2. Voucher & Gift cards

* Get Availlable vouchers list
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProVoucher::all();
```
* Retrieve Voucher details
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProVoucher::get("2");
```
* Create new voucher request
```php
    /**
     * Both returns:
     * 1. Related model on success.
     * 2. null
     * 3. Throw exceptions when an error occurs.
     */
    /**
     * Request via voucher name and value 
     */
    \Chargily\ChargilyProLaravel\Facades\ChargilyProVoucher::request("Mobilis", "100 DA");
    /**
     * Also you can request via voucher id
     */
    \Chargily\ChargilyProLaravel\Facades\ChargilyProVoucher::requestById("2");
    //
```
* Get Sold vouchers
```php
    \Chargily\ChargilyProLaravel\Facades\ChargilyProVoucher::sold();
```
