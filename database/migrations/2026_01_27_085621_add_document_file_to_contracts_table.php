<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add document fields to contracts table only if they don't exist
        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'document_path')) {
                $table->string('document_path')->nullable()->after('notes');
            }
        });

        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'document_original_name')) {
                $table->string('document_original_name')->nullable()->after('document_path');
            }
        });

        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'document_uploaded_at')) {
                $table->timestamp('document_uploaded_at')->nullable()->after('document_original_name');
            }
        });

        Schema::table('contracts', function (Blueprint $table) {
            if (! Schema::hasColumn('contracts', 'document_uploaded_by')) {
                $table->foreignId('document_uploaded_by')->nullable()->after('document_uploaded_at')
                    ->constrained('users')->nullOnDelete();
            }
        });

        // Create global_documents table for company-wide documents (Règlement intérieur, etc.)
        if (! Schema::hasTable('global_documents')) {
            Schema::create('global_documents', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type'); // 'reglement_interieur', 'charte_informatique', etc.
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('original_filename');
                $table->string('mime_type');
                $table->integer('file_size');
                $table->boolean('is_active')->default(true);
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Create global_document_acknowledgments for tracking who read the document
        if (! Schema::hasTable('global_document_acknowledgments')) {
            Schema::create('global_document_acknowledgments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('global_document_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('acknowledged_at');
                $table->timestamps();

                $table->unique(['global_document_id', 'user_id'], 'gdoc_ack_docid_userid_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('global_document_acknowledgments');
        Schema::dropIfExists('global_documents');

        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'document_uploaded_by')) {
                $table->dropForeign(['document_uploaded_by']);
                $table->dropColumn('document_uploaded_by');
            }
            if (Schema::hasColumn('contracts', 'document_uploaded_at')) {
                $table->dropColumn('document_uploaded_at');
            }
            if (Schema::hasColumn('contracts', 'document_original_name')) {
                $table->dropColumn('document_original_name');
            }
            if (Schema::hasColumn('contracts', 'document_path')) {
                $table->dropColumn('document_path');
            }
        });
    }
};
