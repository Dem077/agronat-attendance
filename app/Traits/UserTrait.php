<?php

namespace App\Traits;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait UserTrait
{
    public $user_id;
    public array $users = [];

    public function setUser(): void
    {
        $this->users = $this->accessibleUsersQuery()
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'emp_no' => $user->emp_no,
            ])
            ->toArray();

        $user = Auth::user();

        if ($this->isDepartmentSupervisor($user)) {
            $this->ensureCurrentUserInList($user);
        }

        if (count($this->users) === 1) {
            $this->user_id = $user->id;
        }
    }

    protected function accessibleUsersQuery(): Builder
    {
        $user = Auth::user();
        $query = User::query()->active();

        if ($user->can('reporting-manager')) {
            return $query->select('name', 'id', 'emp_no')->orderBy('name', 'asc');
        }

        $departmentIds = $this->supervisedDepartmentIds($user);

        if ($departmentIds !== []) {
            return $query->select('name', 'id', 'emp_no')
                ->whereIn('department_id', $departmentIds)
                ->orderBy('name', 'asc');
        }

        return $query->select('name', 'id', 'emp_no')
            ->where(function (Builder $builder) use ($user) {
                $builder->where('supervisor_id', $user->id)
                    ->orWhere('id', $user->id);
            })
            ->orderBy('name', 'asc');
    }

    protected function supervisedDepartmentIds(User $user): array
    {
        return Department::where('supervisor_id', $user->id)->pluck('id')->all();
    }

    protected function isDepartmentSupervisor(User $user): bool
    {
        return $this->supervisedDepartmentIds($user) !== [];
    }

    protected function ensureCurrentUserInList(User $user): void
    {
        $ids = array_column($this->users, 'id');

        if (! in_array($user->id, $ids, true)) {
            array_unshift($this->users, [
                'id' => $user->id,
                'name' => $user->name,
                'emp_no' => $user->emp_no,
            ]);
        }
    }

    protected function accessibleUserIds(): array
    {
        if ($this->users === []) {
            $this->setUser();
        }

        return array_column($this->users, 'id');
    }
}
