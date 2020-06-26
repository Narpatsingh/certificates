<?php

declare(strict_types=1);

use Cortex\Auth\Models\Role;
use Cortex\Auth\Models\Admin;
use Cortex\Auth\Models\Member;
use Cortex\Auth\Models\Ability;
use Cortex\Auth\Models\Manager;
use Cortex\Auth\Models\Guardian;
use Rinvex\Menus\Models\MenuItem;
use Rinvex\Menus\Models\MenuGenerator;

Menu::register('adminarea.sidebar', function (MenuGenerator $menu, Ability $ability, Role $role, Admin $admin, Manager $manager, Member $member, Guardian $guardian) {


    $menu->findByTitleOrAdd('Certificate Manager', 10, 'fa fa-certificate', [], function (MenuItem $dropdown) use ($role) {
        $dropdown->route(['adminarea.certificates.accounts.index'], "Accounts", 10, 'fa fa-google')->ifCan('list', $role)->activateOnRoute('adminarea.roles');
        $dropdown->route(['adminarea.certificates.event_create'], "Events", 10, 'fa fa-calendar')->ifCan('list', $role)->activateOnRoute('adminarea.roles');
    });

});
