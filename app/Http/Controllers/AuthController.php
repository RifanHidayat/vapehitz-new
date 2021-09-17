<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Session as FacadesSession;

class AuthController extends Controller
{
    public function showFormLogin()
    {
        if (Auth::check()) {
            //Login Success
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        Auth::attempt(['username' => $username, 'password' => $password]);

        if (Auth::check()) {
            $request->session()->regenerate();
            $user = User::find(Auth::id());
            $userLoginPermission = [];

            if ($user !== null) {
                $userLoginPermission = json_decode($user->group->permission);
            }
            $request->session()->put('userLoginPermissions', $userLoginPermission);
            return redirect()->intended('home');
            // return response()->json([
            //     'status' => 'OK',
            //     'message' => 'logged on',
            //     'code' => 200
            // ]);
        }
        FacadesSession::flash('error', 'Username atau password salah');
        return redirect()->route('login');
        // } return response()->json([
        //     'status' => 'Oops',
        //     'message' => 'incorrect username or password',
        //     'code' => 400
        // ], 400);
        // $rules = [
        //     'username' => 'required|string',
        //     'password' => 'required|string'
        // ];

        // $messages = [
        //     'username.required' => 'Username wajib diisi',
        //     'username.string' => 'Username tidak valid',
        //     'password.required' => 'Password wajib diisi',
        //     'password.string' => 'Password harus berupa string'
        // ];

        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput($request->all);
        // }

        // $data = [
        //     'username'     => $request->input('username'),
        //     'password'  => $request->input('password'),
        // ];

        // Auth::attempt($data);

        // if (Auth::check()) { // true sekalian session field di users nanti bisa dipanggil via Auth
        //     //Login Success
        //     return redirect()->route('home');
        // } else { // false

        //     //Login Fail
        //     Session::flash('error', 'Username atau password salah');
        //     return redirect()->route('login');
        // }
    }

    public function showFormRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // $rules = [
        //     'name'                  => 'required|min:3|max:35',
        //     'email'                 => 'required|email|unique:users,email',
        //     'password'              => 'required|confirmed'
        // ];

        // $messages = [
        //     'name.required'         => 'Nama Lengkap wajib diisi',
        //     'name.min'              => 'Nama lengkap minimal 3 karakter',
        //     'name.max'              => 'Nama lengkap maksimal 35 karakter',
        //     'email.required'        => 'Email wajib diisi',
        //     'email.email'           => 'Email tidak valid',
        //     'email.unique'          => 'Email sudah terdaftar',
        //     'password.required'     => 'Password wajib diisi',
        //     'password.confirmed'    => 'Password tidak sama dengan konfirmasi password'
        // ];

        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     return redirect()->back()->withErrors($validator)->withInput($request->all);
        // }

        // $user = new User;
        // $user->name = ucwords(strtolower($request->name));
        // $user->username = $request->username;
        // $user->group_id = $request->group;
        // $user->email = strtolower($request->email);
        // $user->password = Hash::make($request->password);
        // $user->email_verified_at = \Carbon\Carbon::now();
        // $simpan = $user->save();

        // if ($simpan) {
        //     Session::flash('success', 'Register berhasil! Silahkan login untuk mengakses data');
        //     return redirect('/user');
        // } else {
        //     Session::flash('errors', ['' => 'Register gagal! Silahkan ulangi beberapa saat lagi']);
        //     return redirect()->route('register');
        // }
    }

    public function logout()
    {
        Auth::logout(); // menghapus session yang aktif
        return redirect()->route('login');
    }
}
