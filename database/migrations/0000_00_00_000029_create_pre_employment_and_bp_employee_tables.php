<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pe_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->date('date_of_birth')->nullable();
            $table->string('license_number')->nullable();
            $table->string('efg_associate_id')->nullable()->unique();
            $table->string('efg_invite_link')->nullable()->unique();
            $table->boolean('is_efg_active_associate')->default(false);
            $table->text('bio')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('best_contact_time')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_emp_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->constrained('pe_employees')->cascadeOnDelete();
            $table->string('type')->default('home');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('state_province_id')->nullable()->constrained('state_provinces')->nullOnDelete();
            $table->string('postal_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_emp_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->constrained('pe_employees')->cascadeOnDelete();
            $table->string('type')->default('mobile');
            $table->string('phone_number');
            $table->string('extension')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_job_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->unique()->constrained('pe_employees')->cascadeOnDelete();
            $table->foreignId('rank_id')->nullable()->constrained('ranks')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('job_title')->nullable();
            $table->date('start_date')->nullable();
            $table->string('department')->nullable();
            $table->string('employment_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_emp_tax_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->unique()->constrained('pe_employees')->cascadeOnDelete();
            $table->string('tax_id_type')->nullable();
            $table->string('tax_id_last_four')->nullable();
            $table->string('filing_status')->nullable();
            $table->unsignedSmallInteger('exemptions')->nullable();
            $table->decimal('additional_withholding', 8, 2)->nullable();
            $table->timestamp('w4_signed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_emp_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->constrained('pe_employees')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pe_emp_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pe_employee_id')->constrained('pe_employees')->cascadeOnDelete();
            $table->string('credential_type');
            $table->string('credential_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->foreignId('jurisdiction_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('jurisdiction_state_id')->nullable()->constrained('state_provinces')->nullOnDelete();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('pe_employee_id')->nullable()->constrained('pe_employees')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->date('date_of_birth')->nullable();
            $table->string('license_number')->nullable();
            $table->string('efg_associate_id')->nullable()->unique();
            $table->string('efg_invite_link')->nullable()->unique();
            $table->boolean('is_efg_active_associate')->default(false);
            $table->text('bio')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('best_contact_time')->nullable();
            $table->date('hire_date');
            $table->timestamp('hired_at');
            $table->foreignId('hired_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_emp_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_address_id')->nullable()->constrained('pe_emp_addresses')->nullOnDelete();
            $table->string('type')->default('home');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('state_province_id')->nullable()->constrained('state_provinces')->nullOnDelete();
            $table->string('postal_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_emp_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_phone_id')->nullable()->constrained('pe_emp_phones')->nullOnDelete();
            $table->string('type')->default('mobile');
            $table->string('phone_number');
            $table->string('extension')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_job_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->unique()->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_job_data_id')->nullable()->constrained('pe_job_data')->nullOnDelete();
            $table->foreignId('rank_id')->nullable()->constrained('ranks')->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('job_title')->nullable();
            $table->date('start_date')->nullable();
            $table->string('department')->nullable();
            $table->string('employment_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_emp_tax_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->unique()->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_tax_data_id')->nullable()->constrained('pe_emp_tax_data')->nullOnDelete();
            $table->string('tax_id_type')->nullable();
            $table->string('tax_id_last_four')->nullable();
            $table->string('filing_status')->nullable();
            $table->unsignedSmallInteger('exemptions')->nullable();
            $table->decimal('additional_withholding', 8, 2)->nullable();
            $table->timestamp('w4_signed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_emp_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_document_id')->nullable()->constrained('pe_emp_documents')->nullOnDelete();
            $table->string('document_type');
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bp_emp_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bp_employee_id')->constrained('bp_employees')->cascadeOnDelete();
            $table->foreignId('pe_credential_id')->nullable()->constrained('pe_emp_credentials')->nullOnDelete();
            $table->string('credential_type');
            $table->string('credential_number')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->foreignId('jurisdiction_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('jurisdiction_state_id')->nullable()->constrained('state_provinces')->nullOnDelete();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bp_emp_credentials');
        Schema::dropIfExists('bp_emp_documents');
        Schema::dropIfExists('bp_emp_tax_data');
        Schema::dropIfExists('bp_job_data');
        Schema::dropIfExists('bp_emp_phones');
        Schema::dropIfExists('bp_emp_addresses');
        Schema::dropIfExists('bp_employees');
        Schema::dropIfExists('pe_emp_credentials');
        Schema::dropIfExists('pe_emp_documents');
        Schema::dropIfExists('pe_emp_tax_data');
        Schema::dropIfExists('pe_job_data');
        Schema::dropIfExists('pe_emp_phones');
        Schema::dropIfExists('pe_emp_addresses');
        Schema::dropIfExists('pe_employees');
    }
};
