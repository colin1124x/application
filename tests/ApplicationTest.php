<?php

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testBindMakeString()
    {
        $app = new Rde\Application();

        $concrete = 'ABC';

        $app->bind('x', $concrete);

        $this->assertEquals(
            $concrete,
            $app->make('x')
        );
    }

    public function testBindMakeObject()
    {
        $app = new Rde\Application();

        $concrete = new stdClass();
        $concrete->{'test'} = time();

        $app->bind('x', $concrete);

        $this->assertEquals(
            $concrete,
            $app->make('x')
        );
    }

    public function testBindMakeClosure()
    {
        $tester = $this;
        $app = new Rde\Application();

        $concrete = function($c) use($tester, $app) {

            $tester->assertEquals($c, $app);

            $o = new stdClass();
            $o->{'test'} = 123456;

            return $o;
        };

        $app->bind('x', $concrete);

        $make_concrete = $app->make('x');

        $this->assertInstanceOf(
            'stdClass',
            $make_concrete
        );

        $this->assertEquals(
            123456,
            $make_concrete->{'test'}
        );
    }
}
