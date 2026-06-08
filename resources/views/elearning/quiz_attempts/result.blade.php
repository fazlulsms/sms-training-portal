@extends('layouts.app')

@section('content')

<style>
.result-wrap {
    padding: 30px;
    max-width: 1000px;
    margin: auto;
}

.result-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 22px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 24px;
}

.result-header {
    background: #173a8a;
    color: white;
    padding: 22px;
}

.result-header h2 {
    margin: 0;
    color: white;
}

.result-body {
    padding: 25px;
    text-align: center;
}

.score-circle {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
    font-size: 32px;
    font-weight: 800;
    color: #173a8a;
}

.pass-box {
    background: #dcfce7;
    color: #166534;
    padding: 14px;
    border-radius: 10px;
    font-weight: 700;
}

.fail-box {
    background: #fef3c7;
    color: #92400e;
    padding: 14px;
    border-radius: 10px;
    font-weight: 700;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th,
.history-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
}

.history-table th {
    background: #f9fafb;
}

.back-btn {
    display: inline-block;
    background: #0f766e;
    color: white !important;
    padding: 12px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    margin-top: 20px;
}
</style>

<div class="result-wrap">

    <div class="result-card">
        <div class="result-header">
            <h2>Quiz Result</h2>
        </div>

        <div class="result-body">
            <h3>{{ $quiz->title }}</h3>

            <div class="score-circle">
                {{ $score }}%
            </div>

            <p><strong>Total Questions:</strong> {{ $totalQuestions }}</p>
            <p><strong>Correct Answers:</strong> {{ $correctAnswers }}</p>

            @if($score >= 70)
                <div class="pass-box">
                    Congratulations! You passed this quiz.
                </div>
            @else
                <div class="fail-box">
                    You did not pass. Please review the lesson and try again.
                </div>
            @endif
        </div>
    </div>

    <div class="result-card">
        <div class="result-body" style="text-align:left;">
            <h3>Attempt History</h3>

            <table class="history-table">
                <thead>
                    <tr>
                        <th>Attempt</th>
                        <th>Correct</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $history->correct_answers }} / {{ $history->total_questions }}</td>
                            <td>{{ $history->score }}%</td>
                            <td>{{ $history->score >= 70 ? 'Passed' : 'Failed' }}</td>
                            <td>{{ $history->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

<a href="{{ route('participant.elearning-details', ['enrollment' => $enrollment->id]) }}" class="back-btn">
                Back to My Course
            </a>
        </div>
    </div>

</div>

@endsection