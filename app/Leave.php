<?php

namespace App;

use App\Helpers\ArrayHelper;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'student_id', 'startDate', "endDate", "reason"
    ];

    public static function mapUsersToLeave(array $leaves, array $users)
    {
        foreach ($leaves as $leave) {
            $index = ArrayHelper::indexOf($leave, "student_id", "id", $users);
            $users[$index]["state"] = 0;
        }
        return $users;
    }

    public $timestamps = false;
}
