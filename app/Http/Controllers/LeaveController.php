<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Leave;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Leave::all()->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = $request->user()["id"];
        $validatedData = $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'required|min:5|max:1000'
        ]);
        $validatedData["student_id"] = $userId;
        $newLeave = Leave::create($validatedData);
        return ResponseHelper::response()->message("created")->data($newLeave)->send(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Leave $leave)
    {
        $user = $request->user()["id"];
        if ($leave->student_id != $user) {
            throw new AuthorizationException("You dont have access to delete this resource");
        }
        $leave->delete();
        return ResponseHelper::response()->message("deleted")->data(null)->send(200);
    }
}
