<?php
namespace tests\codeception\unit\models;
use schmunk42\giiant\helpers\GiiantFaker;
use Codeception\Specify;

class GiiantFakerTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testCreateGiiantFaker()
    {
        $faker = GiiantFaker::create();
        $this->assertNotEmpty($faker);

        $this->specify('GiiantFaker object is created', function () use ($faker) {
            expect('GiiantFaker object was not created', $faker)->notEmpty();
        });
    }

    public function testBasicValueNotEmpty()
    {
        $value = GiiantFaker::value();
        $this->specify('value method returns correct value', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
        });
    }

    public function testValueWithStringType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_STRING);
        $this->specify('returned value is string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
        });
    }

    public function testValueWithIntegerType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_INTEGER);
        $this->specify('returned value is integer', function () use ($value) {
            expect('value is integer', is_int($value))->true();
        });
    }

    public function testValueWithFloatType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_NUMBER);
        $this->specify('returned value is float', function () use ($value) {
            expect('value is float', is_float($value))->true();
        });
    }

    public function testValueWithBooleanType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_BOOLEAN);
        $this->specify('returned value is boolean', function () use ($value) {
            expect('value is boolean', is_bool($value))->true();
        });
    }


    public function testValueWithDateType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_DATE);
        $this->specify('returned value is string containing date', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_DATE, $value);
        });
    }

    public function testValueWithDatetimeType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_DATETIME);
        $this->specify('returned value is string containing datetime', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_DATETIME, $value);
        });
    }

    public function testValueWithTimeType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_TIME);
        $this->specify('returned value is string containing time', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_TIME, $value);
        });
    }

    public function testValueWithTimestampType()
    {
        $value = GiiantFaker::value(GiiantFaker::TYPE_TIMESTAMP);
        $this->specify('returned value is string containing timestamp', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_TIMESTAMP, $value);
        });
    }

    public function testStringMethod()
    {
        $value = GiiantFaker::string();
        $this->specify('returned value is string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
        });
    }

    public function testStringMethodMatchingFakersMethodEmail()
    {
        $value = GiiantFaker::string('email');
        $this->specify('returned value is email as string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            expect('value is email', filter_var($value, FILTER_VALIDATE_EMAIL) !== false)->true();
        });
    }

    public function testIntegerMethod()
    {
        $value = GiiantFaker::integer();
        $this->specify('returned value is integer', function () use ($value) {
            expect('value is integer', is_integer($value))->true();
        });
    }

    public function testNumberMethod()
    {
        $value = GiiantFaker::number();
        $this->specify('returned value is float', function () use ($value) {
            expect('value is float', is_float($value))->true();
        });
    }

    public function testDateMethod()
    {
        $value = GiiantFaker::date();
        $this->specify('returned value is date as string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is integer', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_DATE, $value);
        });
    }

    public function testTimeMethod()
    {
        $value = GiiantFaker::time();
        $this->specify('returned value is string as time', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            \DateTime::createFromFormat(GiiantFaker::FORMAT_TIME, $value);
        });
    }

    public function testBooleanMethod()
    {
        $value = GiiantFaker::boolean();
        $this->specify('returned value is boolean', function () use ($value) {
            expect('value is boolean', is_bool($value))->true();
        });
    }

    public function testProviderMethodURL()
    {
        $value = GiiantFaker::string('url');
        $this->specify('returned value is url as string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            expect('value is url', filter_var($value, FILTER_VALIDATE_URL) !== false)->true();
        });
    }

    public function testProviderMethodIPAddress()
    {
        $value = GiiantFaker::string('ipv4');
        $this->specify('returned value is valid IP address as string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            expect('value is valid IP address', filter_var($value, FILTER_VALIDATE_IP) !== false)->true();
        });
    }

    public function testProviderMethodMACAddress()
    {
        $value = GiiantFaker::string('macAddress');
        $this->specify('returned value is MAC address as string', function () use ($value) {
            expect('value is not empty', $value)->notEmpty();
            expect('value is string', is_string($value))->true();
            expect('value is valid MAC address', filter_var($value, FILTER_VALIDATE_MAC) !== false)->true();
        });
    }
}
