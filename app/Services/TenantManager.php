<?php
namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function getTenant(): ?Tenant
    {
        if ($this->tenant) {
            return $this->tenant;
        }

        if (app()->has('tenant')) {
            return app('tenant');
        }

        if (Auth::check() && Auth::user()->tenant_id) {
            $this->tenant = Auth::user()->tenant;
            return $this->tenant;
        }

        return null;
    }

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        app()->instance('tenant', $tenant);
    }

    public function getTenantId(): ?int
    {
        return $this->getTenant()?->id;
    }
}
