<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class HolidayHelper
{
    public function __construct($date)
    {
        $this->date = $date;
    }

    public function findDate($holiday)
    {
        return strtotime($holiday["date"]) == $this->date;
    }
    public function checkHoliday()
    {
        $year = date('Y', $this->date);

        $response = Http::get('https://holidayapi.com/v1/holidays?pretty&key=cd44bc08-ea79-4267-a4ef-a4b733f65b0d&country=IN&year=' . $year);
        return array_filter($response->json()["holidays"], "self::findDate");
    }
}
