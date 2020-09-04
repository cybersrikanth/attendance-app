<?php

namespace Tests\Unit;

use App\Attendance;
use App\User;
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
        return $this;
    }

    public function testAttendanceGet()
    {
        factory(Attendance::class, 5)->create(["state" => rand(0, 1)]);
        $response = $this->useAuth(1)->get("/api/attendance?date=" . date("Y-m-d"));
        $response->assertJsonStructure($this->attendance_structure);
    }

    public function testAttendanceGetValidation()
    {
        $attendances = factory(Attendance::class, 5)->create(["state" => rand(0, 1)]);
        $response = $this->useAuth(1)->get("/api/attendance?date=" . date("2100-01-01"));
        $response->assertStatus(422)->assertJsonStructure($this->error_structure);
    }

    public function testAttendancePost()
    {
        $attendances = factory(Attendance::class, 5)->make()->toArray();
        $response = $this->useAuth(1)->post("/api/attendance?date=" . date("Y-m-d"), [
            "data" => $attendances,
            "overrideHoliday" => false
        ]);
        $response->assertStatus(201);
    }

    public function testAttendancePostValidation()
    {
        $attendances = factory(Attendance::class, 5)->make()->toArray();
        $response = $this->useAuth(0)->post("/api/attendance?date=" . date("Y-m-d"), [
            "data" => $attendances,
            "overrideHoliday" => false
        ]);
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
        $response = $this->useAuth(1)->patch("/api/attendance", [
            "data" => $attendances,
        ]);
        $response->assertStatus(200);
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
        $response = $this->useAuth(1)->patch("/api/attendance", [
            "data" => $attendances,
        ]);
        $response->assertStatus(422);
    }
}
