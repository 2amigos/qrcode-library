<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Format;

use Da\QrCode\Contracts\FormatInterface;
use Da\QrCode\Exception\InvalidCallException;
use Da\QrCode\Exception\UnknownMethodException;
use Da\QrCode\Exception\UnknownPropertyException;

/**
 * Abstract Class FormatAbstract for all formats
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @link https://2am.tech/
 * @package Da\QrCode\Format
 */
abstract class AbstractFormat implements FormatInterface
{
    /**
     * Constructor.
     * The default implementation does two things:
     *
     * - Initializes the object with the given configuration `$config`.
     * - Call [[init()]].
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * - the last parameter of the constructor is a configuration array, like `$config` here.
     * - call the parent implementation at the end of the constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $name => $value) {
                $this->$name = $value;
            }
        }

        $this->init();
    }

    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     *
     * @param  string $name the property name
     *
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException     if the property is write-only
     * @return mixed                    the property value
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     *
     * @param  string $name the property name or the event name
     * @param  mixed $value the property value
     *
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException     if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;

        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        }

        throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     *
     * @param  string $name the property name or the event name
     *
     * @return bool   whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     *
     * @param  string $name the method name
     * @param  array $params method parameters
     *
     * @throws UnknownMethodException when calling unknown method
     * @return mixed                  the method return value
     */
    public function __call($name, $params)
    {
        throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * @return string the string representation of the object
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * Initialization method
     */
    public function init(): void
    {
    }

    /**
     * Returns a value indicating whether a property is defined.
     * A property is defined if:
     *
     * - the class has a getter or setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param  string $name the property name
     * @param  bool $checkVars whether to treat member variables as properties
     *
     * @return bool   whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true): bool
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * A property is readable if:
     *
     * - the class has a getter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param  string $name the property name
     * @param  bool $checkVars whether to treat member variables as properties
     *
     * @return bool   whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true): bool
    {
        return method_exists($this, 'get' . $name) || ($checkVars && property_exists($this, $name));
    }

    /**
     * Returns a value indicating whether a property can be set.
     * A property is writable if:
     *
     * - the class has a setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param  string $name the property name
     * @param  bool $checkVars whether to treat member variables as properties
     *
     * @return bool   whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true): bool
    {
        return method_exists($this, 'set' . $name) || ($checkVars && property_exists($this, $name));
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     *
     * @param  string $name the method name
     *
     * @return bool   whether the method is defined
     */
    public function hasMethod($name): bool
    {
        return method_exists($this, $name);
    }
}
