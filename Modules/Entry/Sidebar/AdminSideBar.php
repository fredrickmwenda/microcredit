<?php
/**
 * Entry Module Sidebar
 */

namespace Modules\Entry\Sidebar;

use Illuminate\Support\Facades\Auth;
use Nwidart\Menus\MenuBuilder;

class AdminSideBar
{
    protected $menu;

    public function __construct(MenuBuilder $menu)
    {
        $this->menu = $menu;
        $this->get_menu();
    }

    function get_menu()
    {
        $this->menu->dropdown('Entry', function ($sub) {
            // Savings sub-menu
            $sub->dropdown('Savings', function ($subsub) {
                $subsub->url('entry/savings/bulk_entry', 'Bulk Entry', ['icon' => 'fa fa-circle-o']);
            }, ['icon' => 'fa fa-circle-o']);
            
            // Loan sub-menu will be added here in future
            // $sub->dropdown('Loans', function ($subsub) {
            //     $subsub->url('entry/loans/bulk_entry', 'Bulk Entry', ['icon' => 'fa fa-circle-o']);
            // }, ['icon' => 'fa fa-circle-o']);
        }, ['icon' => 'fa fa-file-text']);
    }
}
