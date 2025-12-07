<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials or inactive user'], 401);
        }

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

    public function bydepartment(Request $request)
    {
        $request->validate([
            'dep_id' => 'required',
        ]);

        $department = \App\Models\Department::findOrFail($request->dep_id);

        return response()->json($department);
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




    //RADIUS SERVER APIS

    public function authenticate(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'challenge' => 'required|size:16',
            'response' => 'required|size:49'
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('nid', $request->identifier)
            ->orWhere('emp_no', $request->identifier)
            ->where('active', 1)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        // Validate MS-CHAPv2 response
        if ($this->validateMSChapv2Response($user->password, $request->challenge, $request->response)) {
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Authentication successful'
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    private function validateMSChapv2Response($passwordHash, $challenge, $response)
    {
        // Decode hex-encoded challenge and response
        $challenge = hex2bin($challenge);
        $response = hex2bin($response);

        // Extract components from response (49 bytes)
        $reserved = substr($response, 0, 1);
        $reserved2 = substr($response, 1, 1);
        $response_flags = ord(substr($response, 2, 1));
        $peer_challenge = substr($response, 3, 16);
        $reserved3 = substr($response, 19, 8);
        $nt_response = substr($response, 27, 24);

        // Generate NT hash from password
        $nt_hash = hash('md4', mb_convert_encoding($passwordHash, 'UTF-16LE', 'UTF-8'), true);

        // Create session key
        $challenge_response = hash_hmac('md5', $challenge . $peer_challenge, $nt_hash, true);

        // Validate NT response
        return $this->validateNTResponse($nt_response, $nt_hash, $challenge_response);
    }

    private function validateNTResponse($nt_response, $nt_hash, $challenge_response)
    {
        // This is a simplified validation
        // In production, implement full MS-CHAPv2 validation according to RFC 2759
        return hash_equals(
            $nt_response,
            hash_hmac('md4', $challenge_response, $nt_hash, true)
        );
    }


}
