# PHPTemplate
Free PHP template engine for neat and powerful projects!

## Overview
Modern application architectures follow user interface and logic segregation.
Application logic layer provides information to display in user interface layer.
Thus, the user interface (view) will be separated from logic layer (controller).

Template Engines offer structured solution to implement this segregation.
Let's see!

User Interfaces are template files which include static (HTML) and dynamic parts.
Dynamic parts are the places which Template Engine put information there.
Controller pass information and template name to Template Engine.
Template Engine returns compiled template (ultimate HTML).
The controller send the result to the user.

PHPTemplate syntax is the best syntax for HTML, XML and tag based documents.
It is designed smartly to be pretty while web designers design.

### Installation
#### Using Composer (Recommended)
Read
[How to use composer in php projects](http://miladrahimi.com/blog/2015/04/12/how-to-use-composer-in-php-projects)
article if you are not familiar with [Composer](http://getcomposer.org).

Run following command in your project root directory:

```
composer require miladrahimi/phptemplate
```

#### Manually
You may use your own autoloader as long as it follows [PSR-0](http://www.php-fig.org/psr/psr-0) or
[PSR-4](http://www.php-fig.org/psr/psr-4) standards.
Just put `src` directory contents in your vendor directory.

### Getting Started
Following example shows how to render `profile.html` template file.

A simple template file example:

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory("../Views");

$data = array(
    "name"     => "Bon",
    "surname"  => "Jovi"
);

echo $te->render("profile.html", $data);
```

And the `profile.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>
<body>
    <h1>Profile</h1>
    <p>Welcome {name} {surname}!</p>
</body>
</html>
```

### Phrases
Phrases are simple variables which will be filled by Template Engine based on given data.
As the example above illustrates `{name}` and `{surname}` are phrase.

Phrases will be HTML-safe by PHP native `htmlspecialchars()` function.
If you need raw data to be inserted like HTML snippets, put `!` after `{` character. See following example:

```
<div> {!content} </div>
```

### Flexible Templates
Phrases are used to display data in the view (template).
But sometimes the data must be controlled or manipulated before displaying.
Some data must not be displayed always, like "sign in" button when user is authenticated already.
Some data must be repeated more than once, like posts in a blog homepage.
PHPTemplate offers boolean and array tags to implement such cases.

### Boolean Tags
Boolean tags are used to display content while the tag name as an element in the given data is true.

Let's extend the mentioned example:

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory("../Views");

$data = array(
    "name"     => "Bon",
    "surname"  => "Jovi",
    "is-admin" => true
);

echo $te->render("profile.html", $data);
```

And the `profile.html` file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>
<body>
    <h1>Profile</h1>
    <p>Welcome {name} {surname}!</p>
    <is-admin>You are admin!</is-admin>
</body>
</html>
```

In web design level, boolean tags will be displayed in the browser.

### Sequential Arrays
PHPTemplate HTML-like syntax for sequential arrays made using arrays so easy.
Array can be implemented by tags just like booleans.
The tag content will be repeated as much as array length.
Just like a `foreach` loop in PHP,
you need to define a name for current array element to use in the body.
In PHPTemplate you can define this name by HTML-like `value` attribute.

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "name"    => "David",
    "surname" => "Gilmour",
    "genres"  => array("Progressive Rock", "Art Rock", "Blues Rock")
);

echo $te->render("singer.html", $data);
```

And the `singer.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Singer</title>
</head>
<body>
    <h1>{name} {surname}</h1>
    <h2>Genres</h2>
    <genres value="item">
        {item} <br>
    </genres>
</body>
</html>
```

* `genres` tag content will be repeated as much as `genres` array length.
* You can access current element by set name in the `value` attribute, here `item`.
* In web design level, the array body will be displayed once.

### Associative Arrays
Associative Arrays can be passed to template engine by its paired key/value without array container.
Of course PHPTemplate still support it very well.

Associative arrays are pack of key/value elements.
Just like a `foreach()` loop,
you need to define two names, one for current key and one for current value.

In the following example,
I have define `album` name for current key and `year` for current value.

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "name"    => "David",
    "surname" => "Gilmour",
    "genres"  => array("Progressive Rock", "Art Rock", "Blues Rock"),
    "albums"  => array("On an Island" => 2006, "Rattle That Lock" => 2015)
);

echo $te->render("singer.html", $data);
```

And the `singer.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Singer</title>
</head>
<body>
    <h1>{name} {surname}</h1>
    <h2>Genres</h2>
    <genres value="item">
        {item} <br>
    </genres>
    <h2>Albums</h2>
    <albums key="album" value="year">
        {album} : {year} <br>
    </albums>
</body>
</html>
```

### Records
Records are associate array which you need to access its all the elements at once.
Record concept is so useful in table designs.
PHPTemplate let you access records as easy as a pie!

See the example and `singles` arrays which has some records:

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "name"    => "David",
    "surname" => "Gilmour",
    "genres"  => array("Progressive Rock", "Art Rock", "Blues Rock"),
    "albums"  => array("On an Island" => 2006, "Rattle That Lock" => 2015),
    "singles" => array(
        array("track" => "There's No Way Out of Here", "album" => "David Gilmour"),
        array("track" => "Blue Light",                 "album" => "About Face"),
        array("track" => "Love on the Air",            "album" => "About Face")
    )
);

echo $te->render("singer.html", $data);
```

And the `singer.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Singer</title>
</head>
<body>
    <h1>{name} {surname}</h1>
    <h2>Genres</h2>
    <genres value="item">
        {item} <br>
    </genres>
    <h2>Albums</h2>
    <albums key="album" value="year">
        {album} : {year} <br>
    </albums>
    <h2>Singles</h2>
    <singles value="single">
        <single>
            {track} of the album {album} <br>
        </single>
    </singles>
</body>
</html>
```

* No need to define key and value for records.
* All record array keys will be accessible via phrases.
* It's a good feature for table design.

### Data types
PHPTemplate converts all data to strings or arrays.

All scalar data like strings, integers, floats, etc will be converted to string.

All objects which has `__toString()` method will be converted to strings.

All arrays and traversable objects will be converted to arrays.

All closures will be invoked and the returned value will be checked recursively.

If the data type is not convert-able to string or array, `BadDataException` will be thrown.

### Functions
It's a good practice to avoid using logical directives in the view layer.
However, you may still need to access your application APIs in the view layer.
You can pass closures like other valid data types to the template engine.
The passed closure must return a closure or a valid data type value to be processed and replaced in the phrase.

See the `date` element in the following example:

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "name"    => "David",
    "surname" => "Gilmour",
    "genres"  => array("Progressive Rock", "Art Rock", "Blues Rock"),
    "albums"  => array("On an Island" => 2006, "Rattle That Lock" => 2015),
    "singles" => array(
        array("track" => "There's No Way Out of Here", "album" => "David Gilmour"),
        array("track" => "Blue Light", "album" => "About Face"),
        array("track" => "Love on the Air", "album" => "About Face")
    ),
    "date"    => function () {
        return date("Y/m/d");
    },
);

echo $te->render("singer.html", $data);
```

And the `singer.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Singer</title>
</head>
<body>
    <h1>{name} {surname}</h1>
    <h2>Genres</h2>
    <genres value="item">
        {item} <br>
    </genres>
    <h2>Albums</h2>
    <albums key="album" value="year">
        {album} : {year} <br>
    </albums>
    <h2>Singles</h2>
    <singles value="single">
        <single>
            {track} of the album {album} <br>
        </single>
    </singles>
    <p>Today: {date}</p>
</body>
</html>
```

### Scopes
Scopes override data with the same name of the higher level scope. 

For example the `singer` records will overwrite `name` and `surname` in the following example:

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "name" => "Selena",
    "surname" => "Gomez",
    "others" => array(
        array("name" => "Taylor", "surname" => "Swift"),
        array("name" => "Demi", "surname" => "Lovato")
    )
);

echo $te->render("singer.html", $data);
```

And the `singer.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Young Singers</title>
</head>
<body>
    <h1>Young Singers</h1>
    Best Singer: {name} {surname}. <br>
    Others: <br>
    <others value="singer">
        <singer>{name} {surname}</singer><br>
    </others>
</body>
</html>
```

### Importing External Template Files
You may need to import external files.

See the following example which `page.html` imports `head.html` file.

```
use MiladRahimi\PHPTemplate\TemplateEngineFactory;

$te = TemplateEngineFactory::create();
$te->setBaseDirectory(__DIR__);

$data = array(
    "page_title" => "Roger Waters Biography",
    "name"       => "Roger",
    "surname"    => "Waters",
);

echo $te->render("page.html", $data);
```

And the `page.html` template file:

```
<!DOCTYPE html>
<html lang="en">
<import file="head.html">
<body>
    <p>Welcome {name} {surname}!</p>
</body>
</html>
```

And the `head.html` template file:

```
<head>
    <meta charset="UTF-8">
    <title>{page_title}</title>
</head>
```

### Framework Integration
You can install PHPTemplate using Composer.
While all of modern PHP frameworks supports Composer packages,
You can use PHPTemplate in the most of popular frameworks easily.

## License
PHPTemplate is created by [Milad Rahimi](http://miladrahimi.com)
and released under the [MIT License](http://opensource.org/licenses/mit-license.php).
