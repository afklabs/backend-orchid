<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            // Dashboard
            Menu::make('Dashboard')
                ->icon('bs.speedometer2')
                ->title('Main')
                ->route('platform.main'),

            // Stories Section (Temporarily disabled until we create the screens)
            Menu::make('Stories')
                ->icon('bs.book')
                ->url('#')
                ->permission('list stories')
                ->title('Content Management')
                ->canSee(false), // Disabled for now

            Menu::make('Publishing History')
                ->icon('bs.clock-history')
                ->url('#')
                ->permission('list stories')
                ->canSee(false), // Disabled for now

            Menu::make('Categories')
                ->icon('bs.folder')
                ->url('#')
                ->permission('list categories')
                ->canSee(false), // Disabled for now

            Menu::make('Tags')
                ->icon('bs.tags')
                ->url('#')
                ->permission('list tags')
                ->canSee(false), // Disabled for now

            // Members Section (Disabled for now)
            Menu::make('Members')
                ->icon('bs.people')
                ->url('#')
                ->permission('list members')
                ->title('User Management')
                ->canSee(false), // Disabled for now

            // Admin Section (Working)
            Menu::make('Admin Users')
                ->icon('bs.person-gear')
                ->route('platform.systems.users')
                ->permission('list users')
                ->title('Administration'),

            Menu::make('Roles')
                ->icon('bs.shield-check')
                ->route('platform.systems.roles')
                ->permission('list roles'),

            Menu::make('Permissions')
                ->icon('bs.key')
                ->url('#')
                ->permission('list permissions')
                ->canSee(false), // Disabled for now

            Menu::make('Settings')
                ->icon('bs.gear')
                ->url('#')
                ->permission('manage settings')
                ->canSee(false), // Disabled for now

            // Analytics Section (Disabled for now)
            Menu::make('Analytics')
                ->icon('bs.graph-up')
                ->url('#')
                ->permission('view analytics')
                ->title('Reports')
                ->canSee(false), // Disabled for now

            Menu::make('Member Analytics')
                ->icon('bs.people-fill')
                ->url('#')
                ->permission('view member analytics')
                ->canSee(false), // Disabled for now

            Menu::make('Story Analytics')
                ->icon('bs.book-half')
                ->url('#')
                ->permission('view story analytics')
                ->canSee(false), // Disabled for now

            // System Section (Disabled for now)
            Menu::make('System Logs')
                ->icon('bs.file-text')
                ->url('#')
                ->permission('view logs')
                ->title('System')
                ->canSee(false), // Disabled for now
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group('Story Management')
                ->addPermission('list stories', 'List Stories')
                ->addPermission('show stories', 'Show Stories')
                ->addPermission('create stories', 'Create Stories')
                ->addPermission('update stories', 'Update Stories')
                ->addPermission('delete stories', 'Delete Stories')
                ->addPermission('publish stories', 'Publish Stories')
                ->addPermission('unpublish stories', 'Unpublish Stories'),

            ItemPermission::group('Category Management')
                ->addPermission('list categories', 'List Categories')
                ->addPermission('show categories', 'Show Categories')
                ->addPermission('create categories', 'Create Categories')
                ->addPermission('update categories', 'Update Categories')
                ->addPermission('delete categories', 'Delete Categories'),

            ItemPermission::group('Tag Management')
                ->addPermission('list tags', 'List Tags')
                ->addPermission('show tags', 'Show Tags')
                ->addPermission('create tags', 'Create Tags')
                ->addPermission('update tags', 'Update Tags')
                ->addPermission('delete tags', 'Delete Tags'),

            ItemPermission::group('User Management')
                ->addPermission('list users', 'List Admin Users')
                ->addPermission('show users', 'Show Admin Users')
                ->addPermission('create users', 'Create Admin Users')
                ->addPermission('update users', 'Update Admin Users')
                ->addPermission('delete users', 'Delete Admin Users'),

            ItemPermission::group('Member Management')
                ->addPermission('list members', 'List Members')
                ->addPermission('show members', 'Show Members')
                ->addPermission('create members', 'Create Members')
                ->addPermission('update members', 'Update Members')
                ->addPermission('delete members', 'Delete Members')
                ->addPermission('activate members', 'Activate Members')
                ->addPermission('suspend members', 'Suspend Members'),

            ItemPermission::group('Role & Permission Management')
                ->addPermission('list roles', 'List Roles')
                ->addPermission('show roles', 'Show Roles')
                ->addPermission('create roles', 'Create Roles')
                ->addPermission('update roles', 'Update Roles')
                ->addPermission('delete roles', 'Delete Roles')
                ->addPermission('list permissions', 'List Permissions')
                ->addPermission('show permissions', 'Show Permissions'),

            ItemPermission::group('Analytics')
                ->addPermission('view analytics', 'View Analytics')
                ->addPermission('view member analytics', 'View Member Analytics')
                ->addPermission('view story analytics', 'View Story Analytics')
                ->addPermission('export analytics', 'Export Analytics'),

            ItemPermission::group('System')
                ->addPermission('view logs', 'View System Logs')
                ->addPermission('manage settings', 'Manage Settings')
                ->addPermission('backup system', 'Backup System')
                ->addPermission('restore system', 'Restore System'),
        ];
    }
}