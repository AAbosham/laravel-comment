<?php
declare(strict_types=1);

namespace Aabosham\LaravelComment\Tests\Models;

use Aabosham\LaravelComment\CanComment;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property boolean $is_admin
 */
class User extends Model
{
    use CanComment;

    protected $guarded = [];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public $timestamps = false;

    public function canCommentWithoutApprove(): bool
    {
        return $this->is_admin;
    }
}
