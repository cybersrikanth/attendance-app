<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'teacher_id', 'state', "attendance_for"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];


    const STATE = [
        "present" => 1, "absent" => 0
    ];
    const STATES = ["absent", "present"];

    public static function mapStates(array $records, $teacher_id, $date,  $reverse = false)
    {
        $temp = [];
        if ($reverse) {
            foreach ($records as $record) {
                array_push($temp, [
                    "student_id" => $record["id"],
                    "state" => array_key_exists("state", $records) ? self::STATES[$record["state"]] : null,
                    "name" => $record["name"],
                    "email" => $record["email"]
                ]);
            }
        } else {
            foreach ($records as $record) {
                array_push($temp, [
                    "student_id" => $record["id"],
                    "teacher_id" => $teacher_id,
                    "state" => self::STATE[$record["state"]],
                    "attendance_for" => $date
                ]);
            }
        }
        return $temp;
    }
    public static function mapStateReverse($record)
    {

        return [
            "student_id" => $record["id"],
            "state" => self::STATES[$record["state"]]
        ];
    }

    protected static function mapState($record)
    {
        return [
            "student_id" => $record["id"],
            "state" => self::STATE[$record["state"]]
        ];
    }
}
