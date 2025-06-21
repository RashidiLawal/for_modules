# BitCore

A clean, modular PHP starter by Ankabit.

This starter kit can be used directly or used in a project setup as dependencies.

## Frameworks/Libraries Used

- **HTTP/Routing/Middleware**: Slim
- **Translation**: Illuminate
- **Hooks/Events**: Illuminate
- **Storage**: Illuminate
- **Validation**: Illuminate
- **Database/ORM**: Illuminate (Eloquent)
- **DB/File-based Queue Library**: Custom
- **Logging**: Monolog

---

## Installation and Setup Instructions ( Standalone )

### Requirements

- PHP 7.4 or newer.

### Steps to Install

1. Run the following command in the root directory:
   ```bash
   composer install
   ```
2. Point your virtual host document root to the application's `public/` directory.
3. Ensure the following directories are writable by the web server:
   - `storage/logs/`
   - `storage/cache/`

### Setup env

See the sample .env.example and provide equivalent in any of these format .env.test, .env.production, .env.local, .env

### Running the Application

For development, you can run the application using:

```bash
composer start
```

Alternatively, you can use `docker-compose` to run the application with Docker:

```bash
cd [my-app-name]
docker-compose up -d
```

After starting the application, open your browser and visit:  
`http://localhost:8080`

### Run the Test Suite

To execute the test suite, run:

```bash
composer test
```

### Debugging Code

To debug your code using PHPStan, run:

```bash
composer debug
```

To check for formatting/linting issue, run:

```bash
phpcs
```

To fix the formatting/linting issue, run:

```bash
phpcbf
```

## Installation Instructions ( Dependency )

