
# eSewa SDK

The eSewa PHP Library is a tool designed for integrating the eSewa payment gateway into PHP-based web applications. It facilitates seamless processing of payments by providing functionalities to send payment requests and handle responses from the eSewa platform.

## Installation

Install my-project with npm

```bash
  composer require wslib/esewa
```


    
## Usage/Examples

```php
use Wslib\Esewa;

$esewa = Esewa::init()
$esewa->config("transaction_id", "success_url", "failure_url", "amount");
```

```php
// verify the transaction
$esewa->validate("transaction_id", "amount")
```
## Authors

- [@sgrgug](https://www.github.com/sgrgug)
