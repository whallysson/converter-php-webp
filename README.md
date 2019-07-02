# ToWebP @Codeblog 

[![Maintainer](http://img.shields.io/badge/maintainer-@whallysson-blue.svg?style=flat-square)](https://twitter.com/whallysson)
[![Source Code](http://img.shields.io/badge/source-codeblog/conveterphpwebp-blue.svg?style=flat-square)](https://github.com/whallysson/conveter-php-webp)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/codeblog/conveter-php-webp.svg?style=flat-square)](https://packagist.org/packages/codeblog/conveter-php-webp)
[![Latest Version](https://img.shields.io/github/release/whallysson/conveter-php-webp.svg?style=flat-square)](https://github.com/whallysson/conveter-php-webp/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build](https://img.shields.io/scrutinizer/build/g/whallysson/conveter-php-webp.svg?style=flat-square)](https://scrutinizer-ci.com/g/whallysson/conveter-php-webp)
[![Quality Score](https://img.shields.io/scrutinizer/g/whallysson/conveter-php-webp.svg?style=flat-square)](https://scrutinizer-ci.com/g/whallysson/conveter-php-webp)
[![Total Downloads](https://img.shields.io/packagist/dt/codeblog/conveter-php-webp.svg?style=flat-square)](https://packagist.org/packages/codeblog/conveter-php-webp)

###### ToWebP converts JPEG & PNG files to WebP format. That is an image format developed by Google that promises to reduce the image file size by up to 39%.

ToWebP faz a conversão de arquivos JPEG & PNG para o formato WebP. Que é um formato de imagem desenvolvido pelo Google que promete reduzir o tamanho do arquivo de imagens em até 39%.


### Highlights

- Run the Cwebp binary directly via exec () (Executa o binário Cwebp diretamente via exec ())
- Compatible with the PHP GD extension (Compativel com a extensão PHP GD)
- Compatible with the PHP Imagick extension (Compativel com a extensão PHP Imagick)
- Validate images by mime-types (Valida de imagens por mime-types)

## Installation

ToWebP is available via Composer:

```bash
"codeblog/conveter-php-webp": "^1.0"
```

or run

```bash
composer require codeblog/conveter-php-webp
```

## Documentation

###### For details on how to use, see a sample folder in the component directory. In it you will have an example of use for each class. It works like this:

Para mais detalhes sobre como usar, veja uma pasta de exemplo no diretório do componente. Nela terá um exemplo de uso para cada classe. Ele funciona assim:

#### Basic usage example

```php
<?php

// Initialise your autoloader (this example is using Composer)
require 'vendor/autoload.php';

use CodeBlog\ToWebP\ToWebP;

$source = 'image01.jpg';
$destination = 'image-new.webp';

$wp = new ToWebP('uploads', "images");
$wp->convert($source, $destination);

echo $wp->image_webp;
```

#### Other Usage Example

```php
<?php

// Initialise your autoloader (this example is using Composer)
require 'vendor/autoload.php';

use CodeBlog\ToWebP\ToWebP;

$source = 'image01.jpg';
$destination = 'image-new.webp';
$quality = 90;

$wp = new ToWebP('uploads', "images");
$wp->convert($source, $destination, $quality);

$arr =[
    'picture' => [
        'class' => 'responsive'
    ],
    'img' => [
        'alt' => 'Image converted with the ToWebP library',
        'style'=>'width: 400px;'
    ]
];

echo $wp->picture($arr);
```

## Contributing

Please see [CONTRIBUTING](https://github.com/whallysson/conveter-php-webp/blob/master/CONTRIBUTING.md) for details.

## Support

###### Security: If you discover any security related issues, please email whallyssonallain@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para whallyssonallain@gmail.com em vez de usar o rastreador de problemas.

Thank you

## Credits

- [Whallysson Avelino](https://github.com/whallysson) (Developer)
- [CodBlog](https://github.com/whallysson) (Team)
- [All Contributors](https://github.com/whallysson/conveter-php-webp/contributors) (This Rock)

## License

The MIT License (MIT). Please see [License File](https://github.com/whallysson/conveter-php-webp/blob/master/LICENSE) for more information.
