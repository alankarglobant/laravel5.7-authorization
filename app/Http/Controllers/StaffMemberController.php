<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Role;
use App\UserRole;
use App\Http\Requests\CreateStaffRequest;
use Illuminate\Support\Facades\Hash;

class StaffMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('index', User::class);

        $staffMembers = User::where('added_by','=',Auth::id())->get();
        return view('staff.index',['members' => $staffMembers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CreateStaffRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStaffRequest $request)
    {
        $this->authorize('create', User::class);

        $data =  $request->all();
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('123456'),
            'added_by' => Auth::id()
        ]);

        $registerAs = 'Staff';
        $role = Role::where('name','=',$registerAs)->first();
        $userRole =  new UserRole(['role_id' => $role->id]);
        $user->userRole()->save($userRole);

        return redirect(route('staff.index'))->with('status','Staff member has been added successfully !');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('create', User::class);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('create', User::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
