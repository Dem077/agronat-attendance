<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->identifier)
                        ->orWhere('nid', $request->identifier)
                        ->orWhere('emp_no', $request->identifier)
                        ->where('active', 1)
                        ->first();

        // if (!$user || !Auth::attempt($request->only('emp_no', 'password'))) {
        //     return response()->json(['message' => 'Invalid credentials or inactive user'], 401);
        // }

        return response()->json([
            'user' => $user
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function supervisor(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = \App\Models\User::findOrFail($request->id);

        // Check user's direct supervisor
        $supervisor = $user->supervisor;

        // If not set, check department's supervisor
        if (!$supervisor) {
            $supervisor = $user->department ? $user->department->supervisor : null;
        }

        return response()->json($supervisor);
    }

    public function staffs(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $supervisorId = $request->id;

        // Users directly supervised and active
        $directStaffs = \App\Models\User::where('supervisor_id', $supervisorId)
            ->where('active', 1);

        // Users whose department supervisor matches and active
        $departmentStaffs = \App\Models\User::whereHas('department', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        })->where('active', 1);

        // Merge both queries
        $staffs = $directStaffs->union($departmentStaffs)->get();

        return response()->json($staffs);
    }

    public function department(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = \App\Models\User::findOrFail($request->id);

        return response()->json($user->department);
    }

    public function active()
    {
        $users = \App\Models\User::active()->get();
        return response()->json($users);
    }

    public function roles(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = \App\Models\User::findOrFail($request->id);

        // Assuming roles() returns a collection of Role models with a 'name' attribute
        $roleNames = $user->roles->pluck('name');

        return response()->json($roleNames);
    }


}
