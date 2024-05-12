<?php

namespace Diviky\Subscriptions\Concerns;

use Diviky\Subscriptions\Models\PlanSubscription;

trait Subscriptions
{
    /**
     * Subscribe user to a new plan.
     *
     * @param  string  $name
     * @param  \Diviky\Subscriptions\Models\Plan  $plan
     */
    public function subscribeToPlan($plan, $name = 'default'): PlanSubscription
    {
        $plan = app('diviky.subscriptions.plan')
            ->where('slug', $plan)
            ->first();

        $name = $name ?: $plan->slug;

        return $this->newSubscription($name, $plan);
    }

    /**
     * Check if the user subscribed to the given plan.
     *
     * @param  int  $planId
     * @param  mixed  $plans
     * @param  mixed  $name
     */
    public function subscribedToPlan($plans, $name = 'default'): bool
    {
        $subscription = $this->subscription($name);

        if (!$subscription || !$subscription->valid()) {
            return false;
        }

        $plans = !\is_array($plans) ? [$plans] : $plans;

        foreach ($plans as $plan) {
            if ($subscription->hasPlan($plan)) {
                return true;
            }
        }

        return false;
    }

    public function getPlanBySlug($slug)
    {
        return app('diviky.subscriptions.plan')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Determine if the Stripe model is on trial.
     *
     * @param  string  $name
     * @param  null|string  $plan
     * @return bool
     */
    public function onTrial($name = 'default', $plan = null)
    {
        $subscription = $this->subscription($name);

        if (!$subscription || !$subscription->onTrial()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Determine if the entity has a valid subscription on the given plan.
     *
     * @param  string  $plan
     * @return bool
     */
    public function onPlan($plan)
    {
        return !\is_null($this->subscriptions->first(function (PlanSubscription $subscription) use ($plan) {
            return $subscription->valid() && $subscription->hasPlan($plan);
        }));
    }

    /**
     * Determine if the Stripe model has a given subscription.
     *
     * @param  string  $name
     * @param  null|string  $plan
     * @return bool
     */
    public function subscribed($name = 'default', $plan = null)
    {
        $subscription = $this->subscription($name);

        if (!$subscription || !$subscription->valid()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    public function changeToPlan($plan, $name = 'default')
    {
        $plan = $this->getPlanBySlug($plan);

        $subscription = $this->subscription($name);

        if (!$subscription) {
            $subscription = $this->subscribeToPlan($plan->slug, $name);
        }

        return $subscription
            ->skipTrial()
            ->changePlan($plan);
    }

    public function switchToPlan($plan, $name = 'default')
    {
        $plan = $this->getPlanBySlug($plan);

        $subscription = $this->subscription($name);

        return $subscription->changePlan($plan);
    }

    public function subscriptionValid($name = 'default')
    {
        $subscription = $this->subscription($name);

        if (!$subscription) {
            return false;
        }

        return $subscription->valid();
    }

    public function subscriptionCancelled($name = 'default')
    {
        $subscription = $this->subscription($name);

        if (!$subscription) {
            return false;
        }

        return $subscription->cancelled();
    }

    public function subscriptionOnGracePeriod($name = 'default')
    {
        $subscription = $this->subscription($name);

        if (!$subscription) {
            return false;
        }

        return $subscription->onGracePeriod();
    }

    public function subscriptionOnTrial($name = 'default')
    {
        $subscription = $this->subscription($name);

        if (!$subscription) {
            return false;
        }

        return $subscription->onTrial();
    }

    public function cancelAll()
    {
        foreach ($this->subscriptions as $sub) {
            if ($sub->active()) {
                $sub->cancel();
            }
        }

        return $this;
    }

    public function getPlanName($name = 'default')
    {
        $subscription = $this->subscription($name);

        if (!$subscription) {
            return null;
        }

        return \strtolower($subscription->plan->name);
    }
}
