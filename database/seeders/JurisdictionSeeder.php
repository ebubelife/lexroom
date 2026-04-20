<?php

namespace Database\Seeders;

use App\Models\Jurisdiction;
use Illuminate\Database\Seeder;

class JurisdictionSeeder extends Seeder
{
    public function run(): void
    {
        $jurisdictions = [

            // ── United Kingdom ───────────────────────────────────────
            ['region' => 'United Kingdom', 'flag' => '🇬🇧', 'order' => 0, 'names' => [
                'England & Wales', 'Scotland', 'Northern Ireland',
            ]],

            // ── United States ────────────────────────────────────────
            ['region' => 'United States', 'flag' => '🇺🇸', 'order' => 1, 'names' => [
                'New York', 'California', 'Texas', 'Florida', 'Delaware',
                'Illinois', 'Georgia', 'Washington', 'Massachusetts',
                'New Jersey', 'Pennsylvania', 'Ohio', 'Michigan', 'Arizona',
                'Colorado', 'Nevada', 'North Carolina', 'Virginia',
                'Other US State',
            ]],

            // ── Canada ───────────────────────────────────────────────
            ['region' => 'Canada', 'flag' => '🇨🇦', 'order' => 2, 'names' => [
                'Ontario', 'British Columbia', 'Quebec', 'Alberta',
                'Manitoba', 'Saskatchewan', 'Nova Scotia', 'New Brunswick',
                'Other Canadian Province',
            ]],

            // ── Europe ───────────────────────────────────────────────
            ['region' => 'Europe', 'flag' => '🇪🇺', 'order' => 3, 'names' => [
                'Ireland', 'Germany', 'France', 'Netherlands', 'Spain', 'Italy',
                'Portugal', 'Belgium', 'Switzerland', 'Austria', 'Sweden',
                'Norway', 'Denmark', 'Finland', 'Poland', 'Czech Republic',
                'Hungary', 'Romania', 'Bulgaria', 'Greece', 'Croatia',
                'Slovakia', 'Slovenia', 'Estonia', 'Latvia', 'Lithuania',
                'Luxembourg', 'Malta', 'Cyprus', 'Iceland', 'Other European Country',
            ]],

            // ── Australia & New Zealand ──────────────────────────────
            ['region' => 'Australia & New Zealand', 'flag' => '🇦🇺', 'order' => 4, 'names' => [
                'New South Wales', 'Victoria', 'Queensland', 'Western Australia',
                'South Australia', 'Tasmania', 'New Zealand', 'Other Australian State',
            ]],

            // ── Middle East ──────────────────────────────────────────
            ['region' => 'Middle East', 'flag' => '🌏', 'order' => 5, 'names' => [
                'UAE (Dubai)', 'UAE (Abu Dhabi)', 'Saudi Arabia', 'Qatar',
                'Kuwait', 'Bahrain', 'Oman', 'Jordan', 'Lebanon',
                'Israel', 'Turkey', 'Other Middle East',
            ]],

            // ── South Asia ───────────────────────────────────────────
            ['region' => 'South Asia', 'flag' => '🌏', 'order' => 6, 'names' => [
                'India', 'Pakistan', 'Bangladesh', 'Sri Lanka',
                'Nepal', 'Other South Asia',
            ]],

            // ── East & Southeast Asia ────────────────────────────────
            ['region' => 'East & Southeast Asia', 'flag' => '🌏', 'order' => 7, 'names' => [
                'Singapore', 'Malaysia', 'Hong Kong', 'China', 'Japan',
                'South Korea', 'Indonesia', 'Philippines', 'Thailand',
                'Vietnam', 'Myanmar', 'Cambodia', 'Other East/SE Asia',
            ]],

            // ── Latin America ────────────────────────────────────────
            ['region' => 'Latin America', 'flag' => '🌎', 'order' => 8, 'names' => [
                'Brazil', 'Mexico', 'Argentina', 'Colombia', 'Chile',
                'Peru', 'Venezuela', 'Ecuador', 'Bolivia', 'Uruguay',
                'Paraguay', 'Costa Rica', 'Panama', 'Jamaica',
                'Trinidad & Tobago', 'Other Latin America',
            ]],

            // ── Africa (Nigeria included here, no special treatment) ─
            ['region' => 'Africa', 'flag' => '🌍', 'order' => 9, 'names' => [
                'Nigeria (Federal)', 'Lagos State', 'Abuja (FCT)', 'Rivers State',
                'Kano State', 'Ogun State', 'Oyo State', 'Delta State',
                'Anambra State', 'Enugu State', 'Imo State', 'Kaduna State',
                'Kwara State', 'Edo State', 'Cross River State', 'Akwa Ibom State',
                'Borno State', 'Plateau State', 'Osun State', 'Ekiti State',
                'Other Nigerian State',
                'Ghana', 'Kenya', 'South Africa', 'Uganda', 'Tanzania',
                'Ethiopia', 'Rwanda', 'Senegal', "Côte d'Ivoire", 'Cameroon',
                'Zimbabwe', 'Zambia', 'Mozambique', 'Angola', 'Botswana',
                'Namibia', 'Malawi', 'Sierra Leone', 'Liberia', 'Gambia',
                'Egypt', 'Morocco', 'Tunisia', 'Algeria', 'Sudan',
                'Other African Country',
            ]],

            // ── International ────────────────────────────────────────
            ['region' => 'International', 'flag' => '🌐', 'order' => 10, 'names' => [
                'International / Cross-border', 'Other',
            ]],
        ];

        foreach ($jurisdictions as $group) {
            foreach ($group['names'] as $name) {
                Jurisdiction::firstOrCreate(
                    ['name' => $name, 'region' => $group['region']],
                    [
                        'region_flag' => $group['flag'],
                        'is_active'   => true,
                        'sort_order'  => $group['order'],
                    ]
                );
            }
        }
    }
}
