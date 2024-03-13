<?php

declare(strict_types=1);

namespace Rinvex\Subscriptions\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rinvex\Subscriptions\Models\Plan;
use Rinvex\Subscriptions\Models\PlanSubscription;
use Rinvex\Subscriptions\Services\Period;

trait HasPlanSubscriptions
{
    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    abstract public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Boot the HasPlanSubscriptions trait for the model.
     *
     * @return void
     */
    protected static function bootHasSubscriptions()
    {
        static::deleted(function ($plan) {
            $plan->planSubscriptions()->delete();
        });
    }

    /**
     * The subscriber may have many plan subscriptions.
     */
    public function planSubscriptions(): MorphMany
    {
        return $this->morphMany(config('diviky.subscriptions.models.plan_subscription'), 'subscriber', 'subscriber_type', 'subscriber_id');
    }

    /**
     * A model may have many active plan subscriptions.
     */
    public function activePlanSubscriptions(): Collection
    {
        return $this->planSubscriptions->reject->inactive();
    }

    /**
     * Get a plan subscription by slug.
     */
    public function planSubscription(string $subscriptionSlug): ?PlanSubscription
    {
        return $this->planSubscriptions()->where('slug', $subscriptionSlug)->first();
    }

    /**
     * Get subscribed plans.
     */
    public function subscribedPlans(): Collection
    {
        $planIds = $this->planSubscriptions->reject->inactive()->pluck('plan_id')->unique();

        return app('diviky.subscriptions.plan')->whereIn('id', $planIds)->get();
    }

    /**
     * Check if the subscriber subscribed to the given plan.
     *
     * @param  int  $planId
     */
    public function subscribedTo($planId): bool
    {
        $subscription = $this->planSubscriptions()->where('plan_id', $planId)->first();

        return $subscription && $subscription->active();
    }

    /**
     * Subscribe subscriber to a new plan.
     *
     * @param  string  $subscription
     */
    public function newPlanSubscription($subscription, Plan $plan, ?Carbon $startDate = null): PlanSubscription
    {
        $trial = new Period($plan->trial_interval, $plan->trial_period, $startDate ?? now());
        $period = new Period($plan->invoice_interval, $plan->invoice_period, $trial->getEndDate());

        return $this->planSubscriptions()->create([
            'name' => $subscription,
            'plan_id' => $plan->getKey(),
            'trial_ends_at' => $trial->getEndDate(),
            'starts_at' => $period->getStartDate(),
            'ends_at' => $period->getEndDate(),
        ]);
    }
}
