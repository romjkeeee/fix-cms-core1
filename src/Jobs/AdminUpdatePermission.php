<?php

namespace AltSolution\Admin\Jobs;

use AltSolution\Admin\Models\AclPermission;
use App\Jobs\Job;

class AdminUpdatePermission extends Job
{
    /**
     * Add new permissions, remove not existing
     *
     * @return void
     */
    public function handle()
    {
        $allPermissions = cms_system()->getPermission()->getAllPermissions();
        $existsPermissions = AclPermission::all();
        $updatedPermissions = [];

        foreach ($allPermissions as $allPermission) {
            $existsPermission = $existsPermissions
                ->where('name', $allPermission->getName())
                ->first();
            
            if ($existsPermission === null) {
                $newPermission = new AclPermission();
                $newPermission->name = $allPermission->getName();
                $newPermission->description = $allPermission->getDescription();
                $newPermission->save();

                $updatedPermissions[] = $newPermission->id;
            } else {
                $existsPermission->description = $allPermission->getDescription();
                if ($existsPermission->isDirty()) {
                    $existsPermission->save();
                }

                $updatedPermissions[] = $existsPermission->id;
            }
        }
        
        foreach ($existsPermissions as $existsPermission) {
            if (!in_array($existsPermission->id, $updatedPermissions)) {
                $existsPermission->delete();
            }
        }
    }
}
