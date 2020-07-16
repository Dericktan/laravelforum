# Laravel Forum Package - Chatter

### Installation

Quick Note: If this is a new project, make sure to install the default user authentication provided with Laravel. `php artisan make:auth`

1. Include the package in your project

    ```
    composer require "dericktan/chatter=0.2.*"
    ```

2. Add the service provider to your `config/app.php` providers array:

    ```
    DevDojo\Chatter\ChatterServiceProvider::class,
    ```

3. Publish the Vendor Assets files by running:

    ```
    php artisan vendor:publish
    ```

4. Now that we have published a few new files to our application we need to reload them with the following command:

    ```
    composer dump-autoload
    ```

5. Run Your migrations:

    ```
    php artisan migrate
    ```

    Quick tip: Make sure that you've created a database and added your database credentials in your `.env` file.

6. Run the seed files to seed your database with a little data:

    ```
    php artisan db:seed --class=ChatterTableSeeder
    ```


7. Markdown editor

If you are going to make use of the markdown editor instead of tinymce you will need to change that in your config/chatter.php:

```
'editor' => 'simplemde',
```

In order to properly display the posts you will need to include the  `graham-campbell/markdown` library for Laravel:

```
composer require graham-campbell/markdown
```

8. Open your editor and change Event::fire in vender/dericktan/chatter/src/Controllers/ChatterDiscussionController.php and vender/dericktan/chatter/src/Controllers/ChatterPostController.php to Event::dispatch.

9. Lastly, run  
```
composer require laravel/helpers
```

Now, visit your site.com/forums and you should see your new forum in front of you!!!




------------------------------------------------------------------------------------------------------------------------------------
### Customization

*CUSTOM CSS*

If you want to add additional style changes you can simply add another stylesheet at the end of your `@yield('css')` statement in the head of your master file. In order to only load this file when a user is accessing your forums you can include your stylesheet in the following `if` statement:

```
@if(Request::is( Config::get('chatter.routes.home') ) || Request::is( Config::get('chatter.routes.home') . '/*' ))
    <!-- LINK TO YOUR CUSTOM STYLESHEET -->
    <link rel="stylesheet" href="/assets/css/forums.css" />
@endif
```

*SEO FRIENDLY PAGE TITLES*

Since the forum uses your master layout file, you will need to include the necessary code in order to display an SEO friendly title for your page. The following code will need to be added to the `<head>` of your master file:

```
@if( Request::is( Config::get('chatter.routes.home')) )
    <title>Title for your forum homepage -  Website Name</title>
@elseif( Request::is( Config::get('chatter.routes.home') . '/' . Config::get('chatter.routes.category') . '/*' ) && isset( $discussion ) )
    <title>{{ $discussion->category->name }} - Website Name</title>
@elseif( Request::is( Config::get('chatter.routes.home') . '/*' ) && isset($discussion->title))
    <title>{{ $discussion->title }} - Website Name</title>
@endif
```

*OVERRIDING VIEWS*

In order to override Chatter's built in views, simply create a `chatter` folder in your `vendor` views folder, i.e. `ROOT/resources/views/vendor/chatter`. Then simply drop in the Chatter view files you would like to override.

- [home.blade.php](https://github.com/thedericktan/chatter/blob/master/src/Views/home.blade.php)
- [discussion.blade.php](https://github.com/thedericktan/chatter/blob/master/src/Views/discussion.blade.php)

### Custom Function Hooks for the forum

Sometimes you may want to add some additional functionality when a user creates a new discussion or adds a new response. Well, there are a few built in functions that you can create in your script to access this functionality:

*Before User Adds New Discussion*
Create a new global function in your script called:
```
function chatter_before_new_discussion($request, $validator){}
```

Note: that the `$request` object is passed with the user input for each webhook. You can use it if you would like :) If not, no worries just add your custom functionality.

*After User Adds New Discussion*
Create a new global function in your script called:
```
function chatter_after_new_discussion($request){}
```

*Before User Adds New Response*
Create a new global function in your script called:
```
function chatter_before_new_response($request, $validator){}
```

*After User Adds New Response*
Create a new global function in your script called:
```
function chatter_after_new_response($request){}
```

### Laravel [Events](https://laravel.com/docs/events) for the forum

This package provides a number of events allowing you to respond to user actions as they happen:

| Event        | Available properties           | Description  |
| ------------- |:-------------:| -----:|
| `ChatterBeforeNewDiscussion`      | `Illuminate\Http\Request ($request)`, `Illuminate\Validation\Validator ($validator)` | This event is fired *before* a discussion is validated and created |
| `ChatterAfterNewDiscussion`      | `Illuminate\Http\Request ($request)` | This event is fired *after* a discussion has been validated and created |
| `ChatterBeforeNewResponse`      | `Illuminate\Http\Request ($request)`, `Illuminate\Validation\Validator ($validator)` | This event is fired *before* a response is validated and created |
| `ChatterAfterNewResponse`      | `Illuminate\Http\Request ($request)` | This event is fired *after* a response is validated and created |

#### Listening for Events
To register your listeners for these events, follow the [Laravel documentation for registering events and listeners](https://laravel.com/docs/events#registering-events-and-listeners).
For example, to register a listener for the "before new discussion" event, add the following to your `EventServiceProvider`:

```php
protected $listen = [
    'DevDojo\Chatter\Events\ChatterBeforeNewDiscussion' => [
        'App\Listeners\HandleNewDiscussion',
    ],
];
```

where `App\Listeners\HandleNewDiscussion` is a class in your application which handles the event when it's fired.
