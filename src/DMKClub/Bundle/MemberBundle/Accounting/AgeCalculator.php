<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;

use DateTime;

class AgeCalculator
{

    public function getAgeInMonth(DateTime $currentMonth, DateTime $birthday, $raiseOnBirthday): int
    {
        if ($raiseOnBirthday) {
            $birthdayFirst = new DateTime();
            $birthdayFirst->setDate($birthday->format('Y'), $birthday->format('n'), 1);
            $birthdayFirst->setTime(0, 0, 0, 0);
            $birthday = $birthdayFirst;
        }
        $age = $birthday->diff($currentMonth)->y;
        return $age;
    }
}
