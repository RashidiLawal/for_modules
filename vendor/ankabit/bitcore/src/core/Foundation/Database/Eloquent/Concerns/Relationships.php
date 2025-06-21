<?php

declare(strict_types=1);

namespace BitCore\Foundation\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait Relationships
{
    public function hasManyRelation(
        string $related,
        ?string $foreignKey = null,
        ?string $localKey = null
    ): HasMany {
        return $this->hasMany($related, $foreignKey, $localKey);
    }

    public function belongsToRelation(
        string $related,
        ?string $foreignKey = null,
        ?string $ownerKey = null,
        ?string $relation = null
    ): BelongsTo {
        return $this->belongsTo($related, $foreignKey, $ownerKey, $relation);
    }

    public function belongsToManyRelation(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null,
        ?string $parentKey = null,
        ?string $relatedKey = null,
        ?string $relation = null
    ): BelongsToMany {
        return $this->belongsToMany(
            $related,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relation
        );
    }

    public function hasOneRelation(
        string $related,
        ?string $foreignKey = null,
        ?string $localKey = null
    ): HasOne {
        return $this->hasOne($related, $foreignKey, $localKey);
    }

    public function morphManyRelation(
        string $related,
        string $name,
        ?string $type = null,
        ?string $id = null,
        ?string $localKey = null
    ): MorphMany {
        return $this->morphMany($related, $name, $type, $id, $localKey);
    }

    public function morphToRelation(
        ?string $name = null,
        ?string $type = null,
        ?string $id = null,
        ?string $ownerKey = null
    ): MorphTo {
        return $this->morphTo($name, $type, $id, $ownerKey);
    }
}
