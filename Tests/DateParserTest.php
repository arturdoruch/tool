<?php

namespace ArturDoruch\Tool\Tests;

use ArturDoruch\Tool\DateParser;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class DateParserTest extends \PHPUnit\Framework\TestCase
{
    public function getDateData()
    {
        return [
            ['15 mar, 2018', '15-03-2018'],
            ['Jan 23 2017', '23-01-2017'],
        ];
    }

    /**
     * @dataProvider getDateData
     */
    public function testParse($dateString, $expected)
    {
        $date = DateParser::parse($dateString);
        $this->assertEquals($expected, $date->format('d-m-Y'));
    }


    public function getInvalidDateData()
    {
        return [
            ['18 luty 20302'],
        ];
    }

    /**
     * @dataProvider getInvalidDateData
     */
    public function testParseInvalidDate($dateString)
    {
        $date = DateParser::parse($dateString);
        $this->assertNull($date);
    }
}
