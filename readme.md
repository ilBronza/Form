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

## Fieldsets

### Parameteri
``` bash

    #vengono aggiunte alle classi html di default
    array 'classes' => [
        'classe-uno',
        'classe-due'
    ]

    #vengono sostituite alle classi html di default
    array 'classes' => [
        'classe-uno',
        'classe-due'
    ]
    
    #decide se mostrare o meno la legenda sul fieldset
    bool 'showLegend' => true/false
    
    #decide se applicare o meno i divisori della grid uikit
    bool 'divider' => true/false
    
    
    
    

```


### Esempio
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

### Add field inside existing fieldset

```
//ad single field in 3rd position of maindata fieldset
        
$fieldsToAdd = [
    'event' => ['text' => 'string|required|max:128']
];
           
$currentFields = $result['mainData']['fields']; 
        
$result['mainData']['fields'] = static::insertFieldsInPosition(
    $fieldsToAdd,
    $currentFields,
    3
);

//condensed 

$result['mainData']['fields'] = static::insertFieldsInPosition(
    ['event' => ['text' => 'string|required|max:128']],
    $result['mainData']['fields'],
    3
);
		
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
