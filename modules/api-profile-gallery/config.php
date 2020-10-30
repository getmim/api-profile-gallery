<?php

return [
    '__name' => 'api-profile-gallery',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/api-profile-gallery.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'https://iqbalfn.com/'
    ],
    '__files' => [
        'modules/api-profile-gallery' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'api' => NULL
            ],
            [
                'lib-app' => NULL
            ],
            [
                'profile-gallery' => NULL
            ],
            [
                'profile-auth' => NULL 
            ],
            [
                'lib-form' => NULL 
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'ApiProfileGallery\\Controller' => [
                'type' => 'file',
                'base' => 'modules/api-profile-gallery/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'api' => [
            'apiProfileGallery' => [
                'path' => [
                    'value' => '/profile/read/(:name)/gallery',
                    'params' => [
                        'name' => 'slug'
                    ]
                ],
                'handler' => 'ApiProfileGallery\\Controller\\Gallery::index',
                'method' => 'GET'
            ],
            'apiProfileGalleryCreate' => [
                'path' => [
                    'value' => '/profile/read/(:name)/gallery',
                    'params' => [
                        'name' => 'slug'
                    ]
                ],
                'handler' => 'ApiProfileGallery\\Controller\\Gallery::create',
                'method' => 'POST'
            ],
            'apiProfileGalleryRemove' => [
                'path' => [
                    'value' => '/profile/read/(:name)/gallery/(:id)',
                    'params' => [
                        'name' => 'slug',
                        'id' => 'number'
                    ]
                ],
                'handler' => 'ApiProfileGallery\\Controller\\Gallery::remove',
                'method' => 'DELETE'
            ],
            'apiProfileGallerySingle' => [
                'path' => [
                    'value' => '/profile/read/(:name)/gallery/(:id)',
                    'params' => [
                        'name' => 'slug',
                        'id' => 'number'
                    ]
                ],
                'handler' => 'ApiProfileGallery\\Controller\\Gallery::single',
                'method' => 'GET'
            ],
            'apiProfileGalleryUpdate' => [
                'path' => [
                    'value' => '/profile/read/(:name)/gallery/(:id)',
                    'params' => [
                        'name' => 'slug',
                        'id' => 'number'
                    ]
                ],
                'handler' => 'ApiProfileGallery\\Controller\\Gallery::update',
                'method' => 'PUT'
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'api.profile-gallery.create' => [
                '@extends' => ['api.profile-gallery.edit'],
                'title' => [
                    'rules' => [
                        'required' => true 
                    ]
                ],
                'images' => [
                    'rules' => [
                        'required' => true 
                    ]
                ]
            ],
            'api.profile-gallery.edit' => [
                'title' => [
                    'label' => 'Title',
                    'rules' => []
                ],
                'images' => [
                    'label' => 'Image List',
                    'rules' => [
                        'array' => TRUE,
                        'length' => [
                            'min' => 1
                        ]
                    ],
                    'children' => [
                        '*' => [
                            'rules' => [
                                'object' => true
                            ],
                            'children' => [
                                'url' => [
                                    'rules' => [
                                        'required' => true,
                                        'empty' => false,
                                        'upload' => 'std-image' 
                                    ]
                                ],
                                'label' => [
                                    'rules' => [
                                        "required" => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];