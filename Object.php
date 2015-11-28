<?php

namespace shiwolang\base;
/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/26
 * Time: 17:01
 */
class Object
{
    public static function className()
    {
        return get_called_class();
    }

    private static function camelName($name, $ucfirst = true)
    {
        if (strpos($name, "_") !== false) {
            $name = str_replace("_", " ", strtolower($name));
            $name = ucwords($name);
            $name = str_replace(" ", "", $name);
        }

        return $ucfirst ? ucfirst($name) : $name;
    }

    public function __get($name)
    {
        $name   = self::camelName($name);
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new \Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __set($name, $value)
    {
        $name   = self::camelName($name);
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new \Exception('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    public function __isset($name)
    {
        $name   = self::camelName($name);
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }


    public function __unset($name)
    {
        $name   = self::camelName($name);
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new \Exception('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }


    public function __call($name, $params)
    {

        throw new \Exception('Calling unknown method: ' . get_class($this) . "::$name()");
    }


    public static function __callStatic($name, $arguments)
    {
        throw new \Exception('Calling unknown static method: ' . static::className() . "::$name()");
    }

    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }


    public function canGetProperty($name, $checkVars = true)
    {
        $name = self::camelName($name);

        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name);
    }


    public function canSetProperty($name, $checkVars = true)
    {
        $name = self::camelName($name);

        return method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name);
    }


    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }
}