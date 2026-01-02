<?php

namespace App\Services;

use App\Models\GameRate;
use App\Models\User;
use App\Models\Monster;

class BattleService
{
    /**
     * Calculate damage with critical hit chance
     */
    public static function calculateDamage($attacker, $defender, $isUser = true): array
    {
        $baseDamage = max(1, $attacker->attack - $defender->defense);
        
        // Get critical rate
        $criticalRate = $isUser 
            ? GameRate::getRate('base_critical_rate', 0.05)
            : ($defender->critical_rate ?? 0.05);
        
        // Check for critical hit
        $isCritical = (mt_rand(1, 10000) / 10000) <= $criticalRate;
        
        if ($isCritical) {
            $criticalMultiplier = $isUser
                ? GameRate::getRate('base_critical_damage', 2.0)
                : ($defender->critical_damage_multiplier ?? 2.0);
            
            $damage = (int) ($baseDamage * $criticalMultiplier);
            
            return [
                'damage' => $damage,
                'is_critical' => true,
                'multiplier' => $criticalMultiplier,
            ];
        }
        
        return [
            'damage' => $baseDamage,
            'is_critical' => false,
            'multiplier' => 1.0,
        ];
    }

    /**
     * Check if attack is dodged
     */
    public static function checkDodge($defender, $isUser = true): bool
    {
        $dodgeRate = $isUser
            ? GameRate::getRate('base_dodge_rate', 0.03)
            : ($defender->dodge_rate ?? 0.05);
        
        return (mt_rand(1, 10000) / 10000) <= $dodgeRate;
    }

    /**
     * Calculate drop chance with multipliers
     */
    public static function calculateDropChance($monster, $baseChance): float
    {
        $dropMultiplier = $monster->drop_rate_multiplier ?? 1.0;
        $bossMultiplier = $monster->is_boss 
            ? GameRate::getRate('boss_drop_multiplier', 2.0)
            : 1.0;
        
        return min(1.0, $baseChance * $dropMultiplier * $bossMultiplier);
    }

    /**
     * Check if item should drop
     */
    public static function shouldDropItem($monster, $baseChance): bool
    {
        $finalChance = self::calculateDropChance($monster, $baseChance);
        return (mt_rand(1, 10000) / 10000) <= $finalChance;
    }

    /**
     * Check if rare item should drop
     */
    public static function shouldDropRareItem($monster): bool
    {
        $rareRate = $monster->rare_drop_rate ?? GameRate::getRate('rare_drop_base_rate', 0.01);
        $bossMultiplier = $monster->is_boss 
            ? GameRate::getRate('boss_drop_multiplier', 2.0)
            : 1.0;
        
        $finalRate = min(1.0, $rareRate * $bossMultiplier);
        return (mt_rand(1, 10000) / 10000) <= $finalRate;
    }

    /**
     * Check if monster should be encountered
     */
    public static function shouldEncounter($monster): bool
    {
        $encounterRate = $monster->encounter_rate ?? GameRate::getRate('encounter_base_rate', 1.0);
        return (mt_rand(1, 10000) / 10000) <= $encounterRate;
    }
}

