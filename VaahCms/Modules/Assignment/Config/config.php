<?php

return [
    "name"=> "Assignment",
    "title"=> "Assignment Management",
    "slug"=> "assignment",
    "thumbnail"=> "https://img.site/p/300/160",
    "is_dev" => env('MODULE_ASSIGNMENT_ENV')?true:false,
    "excerpt"=> "Assignment Management Module",
    "description"=> "Assignment Management Module",
    "download_link"=> "",
    "author_name"=> "vaah",
    "author_website"=> "https://vaah.dev",
    "version"=> "0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> true,
    "db_table_prefix"=> "vh_assignment_",
    "providers"=> [
        "\\VaahCms\\Modules\\Assignment\\Providers\\AssignmentServiceProvider"
    ],
    "aside-menu-order"=> null
];
