<?php

namespace Tests\Unit;

use App\Attendance;
use App\Helpers\DocHelper;
use App\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    private $attendance_structure = [
        "data" => [
            "holiday",
            "alreadyTook",
            "nameList" => [
                ["student_id", "state"]
            ]
        ]
    ];

    private function useAuth(int $role): object
    {
        $user = factory(User::class)->create(["role" => User::ROLES[$role]]);
        $this->actingAs($user, "api");
        $this->setRequestHeader("Authorization", "Bearer {{Token}}");
        return $this;
    }

    public function testAttendanceGet()
    {
        factory(Attendance::class, 5)->create(["state" => rand(0, 1)]);
        $this->request["uri"] = "/api/attendance?date=" . date("Y-m-d");
        $response = $this->useAuth(1)->fire();
        $response->assertJsonStructure($this->attendance_structure);
        DocHelper::make(__FUNCTION__, $this->request, $response);
    }

    public function testAttendanceGetValidation()
    {
        $attendances = factory(Attendance::class, 5)->create(["state" => rand(0, 1)]);
        $this->request["uri"] = "/api/attendance?date=" . date("2100-01-01");
        $response = $this->useAuth(1)->fire();
        $response->assertStatus(422)->assertJsonStructure($this->error_structure);
    }

    public function testAttendancePost()
    {
        $attendances = factory(Attendance::class, 5)->make()->toArray();
        $this->request["uri"] = "/api/attendance?date=" . date("Y-m-d");
        $this->request["method"] = "POST";
        $this->request["body"] = [
            "data" => $attendances,
            "overrideHoliday" => false
        ];
        $response = $this->useAuth(1)->fire();
        $response->assertStatus(201);
        DocHelper::make(__FUNCTION__, $this->request, $response);
    }

    public function testAttendancePostValidation()
    {
        $attendances = factory(Attendance::class, 5)->make()->toArray();
        $this->request["uri"] = "/api/attendance?date=" . date("Y-m-d");
        $this->request["method"] = "POST";
        $this->request["body"] = [
            "data" => $attendances,
            "overrideHoliday" => false
        ];
        $response = $this->useAuth(0)->fire();
        $response->assertStatus(401);
    }

    public function testAttendanceUpdate()
    {
        $attendances = factory(Attendance::class, 5)->create(["state" => 1])->toArray();
        $attendances = array_map(function ($record) {
            return [
                "id" => $record["id"],
                "state" => Attendance::STATES[rand(0, 1)]
            ];
        }, $attendances);
        $this->request["uri"] = "/api/attendance";
        $this->request["method"] = "PATCH";
        $this->request["body"] = [
            "data" => $attendances,
        ];
        $response = $this->useAuth(1)->fire();
        $response->assertStatus(200);
        DocHelper::make(__FUNCTION__, $this->request, $response);
    }

    public function testAttendanceUpdateValidation()
    {
        $attendances = factory(Attendance::class, 5)->create(["state" => 1])->toArray();
        $attendances = array_map(function ($record) {
            return [
                "id" => $record["id"],
                "state" => rand(0, 2)
            ];
        }, $attendances);
        $this->request["uri"] = "/api/attendance";
        $this->request["method"] = "PATCH";
        $this->request["body"] = [
            "data" => $attendances,
        ];
        $response = $this->useAuth(1)->fire();
        $response->assertStatus(422);
    }
}
