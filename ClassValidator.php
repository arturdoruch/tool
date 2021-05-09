<?php

namespace ArturDoruch\Tool;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 *
 * @deprecated Use the "arturdoruch/exception-formatter" component instead.
 */
class ClassValidator
{
    /**
     * @param string $className The fully qualified class name.
     * @param string $expectedClassName The fully qualified expected class name.
     * @param string $classType The class type.
     *
     * @throws \LogicException
     */
    public static function validateSubclassOf(string $className, string $expectedClassName, string $classType)
    {
        if (!(new \ReflectionClass($className))->isSubclassOf($expectedClassName)) {
            throw new \LogicException(sprintf(
                'The %s class "%s" must extends the "%s" class.', $classType, $className, $expectedClassName
            ));
        }
    }

    /**
     * @param string $className The fully qualified class name.
     * @param string $expectedInterface The fully qualified interface.
     * @param string $classType The class type.
     *
     * @throws \LogicException
     */
    public static function validateImplementsInterface(string $className, string $expectedInterface, string $classType = null)
    {
        if (!(new \ReflectionClass($className))->implementsInterface($expectedInterface)) {
            throw new \LogicException(sprintf(
                'The %sclass "%s" must implement the "%s" interface.', $classType ? $classType . ' ' : '', $className, $expectedInterface
            ));
        }
    }
}
