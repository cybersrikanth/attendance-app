<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Helpers\ResponseHelper;
use App\Leave;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function PHPSTORM_META\type;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaves = Leave::all()->toArray();
        return ResponseHelper::response()
            ->message("leaves")
            ->data($leaves)
            ->send(200);
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
        // dd($request->input());
        $validatedData = $this->validate($request, [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'required|min:5|max:1000',
            'attachments' => 'max:2',
            'attachments.*' => 'mimes:jpeg,png,pdf|max:100'
        ]);
        $validatedData["student_id"] = $userId;
        // dd($validatedData);
        $files = $validatedData["attachments"];

        $newLeave = Leave::create($validatedData);
        $paths = [];
        foreach ($files as $file) {
            $path = Storage::disk('s3')->put('attachments', $file);
            Attachment::create([
                "leave_id" => $newLeave["id"],
                "url" => $path
            ]);
            array_push($paths, $path);
        }

        return ResponseHelper::response()->message("created")->data($newLeave->toArray())->send(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        $attachments = $leave->attachments;
        for ($i = 0; $i < count($attachments); $i++) {
            $attachments[$i]["url"] = Storage::disk('s3')->temporaryUrl($attachments[$i]["url"], now()->addMinutes(1));
        }
        $leave['attachments'] = $attachments;
        return ResponseHelper::response()
            ->message("Leave")
            ->data($leave)
            ->send(200);
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
