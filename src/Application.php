<?php namespace Rde;

class Application implements \ArrayAccess
{
    private $collection = array();

    public function make($abstract, array $args = array())
    {
        if (isset($this->collection[$abstract])) {

            $concrete = $this->collection[$abstract];

            return call_user_func($concrete, $this, $args);
        }
    }

    public function bind($abstract, $concrete = null)
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }

        if ( ! $concrete instanceof \Closure) {
            $concrete = function() use($concrete){
                return $concrete;
            };
        }

        $this->collection[$abstract] = $concrete;
    }

    public function bindShared($abstract, $concrete = null)
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }

        if ( ! $concrete instanceof \Closure) {
            $concrete = function() use($concrete){
                return $concrete;
            };
        }

        $this->collection[$abstract] = function($c) use($concrete){

            static $object;

            if (null === $object) {
                $object = $concrete($c);
            }

            return $object;
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
