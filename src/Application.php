<?php namespace Rde;

class Application implements \ArrayAccess
{
    private $collection = array();

    public function make($abstract, $args = null)
    {
        if (isset($this->collection[$abstract])) {

            $concrete = $this->collection[$abstract];

            return call_user_func($concrete, $this, (array) $args);
        }
    }

    public function bind($abstract, $concrete = null)
    {
        $this->collection[$abstract] = $this->getClosure($concrete, $abstract);
    }

    public function bindShared($abstract, $concrete = null)
    {
        $this->bind($abstract, $this->share($concrete));
    }

    protected function share($concrete)
    {
        return function($c) use ($concrete) {
            static $object;

            if (null === $object) {
                $object = $concrete($c);
            }

            return $object;
        };
    }

    protected function getClosure($concrete, $abstract)
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }

        return ($concrete instanceof \Closure) ?
            $concrete :
            function() use($concrete){
                return $concrete;
            };
    }

    public function offsetExists($k)
    {
        return array_key_exists($k, $this->collection);
    }

    public function offsetGet($k)
    {
        return $this->collection[$k];
    }

    public function offsetSet($k, $v)
    {
        $this->bind($k, $v);
    }

    public function offsetUnset($k)
    {
        unset($this->collection[$k]);
    }
}
