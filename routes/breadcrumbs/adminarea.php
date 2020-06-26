<?php

declare(strict_types=1);

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;


Breadcrumbs::register('adminarea.certificates.import', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('adminarea.certificates.index');
    $breadcrumbs->push('certificates', route('adminarea.certificates.index'));
});