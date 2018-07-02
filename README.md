# Recipe Website

Website for posting and finding vegan food recipes.

- [Recipe Website](#recipe-website)
  - [Description](#description)
      - [External libraries/tools used](#external-librariestools-used)
  - [Installation](#installation)
      - [Mongo database collection](#mongo-database-collection)
  - [Contributing](#contributing)
  - [License](#license)

## Description

This is a small project with the main goal of improving my web development skills.

Written in PHP / HTML5 / CSS3 / JavaScript, using a Mongo database.

#### External libraries/tools used

- [Normalize CSS](https://necolas.github.io/normalize.css/)
- [Google Fonts](https://fonts.google.com/)
- [FontAwesome CSS](https://fontawesome.com/)
  - for icons and placeholder images
- [Tags Input Beautifier](https://github.com/tovic/tags-input-beautifier)
  - adapted code for recipe tag input

## Installation

1. Install Mongo database on web server
   - Create text index for search functionality:
```
db.recipes.createIndex({name:"text", description:"text", "ingredients.item":"text", tags:"text"})
```
1. Install Composer PHP package manager in project folder and install `mongodb` package:
```
php composer.phar require mongodb/mongodb
```
3. Copy `config.php` file to root directory (example below)

```php
<?php

// Example config.php file

$config = array(
  "db" => array(
    "server" => "127.0.0.1",
    "port" => "27017",
    "name" => "mydb",
    "collection" => "recipes",
    "username" => "user",
    "password" => "pass1234"
  ),
  "urls" => array(
    "baseUrl" => "https://mydomain.com/recipes"
  ),
  "max_upload_size_MB" => 8
);

// Constants declared for frequently used path names
define("IMAGES_PATH", realpath(dirname(__FILE__)) . '/public_html/images');
define("TEMPLATES_PATH", realpath(dirname(__FILE__)) . '/resources/templates');
define("LIBRARY_PATH", realpath(dirname(__FILE__)) . '/resources/library');
 ```

#### Mongo database collection

Collection name = `recipe`

| Field name    | Description                                                                                                                                                                                        | Type       |
| ------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- |
| `_id`         | Automatically generated                                                                                                                                                                            | integer    |
| `name`        | Name of recipe                                                                                                                                                                                     | text       |
| `date`        | Date recipe first added                                                                                                                                                                            | datetime   |
| `description` | Brief description of recipe                                                                                                                                                                        | text       |
| `credit`      | Name of person/website to credit for recipe idea                                                                                                                                                   | text       |
| `credit_link` | URL to website (if applicable)                                                                                                                                                                     | text       |
| `tags`        | Tags describing recipe, e.g. "dinner", "snack"                                                                                                                                                     | text array |
| `serves`      | Number of people served by ingredient quantities                                                                                                                                                   | integer    |
| `preptime`    | Preparation time, in minutes                                                                                                                                                                       | integer    |
| `cooktime`    | Cooking time, in minutes                                                                                                                                                                           | integer    |
| `ingredients` | List of ingredients required in recipe, each described by an optional quantity (`qty`, type = integer), optional measuring unit (`unit`, type = text) and required item name (`item`, type = text) | array      |
| `steps`       | Ordered list of steps describing how to make recipe                                                                                                                                                | text array |
| `image`       | Photo of recipe, stored as a binary blob                                                                                                                                                           | binary     |
| `image_type`  | Image format (JPG or PNG)                                                                                                                                                                          | text       |

## Contributing

All contributions are welcome, particularly feedback on code quality, bug reports, tips and ideas for improvement.

## License

All code dedicated to the world-wide public domain under a [Creative Commons Zero v1.0 Universal License](https://creativecommons.org/publicdomain/zero/1.0/)
