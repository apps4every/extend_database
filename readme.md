# Apps4Every Extend Database

This is a package that extend the definition of a table by request.

## How to include in your project:

1. Run `composer require apps4every/extend_database`

2. Run `php artisan vendor:publish --provider="Apps4every\ExtendDatabase\ExtendDatabaseServiceProvider` to publish the Assets and Config

## How to use:

1. Migration tables:

In your migration table file, add:
`require_once config('apps4every_extend_database.pathStatisticalColumns'); `

Inside your class definition (`return new class extends Migration`):

`use StatisticalColumns;` as your first line of code.

2. Models:
`use App\Models\Includes\DataBaseInformation;`

Inside your class definition (`class XXX extends Model`):

As your first lines of code, include these:

`protected $fillable = [];`<br>
`protected $translatable = [];`<br>

`const TABLE         = "TABLE_NAME";`<br>
`const IMAGES_PATH   = "TABLE_NAME";`<br>
`use DataBaseInformation;`<br>

## Functionality included:

1. Migration tables:

- addStatisticalColumns

- addTableComment

- addAccessContentColumns

- addMaintenanceContentColumns

- addRestrictContentColumns

- addCommentsRestrictContentColumns

- addIndexContentColumns

- addPublishContentColumns

- addExpirationContentColumns

- addAdsContentColumns

- addSeoColumns

2. Models:

TO DO

## Security Vulnerabilities

If you discover a security vulnerability within any Apps4Every package, please send an e-mail to Apps4Every info mailbox via [info@apps4every.com](mailto:info@apps4every.com). All security vulnerabilities will be promptly addressed.

## License

The Apps4Every packages are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## More from our Apps4Every Team

- Visit our web [Apps4Every](https://www.apps4every.com)
- Visit our youtube channel [Apps4Every](https://www.youtube.com/@apps4every256)

## TO DO
Automatically generate basic Laravel validation rules based on your database table schema!

Example: https://github.com/laracraft-tech/laravel-schema-rules