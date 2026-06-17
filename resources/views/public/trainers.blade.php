@extends('layouts.public')

@section('page-title', 'Our Expert Trainers')
@section('seo-title', 'Expert Trainers â€“ SMS Training Academy')
@section('seo-desc', 'Meet our team of internationally certified trainers specializing in ISO standards, quality management, compliance, and professional development.')

@section('content')
<style>
.page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #042C53 60%, #1d4ed8 100%);
    padding: 60px 0 70px; color: #fff; text-align: center;
}
.page-hero h1 { font-size: 42px; font-weight: 900; margin: 0 0 12px; }
.page-hero p { font-size: 17px; opacity: .8; max-width: 560px; margin: 0 auto; line-height: 1.7; }
.section-wrap { max-width: 1240px; margin: 0 auto; padding: 0 24px; }
.trainers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px;
    padding: 60px 0;
}
.trainer-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
    text-decoration: none;
    display: block;
    color: inherit;
}
.trainer-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(0,0,0,.12); }
.trainer-photo {
    width: 100%; height: 220px; object-fit: cover;
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    display: flex; align-items: center; justify-content: center;
    font-size: 72px; font-weight: 900; color: #6366f1;
}
.trainer-photo img { width: 100%; height: 100%; object-fit: cover; }
.trainer-body { padding: 20px 22px 24px; }
.trainer-name { font-size: 18px; font-weight: 800; color: #111827; margin: 0 0 4px; }
.trainer-desig { font-size: 13.5px; color: #6366f1; font-weight: 600; margin: 0 0 6px; }
.trainer-org { font-size: 13px; color: #6b7280; margin: 0 0 12px; }
.trainer-exp { display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.trainer-bio { font-size: 13.5px; color: #4b5563; line-height: 1.65; margin: 12px 0 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.trainer-link { margin-top: 16px; color: #042C53; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 5px; }
.empty-state { text-align: center; padding: 80px 24px; color: #9ca3af; }
.empty-state h3 { font-size: 22px; font-weight: 700; color: #d1d5db; margin: 16px 0 8px; }
</style>

<section class="page-hero">
    <div class="section-wrap">
        <h1>Our Expert Trainers</h1>
        <p>Internationally certified professionals delivering world-class training and capacity development programs.</p>
    </div>
</section>

<section>
    <div class="section-wrap">
        @if($trainers->isEmpty())
        <div class="empty-state">
            <div style="font-size:60px;">ðŸ‘¨â€ðŸ«</div>
            <h3>Trainer profiles coming soon</h3>
            <p>Our trainer directory is being updated. Please <a href="/corporate-training" style="color:#042C53;">contact us</a> for trainer information.</p>
        </div>
        @else
        <div class="trainers-grid">
            @foreach($trainers as $trainer)
            <a href="{{ route('public.trainer.profile', $trainer->id) }}" class="trainer-card">
                <div class="trainer-photo">
                    @if($trainer->photo)
                    <img src="{{ asset('storage/'.$trainer->photo) }}" alt="{{ $trainer->name }}">
                    @else
                    {{ substr($trainer->name, 0, 1) }}
                    @endif
                </div>
                <div class="trainer-body">
                    <h3 class="trainer-name">{{ $trainer->name }}</h3>
                    @if($trainer->designation)
                    <p class="trainer-desig">{{ $trainer->designation }}</p>
                    @endif
                    @if($trainer->organization)
                    <p class="trainer-org">{{ $trainer->organization }}</p>
                    @endif
                    @if($trainer->experience)
                    <span class="trainer-exp">â± {{ $trainer->experience }}</span>
                    @endif
                    @if($trainer->short_bio)
                    <p class="trainer-bio">{{ $trainer->short_bio }}</p>
                    @endif
                    <div class="trainer-link">View Profile â†’</div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
