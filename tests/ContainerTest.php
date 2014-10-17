<?php

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testBindMakeString()
    {
        $app = new Rde\Container();

        $concrete = 'ABC';

        $app->bind('x', $concrete);

        $this->assertEquals(
            $concrete,
            $app->make('x')
        );
    }

    public function testBindMakeWithoutConcrete()
    {
        $app = new Rde\Container();

        $app->bind('x');

        $this->assertEquals(
            'x',
            $app->make('x')
        );
    }

    public function testBindMakeObject()
    {
        $app = new Rde\Container();

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
        $app = new Rde\Container();

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

    public function testMakeArguments()
    {
        $app = new \Rde\Container();

        $app->bind('x', function($c, $args){
                $o = new stdClass();
                $o->{'args'} = $args;

                return $o;
            });

        $o1 =  $app->make('x', array(1, 2, 3));
        $o2 =  $app->make('x', array(4, 5, 6));

        $this->assertEquals(
            array(1, 2, 3),
            $o1->{'args'},
            '檢查建構參數1,2,3');

        $this->assertEquals(
            array(4, 5, 6),
            $o2->{'args'},
            '檢查建構參數4,5,6');

        $this->assertEquals(
            array(1, 2, 3, 4, 5, 6),
            array_merge($o1->{'args'}, $o2->{'args'}),
            '檢查兩個物件實體參數');
    }

    public function testArrayAccess()
    {
        $tester = $this;
        $app = new \Rde\Container();
        $app['x'] = function($c) use($tester, $app){
            $tester->assertEquals($app, $c, '檢查IoC容器傳遞');

            $o = new stdClass();
            $o->{'test'} = 654321;

            return $o;
        };

        $this->assertInstanceOf(
            'stdClass',
            $app['x'],
            '測試用ArrayAccess取得'
        );

        $this->assertEquals(
            654321,
            $app['x']->{'test'},
            '檢查物件屬性'
        );
    }

    public function testBindShare()
    {
        $tester = $this;
        $app = new \Rde\Container();

        $app->bindShared('x', function($c, $args = null) use($tester, $app){
                $tester->assertEquals($c, $app, '檢查Container本身有被傳入');
                $tester->assertNull($args, '檢查bindShared不該傳入建構參數');

                $o = new stdClass();
                $o->{'args'} = $args;

                return $o;
            });

        $this->assertEquals(
            $app->make('x', 2),
            $app->make('x', 1),
            '檢查bindShared');
    }
}
