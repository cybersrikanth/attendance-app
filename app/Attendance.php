<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // relations
    public function student()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }

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
                    "id"  => isset($record["student"]) ? $record['id'] : null,
                    "student_id" => isset($record["student"]) ? $record["student"]["id"] : $record["id"],
                    "state" => isset($record["state"]) ? self::STATES[$record["state"]] : null,
                    "name" => isset($record["student"]) ?  $record["student"]["name"] : $record["name"],
                    "email" => isset($record["student"]) ?  $record["student"]["email"] : $record["email"],
                ]);
            }
        } else {
            foreach ($records as $record) {
                array_push($temp, [
                    "student_id" => $record["student_id"],
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
