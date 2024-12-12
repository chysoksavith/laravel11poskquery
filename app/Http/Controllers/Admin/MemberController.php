<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        return view('member.list');
    }
    public function getMember()
    {
        $members = Member::all();
        return response()->json($members);
    }
    public function store(Request $request)
    {

        $request->validate([
            'code_member' => 'required|string|max:255',
            'name_member' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'telephone' => 'required|string|regex:/^[0-9]+$/',
        ]);
        $member = new Member();
        $member->create($request->only(['code_member', 'name_member', 'address', 'telephone']));
        return response()->json([
            'success' => 'Member Created',
        ], 200);
    }
    public function edit($id)
    {
        $member = Member::findOrFail($id);
        return response()->json(
            $member
        );
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'code_member' => 'required|string|max:255',
            'name_member' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'telephone' => 'required|string|regex:/^[0-9]+$/',
        ]);
        $member = Member::findOrFail($id);
        $member->update($request->only(['code_member', 'name_member', 'address', 'telephone']));
        return response()->json([
            'success' => 'Member Updated',
        ], 200);
    }
    public function destroy($id){
        $member = Member::findOrFail($id);
        $member->delete();
        return response()->json([
            'success' => 'member delted'
        ]);
    }
}
