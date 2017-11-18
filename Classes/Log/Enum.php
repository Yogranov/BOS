<?php
/**
 * @link    http://github.com/myclabs/php-enum
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

/**
 * Base Enum class
 *
 * Create an enum by implementing this class and adding class constants.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Daniel Costa <danielcosta@gmail.com>
 * @author Mirosław Filip <mirfilip@gmail.com>
 */

require __DIR__. "/IDBConvertable.php";

abstract class Enum implements IDBConvertable
{
    protected $name;

    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    protected $desc;

    /**
     * Store existing constants in a static cache per object.
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * Creates a new value of some type
     *
     * @param mixed $value
     *
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct($name, $value, $desc)
    {
        if (!$this->isValid($value)) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }

        $this->name = $name;
        $this->value = $value;
        $this->desc = $desc;
    }

    /**
     * @param int $id
     * @return Enum
     */
    public static function &GetById(int $id) {
        $res = self::search($id);

        return $res;
    }

    /**
     * @param int[] $ids
     * @return array : Enum
     */
    public static function GetByIds(array $ids) {
        $result = array();

        foreach ($ids as $id) {
            $data = &self::GetById($id);
            $result[$id] = $data;
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function GetId()
    {
        return $this->getValue();
    }

    public function GetKeyColumn()
    {
        throw new Exception("Shouldn't try that");
    }

    /**
     * Returns the enum key (i.e. the constant name).
     *
     * @return mixed
     */
    public function getKey()
    {
        return static::search($this->value);
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }



    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Compares one Enum with another.
     *
     * This method is final, for more information read https://github.com/myclabs/php-enum/issues/4
     *
     * @return bool True if Enums are equal, false if not equal
     */
    final public function equals(Enum $enum)
    {
        return $this->getValue() === $enum->getValue() && get_called_class() == get_class($enum);
    }

    /**
     * Returns the names (keys) of all constants in the Enum class
     *
     * @return array
     */
    public static function keys()
    {
        return array_keys(static::toArray());
    }

    /**
     * Returns instances of the Enum class of all Enum constants
     *
     * @return static[] Constant name in key, Enum instance in value
     */
    public static function values()
    {
        $values = array();

        foreach (static::toArray() as $key => $item) {
            $values[$key] = new static($key, $item[0], $item[1]);
        }

        return $values;
    }

    /**
     * Returns all possible values as an array
     * @return array Constant name in key, constant value in value
     * @throws Exception
     */
    public static function toArray()
    {
        $class = get_called_class();
        if (!array_key_exists($class, static::$cache)) {
            $reflection            = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();

            $idCountArray = array_count_values(array_column(static::$cache[$class], 0));
            foreach ($idCountArray as $item) {
                if ($item > 1)
                    throw new Exception("The enum $class has duplicate constant values");
            }
        }

        return static::$cache[$class];
    }

    /**
     * Check if is valid enum value
     *
     * @param $value
     *
     * @return bool
     */
    public static function isValid($value)
    {
        foreach (static::toArray() as $name => $item) {
            if ($item[0] == $value)
                return True;
        }

        return False;
    }

    /**
     * Check if is valid enum key
     *
     * @param $key
     *
     * @return bool
     */
    public static function isValidKey($key)
    {
        $array = static::toArray();

        return isset($array[$key]);
    }

    /**
     * Return key for value
     * @param $id
     * @return static
     * @throws Exception
     *
     */
    public static function search($id)
    {
        foreach (static::toArray() as $name => $item) {
            if ($item[0] == $id)
                return new static($name, $item[0], $item[1]);
        }

        throw new Exception("Unknown enum value ($id) for ".get_called_class());
    }

    /**
     * @param $key
     * @return static
     * @throws Exception
     */
    public static function searchByKey($key) {
        foreach (static::toArray() as $name => $item) {
            if ($name == $key)
                return new static($name, $item[0], $item[1]);
        }

        throw new Exception("Unknown enum key ($key) for ".get_called_class());
    }

    /**
     * @param $desc
     * @return static
     * @throws Exception
     */
    public static function searchByDesc($desc) {
        foreach (static::toArray() as $name => $item) {
            if ($item[1] == $desc)
                return new static($name, $item[0], $item[1]);
        }

        throw new Exception("Unknown enum Desc ($desc) for ".get_called_class());
    }


    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return static
     * @throws \BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($name, $array[$name][0], $array[$name][1]);
        }

        throw new \BadMethodCallException("No enum constant '$name' in class " . get_called_class());
    }
}