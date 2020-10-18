# Weather console Task

This project is a PHP-based command line app to get the current weather of any city.

## Example
```
weather London
Overcast clouds, 12 degrees celcius
```
```
weather Antwerp
Few clouds, 12 degrees celcius
```

## Getting started

Install composer dependencies:
```
composer install
```

Add the Open Weather API key in the .env file in the project root directory.

Set an alias:
```
alias weather='php /path/to/project/index.php'
```

Execute the weather command with the city you would like get its current weather:

```
weather London
```

To run tests:

```
./vendor/bin/phpunit tests
```