For instructions, see the [Guide on How to Use BitCore as Dependency in a Project](#guide-on-how-to-use-bitcore-as-dependency-in-a-project) section.

---

## Git Hooks Installation (Dev)

After cloning the project, install pre-commit hooks using:

```bash
./.github/install-hooks.sh
```

This will ensure that necessary formatting and debugging checks are run before every commit. Currently, the following checks are performed:

- **Code formatting**: Checked using `phpcs` and automatically fixed using `phpcbf`.
- **Bug detection**: Performed using `phpstan`.

### Manually Running Checks

- Check formatting issues:
  ```bash
  phpcs
  ```
- Fix formatting issues manually:
  ```bash
  phpcbf
  ```
- Debug code using PHPStan:
  ```bash
  composer debug
  ```

---

# Module Development Guide

## General Guidelines

- Avoid using the `\Slim` namespace in modules to ensure backward compatibility.
- Avoid direct usage of the `\Illuminate` namespace or any other library in modules to prevent breakage during updates.
- Use `\BitCore\Kernel` sparingly in modules, only when absolutely necessary.
- Adhere to **PSR coding standards** and conventions.
- Comment your code adequately:
  - Block comments are required for all methods and classes.
  - Inline/in-text comments should only be added when necessary.
- **Prioritize security**:
  - Sanitize all inputs.
  - Escape data before saving or outputting it.
- Ensure all text is translatable (internationalization).
- Remove any unused or dead code from the codebase.
- Avoid using unnecessary third-party libraries. If required, ensure they are stable and actively maintained.
- Use proper naming conventions and logical grouping to improve code clarity.
- Always document your work, including installation/setup instructions for modules.
- Simplify logic with early returns to avoid deep nesting of conditions.
- Write code that is easily extendable through OOP principles and events, allowing other modules to integrate seamlessly.
- Follow the database table prefix conventions used in the core system.
  - Use the `db()` helper to access the database instance.

## Uploading module quality check

- Check for quality of the module i.e use on bad practices like dispatched hooks i.e hooks()->dispatch('nonStringclass')
- Use of \Slim, \Illuminate namespace in the module
- Too much use of \BitCore\Kernel
- Declaration from global dangerous variables i.e $\_GET, $\_REQUEST, $\_POST e.t.c
- Lack of tests

---

## Module Development

This project is built with a modular architecture. Each module (e.g., `Invoice`, `Affiliate`, `Estimate`) is self-contained and provides its own:

- Models
- Repositories
- Actions (Endpoints)
- Validators
- Translations
- Routes
- Entry point

## Creating a New Module

1. Create a folder under `Modules/`, e.g., `Modules/Invoice`
2. Create an entry file named after the module: `Invoice.php`

The entry file should:

- Define a class named `Invoice`
- Use the namespace `Modules\Invoice`
- Implement the `ModuleInterface`
- Preferably extend `AbstractModule` for default wiring

### Example

```php
namespace Modules\Invoice;

use BitCore\Application\Services\Modules\AbstractModule;

class Invoice extends AbstractModule
{
    protected $autoloadRegister = true;
    protected $autoloadBoot = true;
}
```

A module requires:

1. A folder for the module.
2. An entry file inside the folder.
3. Models
4. Repositories(the modules repositories and it's interface)
5. Actions (Endpoints)
6. Config(in this folder you'll have route and register files)
7. Database(Contains database schemas)
8. lang(this for language translations)
9. Requests(the contains the request validator files)
10. Services
11. Tests

### Example

To create an `Invoice` module:

1. Create a folder named `Invoice`.
2. Inside the `Invoice` folder, create an entry file named `Invoic.php`.

The entry file should:

- Contain a class named `Invoice`.
- Be namespaced as `Modules\Invoice`.
- Implement the `BitCore\Application\Services\Modules\ModuleInterface`.
- We have provide an abstract class `BitCore\Application\Services\Modules\AbstractModule` which is implementation of `BitCore\Application\Services\Modules\ModuleInterface`. We strongly advice you use the absctract class intead as it get many things done for you by default whithout denying you ability to customize.

### Key Method: `register` and `boot`

The `register` method in the `ModuleInterface` is used to:

- Hook into the application's lifecycle.
- Perform tasks like:
  - Registering services.
  - Defining routes, models, controllers, and translations.
  - Listening to events.

You can provide Config/register.php and set autoloadRegister to true i.e

```php
protected $autoloadBoot = true
```

the file should return a callable function that receive `$container` as parameter.

The `boot` method in the `ModuleInterface` is used to:

- Hook into the application's lifecycle.
- Perform tasks like:
  - Loading translation
  - State checking or performing activities that requires other dependencies i.e booting up the module.

You can also provide Config/boot.php and set autoloadBoot to true i.e

```php
protected $autoloadBoot = true
```

the file should return a callable function that receive `$container` as parameter.

For better understanding, refer to the sample module provided.

### Routing:

You can provide Config/route.php and set autoloadRoute to true i.e

```php
protected $autoloadRoute = true; // This is true by default
```

the file should return a callable function that receive `$app` as parameter.

Set it to false if manually loading route.

For better understanding, refer to the sample module provided or the core modules.

---

## Testing Modules

Every module must have **full test coverage**.  
Refer to the `tests/` folder in the sample module for guidance.

## Storage

The storage functionality is powered by **Illuminate\Filesystem** and **League\Flysystem**. This setup provides seamless access to both **local** and **remote** storage systems, such as **Amazon S3**, without needing to worry about the underlying implementation details.

#### Supported Storage Disks

- **Local Disk**: This stores files in the local file system of your application.
- **Amazon S3**: This provides cloud storage using AWS's S3 service, allowing you to store and retrieve files remotely.

#### Configuring Storage Disks

Before using the storage functionality, ensure that your disk configurations are properly set up in the `config/filesystems.php` file.

```php
// config/common.php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
    ],
];
```

In this example:

- **Local disk** will store files within the application's local `storage/app` directory.
- **S3 disk** will connect to AWS S3 using your credentials and configuration from `.env`.

#### Using the `storage` Helper

The `storage()` helper function simplifies the usage of different disks. You can pass the disk name (such as `local` or `s3`) and perform common file operations.

##### Writing Files

To write a file, simply use the `put` method.

```php
// Write a file to S3
storage('s3')->put('example.txt', 'This is some content for the file.');

// Write a file to the local disk
storage('local')->put('example.txt', 'This is some local content.');
```

##### Reading Files

To read the contents of a file, use the `get` method.

```php
// Read a file from S3
$content = storage('s3')->get('example.txt');
echo $content;

// Read a file from the local disk
$content = storage('local')->get('example.txt');
echo $content;
```

##### Checking if a File Exists

To check if a file exists, you can use the `exists` method.

```php
// Check if the file exists on S3
if (storage('s3')->exists('example.txt')) {
    echo "File exists on S3!";
}

// Check if the file exists locally
if (storage('local')->exists('example.txt')) {
    echo "File exists locally!";
}
```

##### Deleting Files

To delete a file, use the `delete` method.

```php
// Delete a file from S3
storage('s3')->delete('example.txt');

// Delete a file from the local disk
storage('local')->delete('example.txt');
```

#### Using Multiple Storage Disks

You can easily switch between multiple storage disks (e.g., from `local` to `s3`), and the `storage()` helper makes it simple to interact with different disks interchangeably.

```php
// Example of switching between disks:
$filename = 'example.txt';
$content = 'This file is first stored locally, then uploaded to S3.';

// Store locally first
storage('local')->put($filename, $content);

// Retrieve from the local disk and upload to S3
if (storage('local')->exists($filename)) {
    $content = storage('local')->get($filename);
    storage('s3')->put($filename, $content);
}
```

#### Advanced Storage Operations

##### File Permissions

You can also handle file permissions when storing files. For example, setting a file to be publicly accessible on S3:

```php
// Upload a file to S3 and make it publicly accessible
storage('s3')->put('public_image.jpg', $imageContent, 'public');
```

##### File Upload

If you're working with uploaded files (e.g., from a form), you can use the `upload` function for handling files in a clean and secure manner or use the`store` method on the uploaded files. Below are two different approaches you can use for file uploading

---

###### Option 1: **Request Validation Class (Recommended)**

When using a request validation class, files are validated automatically. You can then store them easily.

```php
// Get the validated data from the request
$data = CreateUserRequestWithFile::validated();

/** @var \BitCore\Application\Services\Requests\UploadedFile */
$file = $data['file'];

// Store the file in the 'somedir' directory, using the 'local' disk
$filePath = $file->store('somedir', 'local');

// For multiple files
$uploadeds = [];
/** @var \BitCore\Application\Services\Requests\UploadedFile[] */
$files = $data['files'];
foreach ($files as $file) {
    $uploadeds[] = $file->store('somedir', 'local'); // Save each file
}

// Additional validation can still be performed (e.g., file size, extension)
// by accessing the PSR-7 instance, such as:
// $file->getSize(), $file->guessExtension(), etc.
```

**Advantages of Option 1:**

- Automatically handles validation and file sanitization.
- Clean, readable code that reduces the need for manual validation.
- Can be extended with further validation or custom checks like size and extension.

---

###### Option 2: **Without Request Validation Class**

If you choose not to use a request validation class, you can manually handle file validation and saving.

```php
// Get the uploaded files from the input class
$uploadedFiles = $this->input->files();

/** @var \Psr\Http\Message\UploadedFileInterface $file */
$file = $uploadedFiles['file'];

// Perform manual validation, such as checking file size, mime type, etc.
$filePath = $file->store('somedir', 'disk');

// For multiple files
$uploadeds = [];
foreach ($uploadedFiles['files'] as $file) {
    // Save each uploaded file
    $uploadeds[] = $file->store('somedir', 'local');
}
```

**Key Points for Option 2:**

- You have full control over the validation process (e.g., checking file size, mime type).
- You manually handle each file’s validation and storage.
- Suitable if you don’t want to use the Request Validation class for file uploads.

---

###### One-liner (Not Recommended)

You can upload a file in a single line, but it is **not recommended** unless you’ve already validated the input via the request class. This method doesn’t provide sufficient flexibility for error handling or additional validation.

```php
$this->input->files('file')->store('somedir', 'disk');
```

**Why It’s Not Recommended:**

- This approach bypasses custom validation or error handling.
- It can be less clear and harder to maintain.
- It doesn't allow for file-specific checks like size, extension, etc.

---

**Key Notes:**

- **Sanitization**: Avoid using `$request->getUploadedFiles()` directly, as it may not be sanitized for secure file storage. Instead, always prefer using the request validation class or sanitizing files yourself.
- **Multiple Files**: Both options support uploading multiple files, either through a loop or directly.
- **Further Validation**: Even when using the request validation class, you can still perform additional checks on the file’s properties (e.g., size, MIME type) before saving them.
- ** Manual upload**: You can directly use `upload()` method also. However, this require direct PSR7 upload instance. WE DO NOT Recommend this path.

#### Benefits of Using the `storage` Helper

- **Abstraction**: You do not need to worry about the specific implementation of each storage driver. Whether it’s local or S3, the code remains consistent.
- **Centralized Configuration**: All storage-related configurations are stored in `config/filesystems.php`, making it easier to manage different storage disks and their respective configurations.
- **Seamless Integration**: Since **Illuminate\Filesystem** integrates directly with **Flysystem**, it provides a unified API for working with different storage backends, which means you can switch from local storage to cloud storage without changing your codebase.

#### Custom Storage Drivers

If you need to integrate a custom storage driver (e.g., Google Cloud, FTP, etc.), you can easily extend the `FilesystemManager` to include your custom driver.

For example, if you wanted to create a custom `GoogleCloudStorage` disk, you would add a new disk configuration and implement the driver.

```php
// Add custom disk configuration to `config/filesystems.php`
'google_cloud' => [
    'driver' => 'google_cloud',
    'project_id' => 'your-project-id',
    'bucket' => 'your-bucket-name',
    // other Google Cloud storage configurations...
],
```

---

### Example of a Complete Setup

Here’s an example that showcases both S3 and local storage usage within your application:

```php
// Write to a local file
storage('local')->put('local_file.txt', 'This is a local file.');

// Write to an S3 file
storage('s3')->put('s3_file.txt', 'This file is uploaded to S3.');

// Retrieve a file from S3
$s3FileContent = storage('s3')->get('s3_file.txt');
echo $s3FileContent; // This file is uploaded to S3.

// Delete the file from local disk
storage('local')->delete('local_file.txt');

// Delete the file from S3
storage('s3')->delete('s3_file.txt');

upload('upload',$this->input->files()['fileName']);

upload('upload',$this->input->files()['fileName'], 's3');
```

By following the above principles, you can easily manage your file storage operations across multiple storage providers, ensuring seamless flexibility in your modules’s architecture and reliable future compatibility with the core app.

---

## Database

The database functionality is provided by the `Illuminate\Database` package, which powers Laravel's Eloquent ORM. This integration allows for seamless database migrations, models, and query building while ensuring scalability and modularity for your application.

### Migration

To ensure module migrations are properly run and organized, follow these guidelines:

1. **Migration Directory**  
   Create a directory named `Database/Migrations` inside your module folder. This is where you will store all migration files for that module.

2. **Naming Convention**  
   Migration file names must be **unique** across the entire application. To achieve this, prefix your migration file names with your module name.  
   For example, if your module is named `invoice`, migration files should be prefixed as follows:

   - `module_invoice_create_invoices_table.php`
   - `module_invoice_add_due_date_to_invoices_table.php`

   For better tracking, it is recommended to include a timestamp in the filename, such as:

   - `module_invoice_2025_01_17_000001_create_invoices_table.php`.

3. **Using Migrations**  
   Each migration file should define the `up` method to apply changes (e.g., create tables, add columns) and the `down` method to rollback changes.

   Example Migration File:

   ```php
   <?php

    use BitCore\Foundation\Database\Manager as Capsule;
    use BitCore\Foundation\Database\Migration;

    return new class() extends Migration
    {
        public function up()
        {
            if (!Capsule::schema()->hasTable('customers')) {
                Capsule::schema()->create('customers', function (mixed $table) {
                    /** @var BitCore\Foundation\Database\Blueprint $table */
                    $table->increments('id');
                    $table->string('company')->unique();
                    $table->timestamps();
                    $table->softDeletes();
                });

                // Include seedings too here if neccessary or conditionaly check needs for seeding.
            }
        }

        public function down()
        {
            Capsule::schema()->dropIfExists('customers');
        }
    };
   ```

4. **Running Migrations**  
   Use the CLI tool (`artisan`) to perform migrations. The system ensures all migrations, including those in modules, are run. The migrations will be sorted by timestamps to maintain the order of execution.

### Model

To define your database models, create a PHP class file inside your module's `Database/Models` folder and extend the `BitCore\Foundation\Database\Model` class.

#### Why Extend `BitCore\Foundation\Database\Model`?

This ensures all models remain consistent with the application's architecture, allowing you to replace the ORM in the future if necessary. Avoid directly using `\Illuminate` namespaces in your modules for the sake of compatibility and flexibility.

#### Example Model:

```php
<?php

namespace Modules\Invoice\Database\Models;

use BitCore\Foundation\Database\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'amount',
        'due_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Additional Features:

- Leverage all Eloquent functionality, such as relationships (`hasOne`, `hasMany`, `belongsTo`, etc.), scopes, and attribute casting, while ensuring compatibility by relying on the foundation model.

### Database Queries

To execute database queries directly, use the `db()` helper function, which provides access to the default database connection.

#### Examples:

- **Raw Queries**

  ```php
  db()->query('SELECT * FROM invoices WHERE status = ?', ['paid']);
  ```

- **Query Builder**  
  The `db()` function returns an instance of the query builder for the default connection.  
  Example:

  ```php
  $invoices = db()->table('invoices')
                  ->where('status', 'unpaid')
                  ->orderBy('due_date', 'asc')
                  ->get();
  ```

- **Transactions**  
  Use transactions to ensure atomic operations:

  ```php
  db()->transaction(function () {
      db()->table('invoices')->insert([
          'invoice_number' => 'INV12345',
          'amount' => 500.00,
          'status' => 'unpaid',
      ]);

      db()->table('logs')->insert([
          'action' => 'Created invoice INV12345',
          'timestamp' => now(),
      ]);
  });
  ```

### Seeder

Use conditional seeding in migration file. See the migration section

### Best Practices

1. **Unique Table Names**: Use unique table names per module to avoid conflicts. Alway prefix your table with your module name.
2. **Avoid Hardcoding**: Use environment variables and the `config()` function to retrieve database configurations.
3. **Namespace Organization**: Always use proper namespaces for your models, migrations, and seeders to ensure modularity.
4. **Rollback Support**: Always define a `down` method in your migrations to support rollbacks.
5. **Migration Order**: Use timestamps in migration filenames to control the execution order.

## Translation and Internationalization

Strive for 100% internationalization in your module to ensure it is accessible to a global audience. This means all user-facing text should be translatable.

### Translation Loading

Translation loading is automated for modules that extend the `BitCore\Application\Services\Modules\AbstractModule` class. If your module's entry file inherits from this abstract class, the system will automatically locate and load the translation files.

However, if you are not extending the `AbstractModule` class, you must manually implement the `loadLanguage()` method in your module's entry file. This method should register the translation paths with the application's translator, ensuring that your module's language files are accessible.

### Translation File Structure

Translation files should be stored in the `lang` directory within your module. For example:

```
modules/Invoice/lang/en/messages.php
```

### Naming Convention

Each module is namespaced using its module ID. The module ID is derived from the entry file's base name. For instance, if your module is named `Invoice`, then the namespace for its translations will also be `Invoice`.

### Using the Translator

To retrieve translations within your module, use the `trans` helper function. This function accepts a translation key and optional replacement parameters. The translation key should follow this format:

```php
trans('ModuleID::file.key', ['key1' => 'value1', 'key2' => 'value2'])
```

**Example:**
If your module ID is `Invoice` and you have a translation file `messages.php` containing:

```php
return [
    'healthy' => 'The system is running smoothly.',
    'due_invoice' => 'You have :count invoices due for payment.',
];
```

You can retrieve translations like so:

```php
echo trans('Invoice::messages.healthy');
// Output: The system is running smoothly.

echo trans('Invoice::messages.due_invoice', ['count' => 5]);
// Output: You have 5 invoices due for payment.
```

## Settings

### Adding Settings (persistent to storage i.e DB)

To create custom settings for your module, use the following methods:

1. **Saving a single setting:**

   ```php
   settings()->save('mymodule.setting_one', 'some value here i.e any scalar');
   ```

   This saves a simple key-value pair.

2. **Saving an array setting:**

   ```php
   settings()->save('mymodule.setting_two', ['array' => 'value']);
   ```

   This saves an associative array as the setting value.

3. **Saving an object setting:**

   ```php
   settings()->save('mymodule.setting_three', (object) ['array' => 'value']);
   ```

   This saves an object as the setting value. The object will be automatically encoded to JSON when stored.

4. **Save multiple configuration values and persist them to the database:**
   ```php
   settings()->saveMany([
       'mymodule.setting_eight' => 'persistent value one',
       'mymodule.setting_nine' => 'persistent value two',
       'mymodule.setting_array'  => [1,2,3],
       'mymodule.setting_array'  => (object)['key' => 1, 'key2' => 'somethingHere'],
   ]);
   ```
   This method saves multiple key-value pairs and persists them to the database.

### Setting Configuration Values Without Persisting

1. **Set a single configuration key-value pair (does not persist):**

   ```php
   settings()->set('mymodule.setting_four', 'another value');
   ```

   This sets a configuration value in memory but does not persist it to the database.

2. **Set multiple configuration values (does not persist):**
   ```php
   settings()->saveMany([
       'mymodule.setting_five' => 'value one',
       'mymodule.setting_six' => 'value two',
   ]);
   ```
   This sets multiple configuration values at once in memory but does not persist them to the database.

### Retrieving Settings

To retrieve the value of a specific setting:

1. **Getting a simple setting:**

   ```php
   $value = settings()->get('mymodule.setting_one'); // return string by default and array|object for json
   ```

2. **Getting an array setting:**

   ```php
   $value = settings()->get('mymodule.setting_two'); // returns an array
   ```

3. **Getting an object setting:**

   ```php
   $value = settings()->get('mymodule.setting_three'); // returns an object
   ```

4. **Retrieving all settings:**

   ```php
   $allSettings = settings()->getAll();
   ```

   This retrieves all settings, including core settings and those from other modules. Use this with caution as it could return a large dataset.

5. **Retrieving as boolean value for logics:**
   ```php
   $settings = settings()->getAsBool('mymodule.notification_enabled'); // returns boolean
   ```
   This retrieves the settings as truthy. Useful for settings like enabled e.t.c i.e true for yes, true, 1 and false otherwise

### Notes and Recommendations

- The `set` and `setMultiple` methods only update the in-memory configuration. This can be helpful for non persistent replacement of a settings in the app.
- To persist the changes to the database, use `save` or `saveMany`.
- The settings class automatically handles the serialization of complex values like arrays and objects (stored as JSON).
- Be mindful when using `getAll()`, as it will return all settings, not just those specific to your module.
- Always prefix settings in your modules with your module name to avoid conflict with other modules settings i.e mymodule.somesttings
- When dealing with multiple settings, always use the \*Multiple version i.e `saveMany` to reduce numbers of call to persistent engine i.e database.

## Events and Hooks Guide

This guide demonstrates how to use hooks and events effectively within your application. Developers are encouraged to follow best practices, including leveraging class-based events for maintainability and clarity. Examples of both class-based events and simple hook names are provided.

### Class-Based Events Example

Using class-based events is a preferred approach for structured and reusable event handling.

#### Example: SettingsLoaded Event

```php

namespace BitCore\Application\Events;

use BitCore\Application\Services\Settings\Settings;

class SettingsLoaded
{
    public Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
}
```

### Dispatching a Class-Based Event

```php
hooks()->dispatch(SettingsLoaded::class, new Settings([]));
```

### Listening to a Class-Based Event

```php
hooks()->listen(SettingsLoaded::class, function (Settings $settings) {
    $settings->set('app.hook', 'filtered');
});
```

## Hook Name with Array Payload Example

When using hooks with array payloads, ensure the payload is passed by reference if you intend to allow modification by listeners (i.e filter event).

### Example: After Load Settings Hook

```php
$settings = [];

hooks()->listen('after_load_settings', function (&$settings) {
    $settings['app.hook'] = 'filtered';
});

hooks()->dispatch('after_load_settings', [&$settings]);
```

## Queue and Cron

You can get the queue manager using the `queue()` helper.

```php
use Modules\Queue\Enums\JobPriority;
use Modules\Queue\Services\QueueManager;

// Assuming $queueManager is already set up:
queue()->push(
    Modules\YourModule\SendEmailJob::class,
    [
        'to' => 'user@example.com',
        'subject' => 'Welcome!',
        'body' => 'Thank you for signing up!'
    ],
    JobPriority::HIGH // Optional and default to JobPriority::MEDIUM
);
```

### Recommendations for Module Developers

#### 1. **Use Class-Based Events**

- Class-based events provide better structure and reusability. They allow developers to encapsulate event-related data and logic, making debugging and maintenance more manageable.
- Always dispatch events at critical points in your module where other components might need to extend or alter behavior.

#### 2. **Dispatch Events at Necessary Points**

Identify key points in your module where events should be dispatched. Common areas include:

- Configuration loading (`after_load_settings`).
- User actions (e.g., user login/logout).
- Data processing (e.g., after saving or deleting a record).

#### 3. **Provide Clear and Descriptive Hook Names**

For simple hooks, ensure the names are descriptive and follow a clear naming convention (e.g., `module_name.action_name`).

#### 4. **Document Hook Usage**

For every hook or event you add:

- Document its purpose and expected payload.
- Provide examples for developers to integrate their listeners effectively.

#### 5. **Use Listener Priorities Thoughtfully**

Use priorities to control the execution order of listeners when multiple listeners are registered for the same hook.

##### Example: Listener Priorities

```php
hooks()->listen('after_load_settings', function (&$settings) {
    $settings['priority'] = 'high';
}, 5); // High priority

hooks()->listen('after_load_settings', function (&$settings) {
    $settings['priority'] = 'low';
}, 15); // Low priority
```

### Additional Best Practices

1. **Avoid Overlapping Functionality**

   - Ensure each listener handles a distinct task. Avoid duplicating logic across multiple listeners for the same event.

2. **Test All Listeners Thoroughly**

   - Validate that listeners modify the payload or execute as intended in isolation and when combined.

3. **Manage Listener Registration**

   - Always unregister listeners during cleanup to avoid unexpected behavior in subsequent tests or application runs.

4. **Use Dependency Injection Where Possible**
   - For class-based listeners, inject dependencies through the constructor for better testability.

By following this guide, you can implement robust, flexible, and maintainable event-driven functionality within the modules.

### Available hooks.

See core/Application/Events

We will provide an endpoint in settings to show availabe events accross the core and modules. Using docs on the event class when possible.

## Logging

Use the `logger()` helper function, which returns an instance of Monolog, to log messages at various levels. Logging helps in monitoring application behavior, debugging, and error tracking.

### Log Levels and Examples

Here are examples of how to log messages at different levels:

#### **DEBUG**: For detailed debugging information.

```php
logger()->debug('Debugging application flow.', ['context' => 'details here']);
```

#### **INFO**: For informational messages.

```php
logger()->info('User successfully logged in.', ['user_id' => 123]);
```

#### **NOTICE**: For normal but noteworthy events.

```php
logger()->notice('Disk space running low.', ['free_space' => '500MB']);
```

#### **WARNING**: For exceptional situations that are not errors.

```php
logger()->warning('API response time is high.', ['response_time' => '2.5s']);
```

#### **ERROR**: For runtime errors.

```php
logger()->error('Unable to connect to database.', ['error' => $exception->getMessage()]);
```

#### **CRITICAL**: For critical conditions that require immediate attention.

```php
logger()->critical('Application is down.', ['service' => 'backend']);
```

#### **ALERT**: For situations requiring immediate action.

```php
logger()->alert('Payment gateway is down!', ['gateway' => 'PayPal']);
```

#### **EMERGENCY**: For system-wide emergencies.

```php
logger()->emergency('System is unavailable!', ['host' => 'web-server-1']);
```

### **Adding Context and Extra Information**

You can pass additional context or extra data to the log message:

```php
logger()->info('Processing order.', [
    'order_id' => 9876,
    'user_id' => 123
]);
```

### **Example: Using Logging in an Event Listener**

```php
hooks()->listen('user.created', function (User $user) {
    logger()->info("User logged in successfully.", [
        'user_email' => $user->email,
        'login_time' => now()
    ]);
});
```

By combining `hooks()` for event management and `logger()` for structured logging, you can build a robust and maintainable event-driven architecture in your application.

## Notification System

### Sending Notifications

To send a notification via any channel, simply use the `send_notification` helper.

This helper accepts the following parameters:

1. **Notification Instance** – An instance of a notification class that implements the `BitCore\Application\Services\Notifications\Notification` interface.
2. **Recipients** – An array of recipient instances, where each recipient is an instance of `BitCore\Application\Services\Notifications\Recipient`.
3. **Template Data** – An associative array of placeholder values for the notification template.
4. **Channels (Optional)** – An array specifying the channels through which the notification should be sent.
   - If no channels are specified, the system will attempt to notify the user through all enabled admin/user channels.
   - **Best Practice:** We recommend not specifying channels unless you need to enforce a specific channel for a particular notification.

#### Example Usage:

```php
send_notification(
    new SomeNotification(), // Notification instance
    [new Recipient('user@example.com')], // List of recipients
    ['name' => 'John Doe'], // Template data
    [DashboardChannel::$channelName] // (Optional) Specific channels
);
```

---

### Extending Notifications System

The core notification system already registers built-in channels like **Email, SMS, Push, Webhook, and Dashboard**, but you can introduce new ones dynamically **without modifying core files**.

#### Extending Notification Channels via Hooks

A **notification channel** is responsible for delivering messages via a specific medium (e.g.,Email, Slack, WhatsApp, Telegram). You can register your own **custom channel** by listening to the `NotificationChannelsLoaded` event, and push to `$event->channels`

##### Register a Custom Channel

To add a new notification channel, listen for the event and modify the `$channels` array.

###### Example: Adding a Slack Channel

```php
use BitCore\Application\Events\NotificationChannelsLoaded;
use Modules\YourModules\Services\Notification\Channels\SlackChannel;

hooks()->listen(NotificationChannelsLoaded::class, function ($event) {
    $channel = new SlackChannel();

    // Make your provider and register if aavailable
    //$channel->registerProvider(new DefaultSlackChannelProvider());

    $event->channels[] = $channel;
});
```

The `SlackChannel` should extend/implement `\BitCore\Application\Services\Notifications\Channels\NotificationChannel`. Most likely you only want to define the channels name. i.e

```php
declare(strict_types=1);

namespace YourModule\Notifications\Channels;

use BitCore\Application\Services\Notifications\Channels\NotificationChannel;
/**
 * Custom channel to handle notification via slack
 */
class SlackChannel extends NotificationChannel
{
    /**
     * {@inheritDoc}
     */
    public static $channelName = 'slack';
}
```

## Adding a Custom Transport Provider via Hooks

Some channels (e.g., Email, SMS) **support multiple transport providers** (e.g., SMTP, Twilio, SendGrid). You can register a **custom transport provider** for an existing channel without modifying core code.

If you channel only have one means, you still need to implement and provide a transporter.

Your provider class need to implement `BitCore\Application\Services\Notifications\Providers\NotificationProviderInterface` or simply extend `BitCore\Application\Services\Notifications\Providers\NotificationProviderAbstract`.

Then create new instance of the provider class and register it to the channel.

### Register a Custom SMS Provider

To register a **Twilio SMS provider** for the SMS channel:

```php
use BitCore\Application\Events\NotificationChannelsLoaded;
use BitCore\Application\Services\Notifications\Channels\SmsChannel;
use Modules\YourModules\Services\Notifications\Providers\TwilioSmsProvider;

hooks()->listen(NotificationChannelsLoaded::class, function ($event) {
    $event->channels[SmsChannel::class][] = TwilioSmsProvider::class;
});
```

Using above way will ensure the provider is automatically provided with the necessary settings.

Alternatively, for manual control, you can register the channel in App created event or at any point by using the notification manager.

```php
use BitCore\Application\Events\NotificationChannelsLoaded;
use BitCore\Application\Services\Notifications\Channels\SmsChannel;
use Modules\YourModules\Services\Notifications\Providers\TwilioSmsProvider;

$notificationManager = app()->getContainer(NotificationManager::class);
$channel = $notificationManager->getRegisteredChannel(SmsChannel::getChannelName());
if(!$channel) {
    $settings = (array)settings()->get('notification.channels');
    $channel = new SmsChannel();
    $channel->setSettings($settings[$channel->getChannelName()]);
}

$channel->registerProvider(new TwilioSmsProvider(), true);

// Or
//$provider = new TwilioSmsProvider();
//$provider->setSettings(['yoursettings'=>'value']); // Set custom settings on provider.
// The settings can be from your module db or from general settings also. We encourage using the first approach.
//$channel->registerProvider($provider, false);

$notificationManager->registerChannel($channel);

```

### Register channels or providers settings

To manage your provider or channels settings through the app setup notification settings, simply listen to this event and provide settings for your notification channels or providers.

```php
hooks()->listen(NotificationChannelsSettingsPrepared::class, function ($event) {

    $myChannelName = SmsChannel::getChannelName();

    if(!isset($event->channels[$myChannelName])){
        $event->channels[$myChannelName] = [];
    }

    $event->channels[$myChannelName]['enabled'] => 'bool';

    // Provider settings
    $myProviderName = SmsChannel::getProviderName();
    if(!isset($event->channels[$myChannelName][$myProviderName])){
        $event->channels[$myChannelName][$myProviderName] = [];
    }
    $event->channels[$myChannelName][$myProviderName]['enabled'] => 'bool';
    $event->channels[$myChannelName][$myProviderName]['client_key'] => 'text';
    $event->channels[$myChannelName][$myProviderName]['client_secret'] => 'password';
    $event->channels[$myChannelName][$myProviderName]['client_secret'] => new SettingsInput();
});
```

---

## **Handling Dynamic Channels and Providers**

By using `NotificationChannelsLoaded`, module developers can:

- **Add new channels** (e.g., Telegram, Discord, WhatsApp).
- **Extend existing channels** with custom providers.
- **Modify registered channels before they are used.**

Since the event is dispatched **before channels are registered**, all modifications take effect dynamically.

---

## **Final Notes for Module Developers**

✅ **Never modify core files**—always extend via hooks.  
✅ **Register new channels** by modifying `$event->channels`.  
✅ **Extend existing channels** by adding transport providers.  
✅ **Ensure your channels implement `ChannelInterface`** for compatibility.

By following this approach, your module can **seamlessly integrate** into the notification system while remaining **fully upgrade-safe**. 🚀

# Guide on How to Use BitCore as Dependency in a Project

🧩 Create your Modular PHP Starter with ankabit/bitcore.

Here is a full `Step by Step guide`, describing how to use `ankabit/bitcore` as a starter kit in any PHP project (modular and Composer-based).

This guide shows how to create a **modular PHP application** using [`ankabit/bitcore`](https://github.com/ankabit/bitcore) as a base framework. It uses Composer for dependency management and supports clean modular development.

## 🚀 Getting Started

### ✅ Step 1: Create a new Composer project

```bash
mkdir my-app && cd my-app
composer init
```

Fill in prompts as needed (e.g., `vendor/custom-php-app`), and accept defaults.

### ✅ Step 2: Add ankabit/bitcore as a dependency

Since `ankabit/bitcore` is hosted on GitHub (not Packagist), add it manually in your `composer.json`:

```json
 "require": {
        "ankabit/bitcore": "dev-main"
 },
 "repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/ankabit/bitcore"
    }
 ]
```

Then run:

```bash
composer require ankabit/bitcore:dev-main
```

> 💡 Since it is a private repo you may need to setup your github token .

Run the command bellow to set up your GitHub token:

```bash
composer config --global github-oauth.github.com <your-token>
```

### ✅ Step 3: Setup & Folder Structure

Your project should follow a clean and modular structure like below:

> 💡 This structure is **not mandatory**, but it provides a solid foundation for organizing a modular PHP application.

```

my-app/
├── composer.json
├── .env
├── public/
│   └── index.php
├── src/
│   ├── bootstrap.php
│   └── modules/
│   └── config/

```

#### 📁 Create folders

Run the following commands from the root of your project to set up the folder structure:

```bash
mkdir -p public
mkdir -p src/modules
mkdir -p src/config
```

#### 📄 Create `public/index.php`

Create the main entry point of your application:

```bash
touch public/index.php
```

Then add the following code:

```php
<?php
declare(strict_types=1);

// Autoload and bootstrap
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

```

---

#### 📄 Create `src/bootstrap.php`

```bash
touch src/bootstrap.php
```

Add this boilerplate to define the app paths:

```php
<?php
declare(strict_types=1);

// Tell bitcore about your base directory for the project
defined('APP_BASE_PATH') or define('APP_BASE_PATH', realpath(__DIR__ . '/../') . '/');

// Tell bitcore your config folder for autoloading of default config overrides.
defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_BASE_PATH . 'src/config/');

