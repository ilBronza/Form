# Form

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/form
```

## Usage

``` bash
    static $formFields = [
        'common' => [
            'nominativo' => [
                'containerClasses' => [
                ],
                'classes' => [
                    'uk-section-primary',
                ],
                'fields' => [
                    '3__nome' => [
                        'type' => 'text',
                        'rules' => 'string|nullable|max:255'
                    ],
                    '3__cognome' => ['text' => 'string|nullable|max:255'],
                ],
            ],
            'recapiti' => [
                'classes' => [
                    'uk-background-primary',
                    'uk-text-danger'
                ],
                'fields' => [
                    '3__tel_fisso' => ['text' => 'string|nullable|max:255'],
                    '3__tel_mobile' => ['text' => 'string|nullable|max:255'],
                    '3__email' => ['text' => 'string|nullable|max:255'],
                ],
            ],
            'precisazioni' => [
                'fields' => [
                    '3__precisazioni' => ['textarea' => 'string|nullable|max:2048'],
                    '3__qualita_di' => [
                        'type' => 'radio',
                        'rules' => 'string|nullable',
                        'stacked' => true
                    ]
                ],
                'width' => 1,
            ],
        ]
    ];    


```


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.



## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ilbronza/form.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/form.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/form/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/form
[link-downloads]: https://packagist.org/packages/ilbronza/form
[link-travis]: https://travis-ci.org/ilbronza/form
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
