<?php

namespace App\Support;

class AdminPermissions
{
    const ROLES = [
        'super_admin',
        'platform_admin',
        'billing_admin',
        'case_manager',
        'document_reviewer',
        'lawyer_manager',
        'support_agent',
        'auditor',
    ];

    const ROLE_LABELS = [
        'super_admin'       => 'Super Admin',
        'platform_admin'    => 'Platform Admin',
        'billing_admin'     => 'Billing Admin',
        'case_manager'      => 'Case Manager',
        'document_reviewer' => 'Document Reviewer',
        'lawyer_manager'    => 'Lawyer Manager',
        'support_agent'     => 'Support Agent',
        'auditor'           => 'Auditor',
    ];

    /**
     * Maps ability slug → roles that have it (beyond super_admin, who always passes).
     */
    const MAP = [
        // Users
        'admin.users.view'           => ['platform_admin', 'support_agent', 'auditor'],
        'admin.users.manage'         => ['platform_admin', 'support_agent'],
        'admin.users.delete'         => [], // super_admin only

        // Rooms
        'admin.rooms.view'           => ['platform_admin', 'case_manager', 'support_agent', 'auditor'],
        'admin.rooms.manage'         => ['platform_admin', 'case_manager', 'support_agent'],
        'admin.rooms.delete'         => [], // super_admin only

        // Billing
        'admin.billing.view'         => ['platform_admin', 'billing_admin', 'auditor'],
        'admin.billing.manage'       => ['platform_admin', 'billing_admin'],

        // Evidence files
        'admin.files.view'           => ['platform_admin', 'case_manager', 'document_reviewer', 'auditor'],
        'admin.files.delete'         => ['platform_admin'],

        // Reports
        'admin.reports.view'         => ['platform_admin', 'case_manager', 'document_reviewer', 'auditor'],
        'admin.reports.manage'       => ['platform_admin', 'case_manager'],

        // Referrals
        'admin.referrals.view'       => ['platform_admin', 'billing_admin', 'auditor'],
        'admin.referrals.manage'     => ['billing_admin'],

        // Wallets
        'admin.wallets.view'         => ['platform_admin', 'billing_admin', 'auditor'],
        'admin.wallets.manage'       => ['billing_admin'],

        // AI Agents (provider switching)
        'admin.agents.manage'        => ['platform_admin'],

        // System settings — super_admin only
        'admin.settings.manage'      => [],

        // FM Refer Lawyers
        'admin.lawyers.view'         => ['platform_admin', 'lawyer_manager', 'auditor'],
        'admin.lawyers.manage'       => ['lawyer_manager'],

        // Jurisdictions
        'admin.jurisdictions.view'   => ['platform_admin', 'lawyer_manager', 'auditor'],
        'admin.jurisdictions.manage' => ['lawyer_manager'],

        // Subscription plans — super_admin only
        'admin.plans.view'           => ['platform_admin', 'billing_admin', 'auditor'],
        'admin.plans.manage'         => [],

        // Credits / top-ups — super_admin only
        'admin.credits.view'         => ['platform_admin', 'billing_admin', 'auditor'],
        'admin.credits.manage'       => [],

        // Admin account management — super_admin only
        'admin.admins.manage'        => [],
    ];
}
