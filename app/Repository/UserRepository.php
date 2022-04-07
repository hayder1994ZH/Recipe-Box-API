<?php

namespace App\Repository;

use App\Models\Rules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Spatie\QueryBuilder\QueryBuilder;

//                        <----------- Welcome To UserRepository Page ----------->

class UserRepository extends BaseRepository
{
    //Repo for Login 
    public function authenticate($request)
    {
        $user = User::where('username', $request['username'])
            ->where('is_deleted', 0)
            ->with('rules')
            ->first();
            if(!$user){
                return ['error' =>  'invalid_credentials', 'code' => 401];
            }

        if (!Hash::check($request['password'], $user->password)) { //check password
            return ['error' => 'The password is invalid', 'code' => 401];
        }

        $active = User::where('username', $request['username'])->where('is_deleted', 0)->first();

        if (!$active) { //check user active
            return ['error' => 'This user not active', 'code' => 400];
        }

        try {
            JWTAuth::factory()->setTTL(60 * 24 * 360 * 20);
            if (!$token = JWTAuth::fromUser($user)) {
                return ['error' =>  'invalid_credentials', 'code' => 401];
            }
        } catch (JWTException $e) {
            return ['error' => 'could_not_create_token', 'code' => 400];
        }
         $baseToken =  auth()->claims([
            'rule_id' => $user->rule_id,
            'rule_name' => $user->rules->name,
         ])->fromUser($user);
        return ['token' => $baseToken, 'code' => 200];
    }
}
