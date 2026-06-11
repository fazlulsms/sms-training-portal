@extends('layouts.app')
@section('content')
<div style="max-width:1000px; margin:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="font-size:22px; font-weight:800; color:#111827; margin:0;">Course Categories</h2>
        <a href="/admin/course-categories/create" style="background:#1e3a8a; color:#fff; padding:10px 20px; border-radius:8px; font-weight:700; text-decoration:none; font-size:14px;">
            + Add Category
        </a>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:12px 16px; margin-bottom:16px; color:#166534; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Name</th>
                    <th style="padding:12px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Slug</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Courses</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Order</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Public</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Status</th>
                    <th style="padding:12px 16px; text-align:center; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:13px 16px; font-weight:600; color:#111827;">
                        @if($cat->icon)<span style="margin-right:6px;">{{ $cat->icon }}</span>@endif
                        {{ $cat->name }}
                    </td>
                    <td style="padding:13px 16px; font-size:13px; color:#6b7280;">{{ $cat->slug }}</td>
                    <td style="padding:13px 16px; text-align:center; font-weight:700;">{{ $cat->courses_count }}</td>
                    <td style="padding:13px 16px; text-align:center;">{{ $cat->display_order }}</td>
                    <td style="padding:13px 16px; text-align:center;">
                        @if($cat->is_public)
                        <span style="background:#dcfce7; color:#166534; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;">Yes</span>
                        @else
                        <span style="background:#f3f4f6; color:#6b7280; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;">No</span>
                        @endif
                    </td>
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="background:{{ $cat->status=='active'?'#dcfce7':'#f3f4f6' }}; color:{{ $cat->status=='active'?'#166534':'#6b7280' }}; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:700;">
                            {{ ucfirst($cat->status) }}
                        </span>
                    </td>
                    <td style="padding:13px 16px; text-align:center;">
                        <a href="/admin/course-categories/edit/{{ $cat->id }}" style="color:#1e3a8a; font-weight:600; font-size:13px; text-decoration:none; margin-right:12px;">Edit</a>
                        <a href="/admin/course-categories/delete/{{ $cat->id }}" style="color:#dc2626; font-weight:600; font-size:13px; text-decoration:none;" onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:40px; text-align:center; color:#9ca3af;">No categories yet. <a href="/admin/course-categories/create" style="color:#1e3a8a;">Add the first one.</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
