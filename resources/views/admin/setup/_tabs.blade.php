@php
$tabs = [
    'delivery-methods'  => ['label' => 'Delivery Methods',    'route' => 'setup.delivery-methods.index'],
    'training-models'   => ['label' => 'Training Models',     'route' => 'setup.training-models.index'],
    'program-purposes'  => ['label' => 'Program Purposes',    'route' => 'setup.program-purposes.index'],
    'frameworks'        => ['label' => 'Learning Frameworks', 'route' => 'setup.frameworks.index'],
    'standards'         => ['label' => 'Standards',           'route' => 'setup.standards.index'],
    'industries'        => ['label' => 'Industries',          'route' => 'setup.industries.index'],
    'audiences'         => ['label' => 'Audience Types',      'route' => 'setup.audiences.index'],
];
@endphp
<div style="display:flex; gap:4px; margin-bottom:20px; background:#f8fafc; border-radius:10px; padding:6px; border:1px solid #e5e7eb;">
    @foreach($tabs as $key => $tab)
    <a href="{{ route($tab['route']) }}"
       style="flex:1; text-align:center; padding:8px 12px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none;
              background:{{ ($active ?? '') === $key ? '#1e3a8a' : 'transparent' }};
              color:{{ ($active ?? '') === $key ? '#fff' : '#6b7280' }};">
        {{ $tab['label'] }}
    </a>
    @endforeach
</div>
