# CheckDuplicates Package for Laravel

The **CheckDuplicates** package is a Laravel package designed to check for duplicate rows in specified models based on configurable columns. It supports both regular and soft-deleted models and logs the results daily for easy review.

## Features

- Check for duplicate rows in one or more models.
- Supports multiple columns for duplicate checks.
- Configurable to include soft-deleted rows.
- Daily logging of duplicate checks for easy monitoring.

## Installation

You can install the package via Composer:

```bash
composer require johndivam/check-duplicates
```

## Configuration

After installation, publish the configuration file:

```bash
php artisan vendor:publish --provider="Johndivam\CheckDuplicate\CheckDuplicatesServiceProvider"
```

This will create a configuration file at `config/check-duplicates.php`.

### Configuration Options

Edit the `config/check-duplicates.php` file to specify the models and columns you want to check for duplicates:

```php
return [
    'models' => [
        [
            'model' => \App\Models\Task::class,
            'columns' => ['project_id', 'name'],
            'with_deletes' => false, // Set to false to exclude soft-deleted records
        ],
        [
            'model' => \App\Models\Order::class,
            'columns' => ['cart_id'],
            'with_deletes' => true, // Set to true to include soft-deleted records
        ],
        // Add more models as needed
    ],
];
```

## Usage

The package runs a scheduled command to check for duplicates every minute. You can manually trigger the check with:

```bash
php artisan duplicates:check
```

You may also schedule this command in your `App\Console\Kernel.php` file to run at your desired interval:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('duplicates:check')->everyMinute();
}
```

## Logging

The results of the duplicate checks are logged daily in the `storage/logs/` directory. The log file is named as `check_duplicates-YYYY-MM-DD.log`, where `YYYY-MM-DD` is the date the log was created.

### Example Log Entry

```plaintext
[2024-10-28 12:00:00] check_duplicates.INFO: Duplicate entries found in model App\Models\Task. Details: [{"project_id":1,"name":"Test Task"}]
```

## Contributing

If you would like to contribute to this package, please fork the repository and submit a pull request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
