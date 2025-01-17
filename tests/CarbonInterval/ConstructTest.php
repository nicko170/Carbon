<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\CarbonInterval;

use BadMethodCallException;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\Exceptions\OutOfRangeException;
use DateInterval;
use Exception;
use Tests\AbstractTestCase;

class ConstructTest extends AbstractTestCase
{
    public function testInheritedConstruct()
    {
        CarbonInterval::createFromDateString('1 hour');
        $ci = new CarbonInterval('PT0S');
        $this->assertSame('PT0S', $ci->spec());
        $ci = new CarbonInterval('P1Y2M3D');
        $this->assertSame('P1Y2M3D', $ci->spec());
        $ci = CarbonInterval::create('PT0S');
        $this->assertSame('PT0S', $ci->spec());
        $ci = CarbonInterval::create('P1Y2M3D');
        $this->assertSame('P1Y2M3D', $ci->spec());
        $ci = CarbonInterval::create('PT9.5H+85M');
        $this->assertSame('PT9H115M', $ci->spec());
        $ci = CarbonInterval::create('PT9H+85M');
        $this->assertSame('PT9H85M', $ci->spec());
        $ci = CarbonInterval::create('PT1999999999999.5H+85M');
        $this->assertSame('PT1999999999999H115M', $ci->spec());
    }

    public function testConstructWithDateInterval()
    {
        $ci = new CarbonInterval(new DateInterval('P1Y2M3D'));
        $this->assertSame('P1Y2M3D', $ci->spec());
        $interval = new DateInterval('P1Y2M3D');
        $interval->m = -6;
        $interval->invert = 1;
        $ci = new CarbonInterval($interval);
        $this->assertSame(1, $ci->y);
        $this->assertSame(-6, $ci->m);
        $this->assertSame(3, $ci->d);
        $this->assertSame(1, $ci->invert);
    }

