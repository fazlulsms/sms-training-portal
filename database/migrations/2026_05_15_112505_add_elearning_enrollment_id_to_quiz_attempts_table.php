public function up(): void
{
    Schema::table('quiz_attempts', function (Blueprint $table) {
        $table->foreignId('elearning_enrollment_id')
            ->nullable()
            ->after('enrollment_id')
            ->constrained('elearning_enrollments')
            ->cascadeOnDelete();

        $table->foreignId('enrollment_id')->nullable()->change();
    });
}