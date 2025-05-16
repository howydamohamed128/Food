<?php

namespace App\Traits;

trait Publishable
{
    public function toggleStatus(): bool
    {
        if ($this->isActive()) {
            return $this->inActive();
        }
        return $this->active();
    }

    public function isActive()
    {
        return $this->status;
    }

    public function active()
    {
        return $this->update(['status' => 1]);
    }

    public function inActive()
    {
        return $this->update(['status' => 0]);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function toggleStatusSite(): bool
    {
        if ($this->isSiteActive()) {
            return $this->inSiteActive();
        }
        return $this->activeSite();
    }

    public function isSiteActive()
    {
        return $this->site_status;
    }

    public function inSiteActive()
    {
        return $this->update(['site_status' => 0]);
    }
    public function activeSite()
    {
        return $this->update(['site_status' => 1]);
    }
    public function scopeSiteEnabled($query)
    {
        return $query->where('site_status', 1);
    }
}
