<?php
declare(strict_types=1);

namespace Aabosham\LaravelComment;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use App\Models\User;

/**
 * @property Collection $comments
 */
trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(config('comment.model'), 'commentable');
    }

    public function canBeRated(): bool
    {
        return false;
    }

    public function mustBeApproved(): bool
    {
        return false;
    }

    public function primaryId(): string
    {
        return (string)$this->getAttribute($this->primaryKey);
    }

    public function averageRate(int $round = 2): float
    {
        if (!$this->canBeRated()) {
            return 0;
        }

        /** @var Builder $rates */
        $rates = $this->comments()->where('rate','>',0)->approvedComments();

        if (!$rates->exists()) {
            return 0;
        }

        return round((float)$rates->avg('rate'), $round);
    }

    public function totalCommentsCount(): int
    {
        if (!$this->mustBeApproved()) {
            return $this->comments()->count();
        }

        return $this->comments()->approvedComments()->count();
    }

    public function commentBy(){
        return $this->belongsTo(User::class ,'commented_id');
    }

    public function totalCommentsCountDigital(): string
    {
        $count = $this->totalCommentsCount();

        if($count > (1000 * 1000)){
            $count_string = ($count / (1000 * 1000)).'M';
        } else if($count > 1000){
            $count_string = ($count / 1000).'K';
        } else {
            $count_string = $count;
        }

        return (string) $count_string;
    }
}
