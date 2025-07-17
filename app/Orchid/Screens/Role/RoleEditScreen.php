<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Role;

use App\Orchid\Layouts\Role\RoleEditLayout;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RoleEditScreen extends Screen
{
    /**
     * @var Role
     */
    public $role;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Role $role): iterable
    {
        return [
            'role'       => $role,
            'permission' => $role->statusOfPermissions(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return ($this->role && $this->role->exists) ? 'Edit Role' : 'Create Role';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Modify the privileges and permissions associated with a specific role.';
    }

    /**
     * The permissions required to access this screen.
     */
    public function permission(): ?iterable
    {
        // Check permissions based on action
        if ($this->role && $this->role->exists) {
            return ['update roles'];
        }
        
        return ['create roles'];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make(__('Remove'))
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->role && $this->role->exists && auth()->user()->can('delete roles'))
                ->confirm(__('Are you sure you want to delete this role? This action cannot be undone.')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block([
                RoleEditLayout::class,
            ])
                ->title('Role')
                ->description('Defines a set of privileges that grant users access to various services and allow them to perform specific tasks or operations.'),

            Layout::block([
                RolePermissionLayout::class,
            ])
                ->title('Permission/Privilege')
                ->description('A privilege is necessary to perform certain tasks and operations in an area.'),
        ];
    }

    /**
     * Save role
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Role $role)
    {
        // Check permissions
        if ($role->exists && !auth()->user()->can('update roles')) {
            Toast::error(__('You do not have permission to update roles.'));
            return redirect()->route('platform.systems.roles');
        }

        if (!$role->exists && !auth()->user()->can('create roles')) {
            Toast::error(__('You do not have permission to create roles.'));
            return redirect()->route('platform.systems.roles');
        }

        $request->validate([
            'role.name' => 'required|string|max:255',
            'role.slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique(Role::class, 'slug')->ignore($role),
            ],
        ]);

        $role->fill($request->get('role'));

        $role->permissions = collect($request->get('permissions'))
            ->map(fn ($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $role->save();

        Toast::info(__('Role was saved'));

        return redirect()->route('platform.systems.roles');
    }

    /**
     * Remove role
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Role $role)
    {
        // Check permission
        if (!auth()->user()->can('delete roles')) {
            Toast::error(__('You do not have permission to delete roles.'));
            return redirect()->route('platform.systems.roles');
        }

        // Prevent deletion if role has users
        if ($role->users()->count() > 0) {
            Toast::error(__('Cannot delete role. There are users assigned to this role.'));
            return redirect()->route('platform.systems.roles');
        }

        $role->delete();

        Toast::info(__('Role was removed'));

        return redirect()->route('platform.systems.roles');
    }
}