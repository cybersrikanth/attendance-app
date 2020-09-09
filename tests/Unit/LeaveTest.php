<?php

namespace Tests\Unit;

use App\Helpers\DocHelper;
use App\Leave;
use App\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    private function useAuth(int $role): object
    {
        $my_role = User::ROLES[$role];
        $user = factory(User::class)->create(["role" => $my_role]);
        $this->actingAs($user, "api");
        $this->setRequestHeader("Authorization", "Bearer {{" . $my_role . " token}}");
        return $this;
    }

    public function testLeaveApplyWithoutAttachment()
    {
        $leave = factory(Leave::class)->make()->toArray();
        $this->request["method"] = "POST";
        $this->request["uri"] = "/api/leave";
//        $this->setRequestHeader("content-type", "multipart/form-data");
        $this->request["body"] = $leave;
        $response = $this->useAuth(0)->fire();
        $response->assertStatus(201);
    }

    public function testLeaveApplyWithAttachment()
    {
        $leave = factory(Leave::class)->make([
            "attachments" => [UploadedFile::fake()->create('document.jpg', 80)]
        ])->toArray();
        $this->request["uri"] = "/api/leave";
        $this->request["method"] = "POST";
        $this->request["body"] = $leave;
        $response = $this->useAuth(0)->fire();
        $response->assertStatus(201);
        DocHelper::make(__FUNCTION__, $this->request, $response);
    }

    public function testLeaveApplyAuthValidation()
    {
        $leave = factory(Leave::class)->make()->toArray();
        $response = $this->useAuth(1)->post("/api/leave", $leave);
        $response->assertStatus(401);
    }

    public function testLeaveApplyAttachmentSizeValidation()
    {
        $leave = factory(Leave::class)->make([
            "attachments" => [UploadedFile::fake()->create('document.jpg', 102)]
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }

    public function testLeaveApplyAttachmentMimeValidation()
    {
        $leave = factory(Leave::class)->make([
            "attachments" => [UploadedFile::fake()->create('document.mp4', 100)]
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }

    public function testLeaveApplyAttachmentCountValidation()
    {
        $leave = factory(Leave::class)->make([
            "attachments" => [
                UploadedFile::fake()->create('document.jpg', 100),
                UploadedFile::fake()->create('document.pdf', 100),
                UploadedFile::fake()->create('document.jpg', 100)
            ]
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }

    public function testLeaveApplyDateValidation()
    {
        $leave = factory(Leave::class)->make([
            "startDate" => date("Y-m-t"),
            "endDate" => date("Y-m-1")
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }

    public function testLeaveApplyReasonLowerValidation()
    {
        $leave = factory(Leave::class)->make([
            "reason" => "hi"
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }

    public function testLeaveApplyReasonUpperValidation()
    {
        $leave = factory(Leave::class)->make([
            "reason" => str_repeat("hello ", 200)
        ])->toArray();
        $response = $this->useAuth(0)->post("/api/leave", $leave);
        $response->assertStatus(422);
    }
}
