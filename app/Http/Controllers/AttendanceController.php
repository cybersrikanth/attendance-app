<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Helpers\HolidayHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\TimeHelper;
use App\Leave;
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
        return Attendance::with(["student"])->where([
            ["attendance_for", $date],
            ["teacher_id", $id]
        ])->get()->all();
    }

    public function index(Request $request)
    {
        $userId = $request->user()["id"];
        $alreadyTook = true;
        $date = strtotime($request->input('date'));
        if (TimeHelper::isFuture($date)) {
            throw new HttpException(422, "invalid date");
        }

        $holiday = new HolidayHelper($date);
        $holiday = $holiday->checkHoliday();
        $data = self::getMyAttendance(TimeHelper::formatedDate($date), $userId);

        if (!$data) {
            $alreadyTook = false;
            $data = User::where("role", User::ROLES[0])->get()->all();
            $data = User::toList($data);
            $leaves = Leave::where([
                ["startDate", "<=", TimeHelper::formatedDate($date)],
                ["endDate", ">=", TimeHelper::formatedDate(($date))]
            ])->get()->all();
            $data = Leave::mapUsersToLeave($leaves, $data);
        }
        $data = Attendance::mapStates($data, $userId, TimeHelper::formatedDate($date), true);


        return ResponseHelper::response()
            ->message("students")
            ->data([
                "alreadyTook" => $alreadyTook,
                "holiday" => isset($holiday[0]["name"]) ? $holiday[0]["name"] : false,
                "nameList" => $data
            ])
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
            "data.*.student_id" => "required|exists:users,id",
            "data.*.state" => "required|in:" . implode(",", array_keys(Attendance::STATE)),
            "overrideHoliday" => "required|boolean"
        ]);

        $myAttendance = self::getMyAttendance(TimeHelper::formatedDate($date), $userId);

        if ($myAttendance) {
            throw new HttpException(422, "You already took attendence for the date");
        }
        if (!$validatedRequest["overrideHoliday"]) {
            $holiday = new HolidayHelper($date);
            $holiday = $holiday->checkHoliday();
            if (isset($holiday[0]["name"])) {
                throw new HttpException(422, $holiday[0]["name"] . " is holiday");
            }
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
    public function update(Request $request)
    {
        $userId = $request->user()["id"];
        $validatedRequest = $this->validate($request, [
            "data.*.id" => "required|exists:attendances,id",
            "data.*.state" => "required|in:" . implode(",", Attendance::STATES),
        ]);
        $present = [];
        $absent = [];

        foreach ($validatedRequest["data"] as $record) {
            switch ($record["state"]) {
                case Attendance::STATES[1]:
                    array_push($present, $record["id"]);
                    continue;
                case Attendance::STATES[0]:
                    array_push($absent, $record["id"]);
                    continue;
                default:
                    continue;
            }
        }

        if ($present) {
            Attendance::wherein("id", $present)->update([
                "state" => Attendance::STATE["present"]
            ]);
        }
        if ($absent) {
            Attendance::wherein("id", $absent)->update([
                "state" => Attendance::STATE["absent"]
            ]);
        }
        return ResponseHelper::response()
            ->message("attendance updated")
            ->data(null)
            ->send(200);
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
