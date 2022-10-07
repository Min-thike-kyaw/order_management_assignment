<?php


namespace App\Traits;

trait SecureDeletes
{


    public function forceDeleteWithRelations(String ...$relations)
    {
        foreach($relations as $relation){
            $this->$relation()->withTrashed()->forceDelete();
        }
        $this->forceDelete();
        return true;
    }

    //
    public function canSecureDelete(String ...$relations)
    {
        $hasRelation = false;
        foreach ($relations as $relation) {
            if ($this->$relation()->count()) {
                $hasRelation = true;
                break;
            }
        }

        if ($hasRelation) {
            return false;
        } else {
            return true;
        }
    }
}
