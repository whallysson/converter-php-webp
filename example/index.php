<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
        <title>Test Webp</title>
    </head>
    <body>
        <?php

        require_once '../vendor/autoload.php';

        use CodeBlog\ToWebP\ToWebP;


        $source = 'image01.jpg';
        $destination = 'image-new-01.webp';

        $wp = new ToWebP('uploads', "images");
        $wp->convert($source, $destination);

        echo $wp->image_webp;


        /**
         * Exemple 02
         */
        $source = 'image01.jpg';
        $destination = 'image-new-02.webp';
        $quality = 90;

        $wp = new ToWebP('uploads', "images");
        $wp->convert($source, $destination, $quality);

        $arr = [
            'picture' => [
                'class' => 'responsive'
            ],
            'img' => [
                'alt' => 'Image converted with the ToWebP library',
                'style'=>'width: 400px;'
            ]
        ];

        echo $wp->picture($arr);
        ?>
    </body>
</html>
