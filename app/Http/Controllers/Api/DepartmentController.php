<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * List departments with channels available to the user.
     */

    public function index()
    {
        $departments = Department::withCount(['channels', 'users'])->get();
        return DepartmentResource::collection($departments);
    }

    /**
     * Display department details along with its channels.
     */
    public function show(Department $department)
    {
        $department->load(['channels' =>function($query){
            $query->where('type','public')
            ->orWhereHas('users', fn ($q) => $q->where('user_id', auth()->id));

        }]);

        return new DepartmentResource($department);
    }
}
