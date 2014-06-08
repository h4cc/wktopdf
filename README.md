Wktopdf Wrapper
=======

[![Build Status](https://travis-ci.org/umbrellaTech/wktopdf.png?branch=master)](https://travis-ci.org/umbrellaTech/wktopdf)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/umbrellaTech/wktopdf/badges/quality-score.png?s=d8f9bc50a6423588669ba7570dcf92616f5aacfd)](https://scrutinizer-ci.com/g/umbrellaTech/wktopdf/)
[![Code Coverage](https://scrutinizer-ci.com/g/umbrellaTech/wktopdf/badges/coverage.png?s=1212763c1d636eaed5fcf8c7999e7d4cf38fade6)](https://scrutinizer-ci.com/g/umbrellaTech/wktopdf/)
[![Latest Stable Version](https://poser.pugx.org/umbrella/wktopdf/v/stable.png)](https://packagist.org/packages/umbrella/wktopdf)
[![Latest Unstable Version](https://poser.pugx.org/umbrella/wktopdf/v/unstable.png)](https://packagist.org/packages/umbrella/wktopdf)

WkToPdf is a component for generating PDF files. It is made using the Google's webkit. Remember that you need to have [WKHTMLToPDF](https://github.com/antialize/wkhtmltopdf) installed to use this library.

# Installation

Through Composer, obviously:

```json
{
    "require": {
        "umbrella/wktopdf": "v1.0.*"
    }
}
```

You can also use Wktopdf without using Composer by registering an autoloader function:

```php
spl_autoload_register(function($class) {
    if (!substr($class, 0, 17) === 'Umbrella\\WkToPdf') {
        return;
    }

    $location = __DIR__ . 'path/to/wktopdf/src/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($location)) {
        require_once($location);
    }
});
```

Usage
-----

To render a simple PDF file:

```php
//The content that will be converted to HTML
$htmlContent = '<h1>Hello World</h1>';

// The location of the HTML output, this is a temp file.
$tempHtmlOutput = '/tmp';

//The location where pdf will be saved.
$output = '/home/user';

$renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath($tempHtmlOutput)
                ->setPdfName('pde-demo.pdf')
                ->setPdfPath($output);
```

To render a footer and header to all pages:

```php
//The content that will be converted to HTML
$htmlContent = '<h1>Hello World</h1>';

// The location of the HTML output, this is a temp file.
$tempHtmlOutput = '/tmp';

//The location where pdf will be saved.
$output = '/home/user';

$renderer = new \Umbrella\WkToPdf\WkPdfRenderer();
        $renderer->setHtmlContent($htmlContent)
                ->setHtmlPath($tempHtmlOutput)
                ->setPdfName('pde-demo.pdf')
                ->setPdfPath($output);
                
$serverUrl = $_SERVER['HTTP_HOST'];
$time = microtime();

$header = new \Umbrella\WkToPdf\HeaderOptions();
//Sets the template that will be used on the header.
$header->setPathTemplate("/your/app/templates/header.html")
      // The wkhtmltopdf footer needs to be acessible form a url
      ->setHtmlUrl("http://{$serverUrl}/tmp/{$time}.html")
      // The path where the final html header will be rendered
      ->setPath(constant('APPLICATION_PATH') . "/tmp/{$time}.html");
$renderer->setHeader($header);

$footer = new \Umbrella\WkToPdf\FooterOptions();
//Sets the template that will be used on the footer.
$footer->setPathTemplate("/your/app/templates/footer.html")
      // The wkhtmltopdf footer needs to be acessible form a url
      ->setHtmlUrl("http://{$serverUrl}/tmp/{$time}.html")
      // The path where the final html footer will be rendered
      ->setPath(constant('APPLICATION_PATH') . "/tmp/{$time}.html");
$renderer->setFooter($footer);
```

To set any options available on wkhtmlltopdf, just use:
```php
// Global options
$renderer->getOptions()
            ->set('margin.top', '1cm')
            ->set('margin.right', '0')
            ->set('margin.bottom', '2.5cm')
            ->set('margin.left', '0');
            
// Page options
$renderer->getPdfOptions();
            
```

License
-------

The MIT License (MIT)

Copyright (c) 2012-2014 Florian Eckerstorfer

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
