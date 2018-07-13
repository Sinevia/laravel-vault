<?php

namespace Sinevia\Vault;

trait VaultAttributeTrait {

    private function getVaultAttribute($vaultId) {
        $vault = Model\Vault::find($vaultId);
        if ($vault == null) {
            return '';
        }
        return $vault->getValue(\App\Helpers\AppHelper::vaultKey());
    }

    private function setVaultAttribute($attributeName, $value) {
        $vault = Models\Vault::find($this->attributes[$attributeName]);
        if ($vault == null) {
            $vaultId = Models\Vault::storeValue($value, \App\Helpers\AppHelper::vaultKey());
            $this->attributes[$attributeName] = $vaultId;
            $vault = Models\Vault::find($this->attributes[$attributeName]);
        }
        return $vault->setValue($value, \App\Helpers\AppHelper::vaultKey());
    }

}
