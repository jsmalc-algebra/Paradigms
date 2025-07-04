<?php

class Singleton
{
    private static $instances = [];

    protected function __construct(){}

    protected function __clone(){}

    /**
     * @throws Exception
     */
    public function __wakeup(){
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(){
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}