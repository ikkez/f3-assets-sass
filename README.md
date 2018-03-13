# SASS addon for F3 Sugar Assets
This is an extention to add a SASS/SCSS compiler to the existing [Assets Management plugin](https://github.com/ikkez/f3-assets) for the [PHP Fat-Free Framework](https://github.com/bcosca/fatfree).

## Install

When you have F3-Assets already up and running, just run `composer require ikkez/f3-assets-sass`. 
In case you do not use composer, copy the `assets/` folder into your `AUTOLOAD` path, install [leafo/scssphp](https://github.com/leafo/scssphp) separately and you should be ready to go.


## Usage

To register the sass compiler, just add this line to your view controller, or where ever you have put the initialisation of the main assets plugin. 

```php
// register sass handler
\Assets\Sass::instance()->init();
```

Within your templates you can then easily use `.scss` files directly, as it would be normal css files.

```html
<link rel="stylesheet" href="scss/main_styles.scss">
```

That's it. Compilation, minification and concatenation with other files is now handled by the assets plugin.
The base directory of the sass file can also be used as import path, 
so using `@import` within your sass file can be used to load other relative sass files. So recompiling a whole bootstrap frontend is not problem:

```html
<link rel="stylesheet" href="components/MDBootstrap/sass/mdb.scss">
```

The only drawback with `@include` files is, that changes to those files are currently not detected automatically, 
so the whole main sass file does not update on the fly. However, you can add the `watch` attribute and define 
one or multiple paths to scan for file changes - wildcards are possible:
 
```html
<link rel="stylesheet" href="components/MDBootstrap/sass/mdb.scss" watch="custom.scss">

<link rel="stylesheet" href="components/MDBootstrap/sass/mdb.scss" watch="custom.scss, addons/*.scss">
```

NB: The `watch`-attribute should only be considered while working on the files, because scanning for file modification times isn't necessary for production-ready styles and would, depending on the amount of files to scan, slow things down. 
If you need to refresh files on a production environment, it's recommended to clear the temp files with `\Assets::instance()->clear();`, instead of proactive looking for changes.


License
-

GPLv3
