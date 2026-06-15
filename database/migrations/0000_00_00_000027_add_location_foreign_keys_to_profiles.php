<?php

use App\Support\LocationOptions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('countries')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('profiles', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('city')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('profiles', 'state_province_id')) {
                $table->foreignId('state_province_id')->nullable()->after('country_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('profiles', 'timezone_id')) {
                $table->foreignId('timezone_id')->nullable()->after('state_province_id')->constrained()->nullOnDelete();
            }
        });

        if (! Schema::hasColumn('profiles', 'country')) {
            return;
        }

        $countryIds = DB::table('countries')->pluck('id', 'name');

        DB::table('profiles')
            ->select(['id', 'country', 'province', 'timezone'])
            ->orderBy('id')
            ->chunkById(100, function ($profiles) use ($countryIds): void {
                foreach ($profiles as $profile) {
                    $countryId = filled($profile->country) ? ($countryIds[$profile->country] ?? null) : null;
                    $stateProvinceId = null;
                    $timezoneId = null;

                    if ($countryId && filled($profile->province)) {
                        $stateProvinceId = DB::table('state_provinces')
                            ->where('country_id', $countryId)
                            ->where('name', $profile->province)
                            ->value('id');
                    }

                    if (filled($profile->timezone)) {
                        $timezoneId = DB::table('timezones')
                            ->where(function ($query) use ($profile): void {
                                $query->where('code', $profile->timezone)
                                    ->orWhere('name', $profile->timezone);
                            })
                            ->when($countryId, fn ($query) => $query->where(function ($query) use ($countryId): void {
                                $query->where('country_id', $countryId)->orWhereNull('country_id');
                            }))
                            ->value('id');
                    }

                    DB::table('profiles')->where('id', $profile->id)->update([
                        'country_id' => $countryId,
                        'state_province_id' => $stateProvinceId,
                        'timezone_id' => $timezoneId,
                    ]);
                }
            });

        Schema::table('profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('profiles', 'country')) {
                $table->dropColumn('country');
            }

            if (Schema::hasColumn('profiles', 'province')) {
                $table->dropColumn('province');
            }

            if (Schema::hasColumn('profiles', 'timezone')) {
                $table->dropColumn('timezone');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('profiles', 'country_id')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('profiles', 'country')) {
                $table->string('country')->nullable()->after('city');
            }

            if (! Schema::hasColumn('profiles', 'province')) {
                $table->string('province')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('profiles', 'timezone')) {
                $table->string('timezone')->nullable()->after('country');
            }
        });

        $countries = DB::table('countries')->pluck('name', 'id');
        $states = DB::table('state_provinces')->get(['id', 'name'])->keyBy('id');
        $timezones = DB::table('timezones')->get(['id', 'code', 'name'])->keyBy('id');

        DB::table('profiles')
            ->select(['id', 'country_id', 'state_province_id', 'timezone_id'])
            ->orderBy('id')
            ->chunkById(100, function ($profiles) use ($countries, $states, $timezones): void {
                foreach ($profiles as $profile) {
                    DB::table('profiles')->where('id', $profile->id)->update([
                        'country' => $profile->country_id ? ($countries[$profile->country_id] ?? null) : null,
                        'province' => $profile->state_province_id ? ($states[$profile->state_province_id]->name ?? null) : null,
                        'timezone' => $profile->timezone_id ? ($timezones[$profile->timezone_id]->code ?? $timezones[$profile->timezone_id]->name ?? null) : null,
                    ]);
                }
            });

        Schema::table('profiles', function (Blueprint $table): void {
            if (Schema::hasColumn('profiles', 'timezone_id')) {
                $table->dropConstrainedForeignId('timezone_id');
            }

            if (Schema::hasColumn('profiles', 'state_province_id')) {
                $table->dropConstrainedForeignId('state_province_id');
            }

            if (Schema::hasColumn('profiles', 'country_id')) {
                $table->dropConstrainedForeignId('country_id');
            }
        });
    }
};
