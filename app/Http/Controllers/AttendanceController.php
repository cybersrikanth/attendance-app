<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Helpers\ResponseHelper;
use App\Helpers\TimeHelper;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static function getMyAttendance($date, $id)
    {
        return Attendance::where([
            ["attendance_for", $date],
            ["teacher_id", $id]
        ])->get()->all();
    }

    public function index(Request $request)
    {
        $userId = $request->user()["id"];
        $date = strtotime($request->input('date'));
        if (TimeHelper::isFuture($date)) {
            throw new HttpException(422, "invalid date");
        }

        $data = self::getMyAttendance(TimeHelper::formatedDate($date),$userId );

        if (!$data) {
            $data = User::where("role", User::ROLES[0])->get()->all();
            $data = User::toList($data);
            $data = Attendance::mapStates($data, $userId, TimeHelper::formatedDate($date), true);
        }


        return ResponseHelper::response()
            ->message("students")
            ->data($data)
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
        $date = strtotime($request->input('date'));
        if (TimeHelper::isFuture($date)) {
            throw new HttpException(422, "invalid date");
        }
        $userId = $request->user()["id"];
        $validatedRequest = $this->validate($request, [
            "data.*.id" => "required|exists:users,id",
            "data.*.state" => "required|in:" . implode(",", array_keys(Attendance::STATE)),

        ]);

        $myAttendance = self::getMyAttendance(TimeHelper::formatedDate($date), $userId);

        if ($myAttendance) {
            throw new HttpException(422, "You already took attendence for the date");
        }

        $newAttendance = Attendance::mapStates($validatedRequest["data"], $userId, TimeHelper::formatedDate($date));



        $newAttendance = Attendance::insert($newAttendance);
        return ResponseHelper::response()
            ->message("attendance posted")
            ->data($newAttendance)
            ->send(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
