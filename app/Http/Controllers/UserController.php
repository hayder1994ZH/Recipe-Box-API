<?php

namespace App\Http\Controllers;


use JWTAuth;
use App\Models\User;
use App\Helpers\Utilities;
use Illuminate\Http\Request;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $UserRepository;
    private $auth;
    public function __construct()
    {
        $this->UserRepository = new UserRepository(new User());
        $this->middleware('role:admin', ['only' => ['index', 'update', 'store', 'destroy']]);
        $this->auth = Utilities::auth();
    }

    public function index(Request $request)
    {
        $request->validate([
            'skip' => 'Integer',
            'take' => 'required|Integer'
        ]);
        $relations = ['rules'];
        $filter = ['full_name', 'username', 'phone'];
        $take = $request->take;
        $skip = $request->skip;
        return $this->UserRepository->getList($skip, $take, $relations, $filter);
    }

    public function show($id) // Anyone
    {
        return $this->UserRepository->getById($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6',
            'rule_id' => 'required|integer|exists:rules,id',
        ]);
        
        //Processing
        $data['password'] = Hash::make($data['password']);
        if ($request->hasfile('image')) { //check image
            $data['image'] = Utilities::uploadImage($request->file('image'));            
        }

        //Response
        $response =  $this->UserRepository->create($data);
        return Utilities::wrap($response, 200);

    }

    public function update(Request $request, $id)
    {
        $this->UserRepository->getById($id);
        $data = $request->validate([//Validation
            'full_name' => 'string',
            'username' => 'string|unique:users,username,'.$id,
            'phone' => 'string',
            'rule_id' => 'integer|exists:rules,id',
            'password' => 'string|min:6',
        ]);
        if (array_key_exists("password", $request->all())) {
            if (!is_null($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
        }
        if ($request->hasfile('image')) { //check image
            $data['image'] = Utilities::uploadImage($request->file('image'));            
        }
        $response = $this->UserRepository->update($id, $data);
        return Utilities::wrap($response, 200);
    }
    public function destroy($id)
    {
        $model = $this->UserRepository->getById($id);
        return $this->UserRepository->delete($model);
    }

    // ======================================  auth functions  =========================================//
    public function register(Request $request) // Anyone
    {
        $data = $request->validate([//Validation
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6',
            'rule_id' => 'integer|exists:rules,id',
        ]);
       
        //Processing
        $data['password'] = Hash::make($data['password']);
        $data['rule_id'] = 1;

        //Response
        $response = $this->UserRepository->create($data);
        return Utilities::wrap($response, 200);

    }

    //login
    public function login(Request $request) // Anyone
    {
        $valiation = $request->validate([//Validation
            'username' => 'required',
            'password' => 'required|min:6',
        ]);
        
        //Processing
        $response = $this->UserRepository->authenticate($valiation);

        // Response
        return ($response['code'] == 200)? Utilities::wrap(['token' => $response['token']], 200):
        Utilities::wrap(['error' => $response['error']], $response['code']);
    }

    public function logout() // Anyone
    {
        //Response
        return auth()->logout();
    }

    public function me() // Anyone
    {
        //Response
        return auth()->user()->load('rules');
    }

    public function updateProfile(Request $request) // Anyone
    {
        $data = $request->validate([
            'full_name' => 'string',
            'username' => 'string|unique:users,username,'.$this->auth->id,
            'phone' => 'string',
            'password' => 'string|min:6',
            'image' => 'file'
        ]);
        
        //Processing
        if (array_key_exists("password", $request->all())) {
            $data['password'] = Hash::make($data['password']);
        }
        if ($request->hasfile('image')) { //check image
            $data['image'] = Utilities::uploadImage($request->file('image'));            
        }

        //Response
        $response = auth()->user()->update($data);
        return Utilities::wrap($response, 200);
    }
}