    public function testDefaults()
    {
        $ci = new CarbonInterval();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 1, 0, 0, 0, 0, 0);
    }

    public function testNulls()
    {
        $ci = new CarbonInterval(null, null, null, null, null, null);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
        $ci = CarbonInterval::days(null);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
    }

    public function testZeroes()
    {
        $ci = new CarbonInterval(0, 0, 0, 0, 0, 0);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);

        $ci = CarbonInterval::days(0);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
    }

    public function testZeroesChained()
    {
        $ci = CarbonInterval::days(0)->week(0)->minutes(0);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
    }

    public function testYears()
    {
        $ci = new CarbonInterval(1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 1, 0, 0, 0, 0, 0);

        $ci = CarbonInterval::years(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 2, 0, 0, 0, 0, 0);

        $ci = CarbonInterval::year();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 1, 0, 0, 0, 0, 0);

        $ci = CarbonInterval::year(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 3, 0, 0, 0, 0, 0);
    }

    public function testMonths()
    {
        $ci = new CarbonInterval(0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 1, 0, 0, 0, 0);

        $ci = CarbonInterval::months(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 2, 0, 0, 0, 0);

        $ci = CarbonInterval::month();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 1, 0, 0, 0, 0);

        $ci = CarbonInterval::month(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 3, 0, 0, 0, 0);
    }

    public function testWeeks()
    {
        $ci = new CarbonInterval(0, 0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 7, 0, 0, 0);

        $ci = CarbonInterval::weeks(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 14, 0, 0, 0);

        $ci = CarbonInterval::week();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 7, 0, 0, 0);

        $ci = CarbonInterval::week(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 21, 0, 0, 0);
    }

    public function testDays()
    {
        $ci = new CarbonInterval(0, 0, 0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 1, 0, 0, 0);

        $ci = CarbonInterval::days(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 2, 0, 0, 0);

        $ci = CarbonInterval::dayz(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 2, 0, 0, 0);

        $ci = CarbonInterval::day();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 1, 0, 0, 0);

        $ci = CarbonInterval::day(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 3, 0, 0, 0);
    }

    public function testHours()
    {
        $ci = new CarbonInterval(0, 0, 0, 0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 1, 0, 0);

        $ci = CarbonInterval::hours(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 2, 0, 0);

        $ci = CarbonInterval::hour();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 1, 0, 0);

        $ci = CarbonInterval::hour(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 3, 0, 0);
    }

    public function testMinutes()
    {
        $ci = new CarbonInterval(0, 0, 0, 0, 0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 1, 0);

        $ci = CarbonInterval::minutes(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 2, 0);

        $ci = CarbonInterval::minute();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 1, 0);

        $ci = CarbonInterval::minute(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 3, 0);
    }

    public function testSeconds()
    {
        $ci = new CarbonInterval(0, 0, 0, 0, 0, 0, 1);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 1);

        $ci = CarbonInterval::seconds(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 2);

        $ci = CarbonInterval::second();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 1);

        $ci = CarbonInterval::second(3);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 3);
    }

    public function testMilliseconds()
    {
        $ci = CarbonInterval::milliseconds(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
        $this->assertSame(2, $ci->milliseconds);

        $ci = CarbonInterval::millisecond();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
        $this->assertSame(1, $ci->milliseconds);
    }

    public function testMicroseconds()
    {
        $ci = CarbonInterval::microseconds(2);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
        $this->assertSame(2, $ci->microseconds);

        $ci = CarbonInterval::microsecond();
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 0, 0, 0, 0, 0, 0);
        $this->assertSame(1, $ci->microseconds);
    }

    public function testYearsAndHours()
    {
        $ci = new CarbonInterval(5, 0, 0, 0, 3, 0, 0);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 5, 0, 0, 3, 0, 0);
    }

    public function testAll()
    {
        $ci = new CarbonInterval(5, 6, 2, 5, 9, 10, 11);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 5, 6, 19, 9, 10, 11);
    }

    public function testAllWithCreate()
    {
        $ci = CarbonInterval::create(5, 6, 2, 5, 9, 10, 11);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 5, 6, 19, 9, 10, 11);
    }

    public function testInstance()
    {
        $ci = CarbonInterval::instance(new DateInterval('P2Y1M5DT22H33M44S'));
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 2, 1, 5, 22, 33, 44);
        $this->assertFalse($ci->days);
    }

    public function testInstanceWithNegativeDateInterval()
    {
        $di = new DateInterval('P2Y1M5DT22H33M44S');
        $di->invert = 1;
        $ci = CarbonInterval::instance($di);
        $this->assertInstanceOfCarbonInterval($ci);
        $this->assertCarbonInterval($ci, 2, 1, 5, 22, 33, 44);
        $this->assertFalse($ci->days);
        $this->assertSame(1, $ci->invert);
    }

    public function testInstanceWithDays()
    {
        $ci = CarbonInterval::instance(Carbon::now()->diff(Carbon::now()->addWeeks(3)));
        $this->assertCarbonInterval($ci, 0, 0, 21, 0, 0, 0);
    }

    public function testCopy()
    {
        $one = CarbonInterval::days(10);
        $two = $one->hours(6)->copy()->hours(3);
        $this->assertCarbonInterval($one, 0, 0, 10, 6, 0, 0);
        $this->assertCarbonInterval($two, 0, 0, 10, 3, 0, 0);
    }

    public function testMake()
    {
        $this->assertCarbonInterval(CarbonInterval::make(3, 'hours'), 0, 0, 0, 3, 0, 0);
        $this->assertCarbonInterval(CarbonInterval::make('3 hours 30 m'), 0, 0, 0, 3, 30, 0);
        $this->assertCarbonInterval(CarbonInterval::make('PT5H'), 0, 0, 0, 5, 0, 0);
        $this->assertCarbonInterval(CarbonInterval::make(new DateInterval('P1D')), 0, 0, 1, 0, 0, 0);
        $this->assertCarbonInterval(CarbonInterval::make(new CarbonInterval('P2M')), 0, 2, 0, 0, 0, 0);
        $this->assertNull(CarbonInterval::make(3));

        $this->assertSame(3, CarbonInterval::make('3 milliseconds')->totalMilliseconds);
        $this->assertSame(3, CarbonInterval::make('3 microseconds')->totalMicroseconds);
        $this->assertSame(21, CarbonInterval::make('3 weeks')->totalDays);
        $this->assertSame(9, CarbonInterval::make('3 quarters')->totalMonths);
        $this->assertSame(30, CarbonInterval::make('3 decades')->totalYears);
        $this->assertSame(300, CarbonInterval::make('3 centuries')->totalYears);
        $this->assertSame(3000, CarbonInterval::make('3 millennia')->totalYears);
    }

    public function testBadFormats()
    {
        $this->expectExceptionObject(new Exception('PT1999999999999.5.5H+85M'));

        CarbonInterval::create('PT1999999999999.5.5H+85M');
    }

    public function testOutOfRange()
    {
        $this->expectExceptionObject(new OutOfRangeException(
            'hour',
            -0x7fffffffffffffff,
            0x7fffffffffffffff,
            999999999999999999999999
        ));

        CarbonInterval::create('PT999999999999999999999999H');
    }

    public function testCallInvalidStaticMethod()
    {
        $this->expectExceptionObject(new BadMethodCallException(
            'Unknown fluent constructor \'anything\''
        ));

        CarbonInterval::anything();
    }
}
