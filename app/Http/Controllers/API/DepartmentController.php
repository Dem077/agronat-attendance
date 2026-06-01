<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function active(): JsonResponse
    {
        $departments = Department::orderBy('name', 'asc')->get();

        return response()->json($departments);
    }
}