// Tell bitcore where you plan to store your modules and the namespace for module loading.
//We are using Modules\ namespace and will be added to the composer.json psr4
defined('APP_MODULES_PATH') or define('APP_MODULES_PATH', APP_BASE_PATH . 'src/modules/');
defined('APP_MODULES_BASE_NAMESPACE') or define('APP_MODULES_BASE_NAMESPACE', 'Modules\\');

// Optionally include index from bitcore to handle request (PSR7) out of the box
require __DIR__ . '/../vendor/ankabit/bitcore/public/index.php';

```

---

#### 📄 Create `.env` file

```bash
touch .env
```

Add your application and database environment variables:

```dotenv
# Application Settings
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_DEFAULT_LANG=en

# CSRF
APP_CSRF_ENABLED=true

# CSRF route path
APP_CSRF_ROUTE_PATH=

# Comma separated csrf excluded paths
APP_CSRF_EXCLUDED_PATHS=/api/csrf-cookie

# Logger
LOGGER_NAME=erp-app
LOGGER_LOG_ERROR=true
LOGGER_LOG_ERROR_DETAILS=true
LOGGER_LEVEL=Debug #Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency


# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_api
DB_USERNAME=root
DB_PASSWORD=
DB_SOCKET=
DB_PREFIX=erp_

