<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Helpers\ResponseHelper;
use App\User;

class AdminController extends Controller
{

    public static function top5($record)
    {
        return [
            "name" => $record["student"]["name"],
            "email" => $record['student']['email'],
        ];
    }

    public function explore()
    {
        $result = [
            "todays_percentage" => null,
            "monthly_percentage" => null,
            "yearly_percentage" => null,
            "top_5_present" => null,
            "top_5_absent" => null
        ];
        $students = User::where("role", User::ROLES[0]);

        $today_attendance = Attendance::where([
            ["attendance_for", "=", date("Y-m-d")]
        ]);
        $todays_total = $today_attendance->get()->count();
        $todays_present = $today_attendance->where("state", "=", 1)->get()->count();
        $result["todays_percentage"] = $todays_present / $todays_total * 100;

        $monthly_attendance = Attendance::where([
            ["attendance_for", ">=", date('Y-m-1')],
            ["attendance_for", "<=", date('Y-m-t')],
        ]);
        $monthly_total = $monthly_attendance->get()->count();
        $monthly_present = $monthly_attendance->where("state", "=", 1)->get()->count();
        $result["monthly_percentage"] = $monthly_present / $monthly_total * 100;

        $yearly_attendance = Attendance::where([
            ["attendance_for", ">=", date('Y-1-1')]
        ]);
        $yearly_total = $yearly_attendance->get()->count();
        $yearly_present = $yearly_attendance->where("state", "=", 1)->get()->count();
        $result["yearly_percentage"] = $yearly_present / $yearly_total * 100;

        $top_5_present = Attendance::where("state", "=", 1)->with("student")->select("student_id")->groupBy("student_id")->orderByRaw('COUNT(*) DESC')->limit(5)->get()->all();
        $top_5_absent = Attendance::where("state", "=", 0)->with("student")->select("student_id")->groupBy("student_id")->orderByRaw('COUNT(*) DESC')->limit(5)->get()->all();
        $result["top_5_present"] = array_map("self::top5", $top_5_present);
        $result["top_5_absent"] = array_map("self::top5", $top_5_absent);
        return ResponseHelper::response()
            ->message("Todays percentage")
            ->data($result)
            ->send(200);
    }
}
