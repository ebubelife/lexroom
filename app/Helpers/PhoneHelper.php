<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Nigerian network prefixes
     */
    public static $nigerianPrefixes = [
        // MTN
        '0803', '0806', '0813', '0814', '0816', '0903', '0906', '0913', '0916',
        // Airtel
        '0802', '0808', '0812', '0901', '0902', '0904', '0907', '0912',
        // Glo
        '0805', '0807', '0811', '0815', '0905', '0915',
        // 9mobile (Etisalat)
        '0809', '0817', '0818', '0908', '0909',
        // NTEL
        '0804',
        // Smile
        '0702',
        // Spectranet
        '0070',
        // Visafone
        '070', '071', '072', '073', '074', '075', '076', '077', '078', '079',
        // Starcomms
        '0819',
        // Zoom Mobile
        '0707',
        // Additional common prefixes
        '0701', '0708', '0810', '0820', '0821', '0822', '0823', '0824', '0825', '0826', '0827', '0828', '0829',
        '0830', '0831', '0832', '0833', '0834', '0835', '0836', '0837', '0838', '0839',
        '0840', '0841', '0842', '0843', '0844', '0845', '0846', '0847', '0848', '0849',
        '0850', '0851', '0852', '0853', '0854', '0855', '0856', '0857', '0858', '0859',
        '0860', '0861', '0862', '0863', '0864', '0865', '0866', '0867', '0868', '0869',
        '0870', '0871', '0872', '0873', '0874', '0875', '0876', '0877', '0878', '0879',
        '0880', '0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889',
        '0890', '0891', '0892', '0893', '0894', '0895', '0896', '0897', '0898', '0899',
    ];

    /**
     * Validate and normalize Nigerian phone number
     */
    public static function validateAndNormalize($phone)
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Handle different formats
        if (str_starts_with($phone, '+234')) {
            // +2348070190815 -> 08070190815
            $phone = '0' . substr($phone, 4);
        } elseif (str_starts_with($phone, '234')) {
            // 2348070190815 -> 08070190815
            $phone = '0' . substr($phone, 3);
        } elseif (preg_match('/^[789]\d{9}$/', $phone)) {
            // 8070190815 -> 08070190815 (missing leading 0)
            $phone = '0' . $phone;
        }

        // Check if it's a valid Nigerian number
        if (!self::isValidNigerianNumber($phone)) {
            return false;
        }

        return $phone;
    }

    /**
     * Check if phone number is a valid Nigerian number
     */
    public static function isValidNigerianNumber($phone)
    {



        // Must be 11 digits starting with 0
        if (!preg_match('/^0\d{10}$/', $phone)) {
            return false;
        }

        // Check if it starts with valid Nigerian prefixes
        // 070-079 (Visafone, Multilinks, etc.)
        // 080-089 (MTN, Airtel, Glo, 9mobile, etc.)
        // 090-099 (New generation networks)
        $firstThree = substr($phone, 0, 3);
        return in_array($firstThree, ['070', '071', '072', '073', '074', '075', '076', '077', '078', '079']) ||
               preg_match('/^08[0-9]$/', $firstThree) ||
               preg_match('/^09[0-9]$/', $firstThree);
    }

    /**
     * Format phone number for display
     */
    public static function format($phone)
    {
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
        }

        return $phone;
    }

    /**
     * Convert to international format
     */
    public static function toInternational($phone)
    {
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return '+234' . substr($phone, 1);
        }

        return $phone;
    }
}