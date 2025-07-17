<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Rows;

class UserRoleLayout extends Rows
{
    /**
     * Views.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            CheckBox::make('user.roles.super-admin')
                ->title('Super Admin')
                ->placeholder('Full system access with all permissions')
                ->help('Has access to all features and can manage other administrators')
                ->sendTrueOrFalse()
                ->canSee(auth()->user()->inRole('super-admin')),

            CheckBox::make('user.roles.admin')
                ->title('Admin')
                ->placeholder('Administrative access with most permissions')
                ->help('Can manage stories, categories, tags, users, and members')
                ->sendTrueOrFalse(),

            CheckBox::make('user.roles.editor')
                ->title('Editor')
                ->placeholder('Content management access')
                ->help('Can create, edit, and publish stories, manage categories and tags')
                ->sendTrueOrFalse(),

            CheckBox::make('user.roles.author')
                ->title('Author')
                ->placeholder('Content creation access')
                ->help('Can create and edit stories, view categories and tags')
                ->sendTrueOrFalse(),

            CheckBox::make('user.roles.viewer')
                ->title('Viewer')
                ->placeholder('Read-only access')
                ->help('Can view stories, categories, tags, and analytics')
                ->sendTrueOrFalse(),
        ];
    }
}