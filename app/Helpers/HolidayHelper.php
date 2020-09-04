<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class HolidayHelper
{
    public function __construct($date)
    {
        $this->date = $date;
    }

    public function checkHoliday()
    {
        $year = date('Y', $this->date);
        $holiday = null;
        $response = Http::get('https://holidayapi.com/v1/holidays?pretty&key=cd44bc08-ea79-4267-a4ef-a4b733f65b0d&country=IN&year=' . $year);
        $holidays = $response->json()["holidays"];
        for ($i = 0; $i < count($holidays); $i++) {
            if (strtotime($holidays[$i]["date"]) == $this->date) {
                return $holidays[$i];
            }
        }

        return $holiday;
    }
}
