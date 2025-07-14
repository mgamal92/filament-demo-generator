<?php

namespace Mgamal92\FilamentDemoGenerator\Support;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ReflectionMethod;

class RelationResolver
{
    public static function getBelongsToRelations(object $model): array
    {
        $relations = [];
        $class = get_class($model);
        $methods = get_class_methods($class);

        foreach ($methods as $method) {
            if (str_starts_with($method, '__')) continue;

            try {
                $reflection = new ReflectionMethod($class, $method);

                if (self::isEligibleRelationMethod($reflection, $class)) {
                    $return = $reflection->invoke($model);

                    if ($return instanceof BelongsTo) {
                        $relations[$return->getForeignKeyName()] = [
                            'relation' => $return,
                            'method' => $method,
                        ];
                    }
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return $relations;
    }

    protected static function isEligibleRelationMethod(\ReflectionMethod $method, string $class): bool
    {
        return $method->isPublic()
            && !$method->isStatic()
            && $method->getNumberOfParameters() === 0
            && $method->getDeclaringClass()->getName() === $class;
    }
}
