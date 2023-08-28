# AzEconomy

AzEconomy is a simple plugin designed for Pocketmine-MP servers, offering a multi-economy system.
##### Plugin is under development, if you find any bug, please report it.
#### Features

- Multi-economy system
- SQLite database
- Multi-language system
- FormAPI support
#### To do:

- [ ] MySQL support
- [ ] More...

#### Commands

| Command                                        | Description               | Permission |
|------------------------------------------------|---------------------------| --- |
| `/azeconomy currencies`                        | Show your currencies      | `azeconomy.command.currencies` |
| `/azeconomy pay <player> <currency> <amount>`  | Pay currency to a player  | `azeconomy.command.pay` |
| `/azeconomy set <player> <currency> <amount>`  | Set currency to a player  | `azeconomy.command.set` |
| `/azeconomy take <player> <currency> <amount>` | Take currency to a player | `azeconomy.command.add` |
| `/azeconomy give <player> <currency> <amount>` | Give currency to a player | `azeconomy.command.remove` |
| `/azeconomy top`                               | Show top players          | `azeconomy.command.top` |


#### Events

| Event                                     | Description |
|-------------------------------------------| --- |
| `EconomyTransactionEvent` | Called when a player's currency changes |

#### API

Get Currency:
```php
$callback = function(float $currency) : void {
    // Do something
};

EcoAPI::getCurrency(string $username, string $currency, callable $callback);
```
Get Currencies:
```php
$callback = function(?BaseCurrencies $currency) : void {
    // Do something
};
EcoAPI::getCurrencies(string $username, callable $callback);
```

Set Currency:
```php
EcoAPI::setCurrency(string $username, string $currency, float $amount);
```
Add Currency:
```php
EcoAPI::addCurrency(string $username, string $currency, float $amount);
```
Remove Currency:
```php
EcoAPI::removeCurrency(string $username, string $currency, float $amount);
```