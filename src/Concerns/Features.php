<?php

namespace Diviky\Subscriptions\Concerns;

trait Features
{
    public function getFeatureUsage($name, $subscription = 'default')
    {
        $subscription = $this->subscription($subscription);
        if (! $subscription) {
            return false;
        }

        return $subscription->getFeatureUsage($name);
    }

    public function canUseFeature($name, $subscription = 'default')
    {
        $subscription = $this->subscription($subscription);
        if (! $subscription) {
            return false;
        }

        return $subscription->canUseFeature($name);
    }

    public function recordFeatureUsage($name, $increment = 1, $subscription = 'default')
    {
        $subscription = $this->subscription($subscription);
        if (! $subscription) {
            return false;
        }

        return $subscription->recordFeatureUsage($name, $increment);
    }

    public function reduceFeatureUsage($name, $decrement = 1, $subscription = 'default')
    {
        $subscription = $this->subscription($subscription);
        if (! $subscription) {
            return false;
        }

        return $subscription->reduceFeatureUsage($name, $decrement);
    }

    public function usageDelete($subscription = 'default')
    {
        $subscription = $this->subscription($subscription);
        if (! $subscription) {
            return false;
        }

        return $subscription->usage()->delete();
    }
}
