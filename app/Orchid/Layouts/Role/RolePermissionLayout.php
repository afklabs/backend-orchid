<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Role;

use Illuminate\Support\Collection;
use App\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Throwable;

class RolePermissionLayout extends Rows
{
    /**
     * @var User|null
     */
    private $user;

    /**
     * The screen's layout elements.
     *
     * @throws Throwable
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $this->user = $this->query->get('user');

        return array_merge(
            $this->getControlFields(),
            $this->generatedPermissionFields(
                $this->query->getContent('permission')
            )
        );
    }

    /**
     * Get control fields (search, select all, deselect all)
     *
     * @return Field[]
     */
    private function getControlFields(): array
    {
        return [
            Input::make('permission_search')
                ->title('🔍 Search Permissions')
                ->placeholder('Type to search permissions...')
                ->help('Search through permissions by name or description'),

            Input::make('select_all_btn')
                ->type('button')
                ->value('✅ Select All')
                ->help('Select all visible permissions'),

            Input::make('deselect_all_btn')
                ->type('button')
                ->value('❌ Deselect All')
                ->help('Deselect all visible permissions'),
        ];
    }

    /**
     * Generate permission fields
     */
    private function generatedPermissionFields(Collection $permissionsRaw): array
    {
        return $permissionsRaw
            ->map(fn (Collection $permissions, $title) => $this->makeCheckBoxGroup($permissions, $title))
            ->flatten()
            ->toArray();
    }

    /**
     * Make checkbox group for permissions
     */
    private function makeCheckBoxGroup(Collection $permissions, string $title): Collection
    {
        return $permissions
            ->map(fn (array $chunks) => $this->makeCheckBox(collect($chunks)))
            ->flatten()
            ->map(fn (CheckBox $checkbox, $key) => $key === 0
                ? $checkbox->title("📋 " . $title)
                : $checkbox)
            ->chunk(3)
            ->map(fn (Collection $checkboxes) => Group::make($checkboxes->toArray()));
    }

    /**
     * Make individual checkbox
     */
    private function makeCheckBox(Collection $chunks): CheckBox
    {
        return CheckBox::make('permissions.'.base64_encode($chunks->get('slug')))
            ->placeholder($chunks->get('description'))
            ->value($chunks->get('active'))
            ->sendTrueOrFalse()
            ->set('data-permission-name', $chunks->get('slug'))
            ->set('data-permission-desc', $chunks->get('description'))
            ->indeterminate($this->getIndeterminateStatus(
                $chunks->get('slug'),
                $chunks->get('active')
            ));
    }

    /**
     * Get indeterminate status for checkbox
     */
    private function getIndeterminateStatus($slug, $value): bool
    {
        return optional($this->user)->hasAccess($slug) === true && $value === false;
    }

    /**
     * Custom CSS and JavaScript for enhanced functionality
     */
    public function __toString()
    {
        $content = parent::__toString();
        
        $script = <<<'HTML'
<style>
.permission-group {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    background-color: #f8f9fc;
}

.permission-search-highlight {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    border-radius: 4px;
}

input[name="permission_search"] {
    margin-bottom: 1rem;
}

input[name="select_all_btn"], 
input[name="deselect_all_btn"] {
    margin: 0.5rem 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    cursor: pointer;
    font-weight: 500;
}

input[name="select_all_btn"] {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

input[name="select_all_btn"]:hover {
    background-color: #c3e6cb;
}

input[name="deselect_all_btn"] {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

input[name="deselect_all_btn"]:hover {
    background-color: #f5c6cb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('input[name="permission_search"]');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const allCheckboxes = document.querySelectorAll('input[name^="permissions."]');
            
            allCheckboxes.forEach(checkbox => {
                const permissionName = checkbox.getAttribute('data-permission-name') || '';
                const permissionDesc = checkbox.getAttribute('data-permission-desc') || '';
                const label = checkbox.closest('.form-group')?.querySelector('label')?.textContent || '';
                
                const isMatch = searchTerm === '' || 
                              permissionName.toLowerCase().includes(searchTerm) ||
                              permissionDesc.toLowerCase().includes(searchTerm) ||
                              label.toLowerCase().includes(searchTerm);
                
                const checkboxContainer = checkbox.closest('.form-group');
                if (checkboxContainer) {
                    checkboxContainer.style.display = isMatch ? 'block' : 'none';
                    
                    if (isMatch && searchTerm !== '') {
                        checkboxContainer.classList.add('permission-search-highlight');
                    } else {
                        checkboxContainer.classList.remove('permission-search-highlight');
                    }
                }
            });
        });
    }
    
    // Select All functionality
    const selectAllBtn = document.querySelector('input[name="select_all_btn"]');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('input[name^="permissions."]').forEach(checkbox => {
                const container = checkbox.closest('.form-group');
                if (container && container.style.display !== 'none') {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }
    
    // Deselect All functionality
    const deselectAllBtn = document.querySelector('input[name="deselect_all_btn"]');
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('input[name^="permissions."]').forEach(checkbox => {
                const container = checkbox.closest('.form-group');
                if (container && container.style.display !== 'none') {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }
});
</script>
HTML;

        return $content . $script;
    }
}