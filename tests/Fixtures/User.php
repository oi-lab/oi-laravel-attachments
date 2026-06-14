<?php

namespace OiLab\OiLaravelAttachments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OiLab\OiLaravelAttachments\Concerns\HasAttachments;

class User extends Authenticatable
{
    use HasAttachments;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
