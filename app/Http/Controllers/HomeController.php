<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\Role;
use App\Models\Cita;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->hasRole(Role::ADMIN)) {
            $totalCitas = Cita::where('start', '>=', now())->count();
            $totalUsuarios = User::count();
            
         
    
            return view('admin.dashboard', compact('totalCitas', 'totalUsuarios'));
        }
    


        return view('dashboard');
    }
}
   