# Filesystem Configuration
FILESYSTEM_DISK=local
FILESYSTEM_CLOUD=s3
PUBLIC_DISK_ROOT = 'app/public'
LOCAL_DISK_ROOT = 'app'

# AWS S3 Configuration
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-west-2
AWS_BUCKET=
AWS_URL=
AWS_ENDPOINT=
AWS_S3_ROOT=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Optional: PDO SSL
MYSQL_ATTR_SSL_CA=

# Optional: Docker
DOCKER=false
```

> 🛠 BitCore uses `vlucas/phpdotenv` to load environment variables. Make sure this file is in your **project root** (same level as `composer.json`).

---

### ✅ Step 4: Configure autoloading in `composer.json`

```json
"autoload": {
  "psr-4": {
    "Modules\\": "src/modules/"
  }
}
```

Then run:

```bash
composer dump-autoload
```

---

### ✅ Step 5: Start the server

Use PHP’s built-in server:

```bash
php -S localhost:8000 -t public
```

Or define a custom Composer script:

```json
"scripts": {
  "start": "php -S localhost:8000 -t public"
}
```

Then run:

```bash
composer start
```

---

## 🧱 Creating a Sample Module

Please Refer to the Modules creation guide to create module for your project

---

## ✅ Summary

| Step | Description                                                                                                                               |
| ---- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| 1    | Create Composer project                                                                                                                   |
| 2    | Add GitHub repo and require `ankabit/bitcore`                                                                                             |
| 3    | Setup modular folder structure, Define bootstrap constants, Create `public/index.php` to boot BitCore , Add `.env` for environment config |
| 4    | Configure PSR-4 autoload                                                                                                                  |
| 5    | Start dev server with `php -S` or `composer start`                                                                                        |

---
