<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
    .page { padding: 36px 40px; }
    .header { border-bottom: 2px solid #2d5a27; padding-bottom: 14px; margin-bottom: 22px; }
    .brand { font-size: 20px; font-weight: bold; color: #2d5a27; letter-spacing: -0.5px; }
    .brand-sub { font-size: 10px; color: #666; margin-top: 2px; }
    .export-meta { font-size: 10px; color: #888; margin-top: 6px; }
    .section { margin-bottom: 22px; }
    .section-title { font-size: 13px; font-weight: bold; color: #2d5a27; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px; margin-bottom: 10px; }
    .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; }
    .profile-row { display: flex; gap: 6px; font-size: 11px; }
    .profile-key { color: #666; min-width: 120px; }
    .table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 6px; }
    .table th { background: #f0f4ee; text-align: left; padding: 6px 8px; font-weight: bold; color: #2d5a27; border-bottom: 1px solid #d0d0d0; }
    .table td { padding: 5px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
    .badge-done { background: #d4edda; color: #155724; }
    .badge-progress { background: #fff3cd; color: #856404; }
    .badge-start { background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }
    .riasec-bar { height: 8px; background: #e0e0e0; border-radius: 4px; margin-top: 3px; }
    .riasec-fill { height: 8px; background: #2d5a27; border-radius: 4px; }
    .riasec-row { margin-bottom: 6px; font-size: 11px; }
    .riasec-label { display: flex; justify-content: space-between; }
    .archive-card { border: 1px solid #e0e0e0; border-left: 3px solid #2d5a27; padding: 8px 10px; margin-bottom: 8px; border-radius: 2px; }
    .archive-meta { font-size: 10px; color: #888; margin-top: 3px; }
    .reflection { font-size: 10px; color: #555; font-style: italic; margin-top: 5px; padding-left: 8px; border-left: 2px solid #e0e0e0; }
    .footer { margin-top: 30px; border-top: 1px solid #e0e0e0; padding-top: 10px; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="brand">Suluh</div>
        <div class="brand-sub">Platform Panduan Karir Personal</div>
        <div class="export-meta">Diekspor pada: {{ $data['exported_at'] }} &nbsp;|&nbsp; Untuk: {{ $user->name }}</div>
    </div>

    {{-- Profil --}}
    <div class="section">
        <div class="section-title">Profil Pengguna</div>
        <div class="profile-grid">
            <div class="profile-row"><span class="profile-key">Nama:</span> {{ $data['profile']['name'] }}</div>
            <div class="profile-row"><span class="profile-key">Email:</span> {{ $data['profile']['email'] }}</div>
            <div class="profile-row"><span class="profile-key">Usia:</span> {{ $data['profile']['age_range'] ?? '—' }}</div>
            <div class="profile-row"><span class="profile-key">Pendidikan:</span> {{ $data['profile']['education_level'] ?? '—' }}</div>
            <div class="profile-row"><span class="profile-key">Pengalaman:</span> {{ $data['profile']['work_experience'] ?? '—' }}</div>
            <div class="profile-row"><span class="profile-key">Karir Saat Ini:</span> {{ $data['profile']['current_career'] ?? '—' }}</div>
        </div>
    </div>

    {{-- Hasil Asesmen --}}
    @if(count($data['assessment_results']))
    <div class="section">
        <div class="section-title">Hasil Asesmen Terkini</div>
        @php $latest = $data['assessment_results'][0]; @endphp
        @if($latest['riasec'])
        <p style="font-size:11px;color:#666;margin-bottom:8px;">Skor RIASEC (Holland Code)</p>
        @foreach($latest['riasec'] as $letter => $score)
        <div class="riasec-row">
            <div class="riasec-label"><span>{{ $letter }}</span><span>{{ $score }}%</span></div>
            <div class="riasec-bar"><div class="riasec-fill" style="width:{{ $score }}%;"></div></div>
        </div>
        @endforeach
        @endif
    </div>
    @endif

    {{-- Progress Skill --}}
    @if(count($data['current_progress']))
    <div class="section">
        <div class="section-title">Progress Skill Saat Ini</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Skill</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['current_progress'] as $p)
                <tr>
                    <td>{{ $p['skill'] ?? '—' }}</td>
                    <td>{{ $p['level'] ?? '—' }}</td>
                    <td>
                        @if($p['status'] === 'done')
                            <span class="badge badge-done">Selesai</span>
                        @elseif(in_array($p['status'], ['in_progress','learning']))
                            <span class="badge badge-progress">Berlangsung</span>
                        @else
                            <span class="badge badge-start">Belum mulai</span>
                        @endif
                    </td>
                    <td style="font-size:10px;color:#888;">{{ $p['completed_at'] ? \Carbon\Carbon::parse($p['completed_at'])->format('d M Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Arsip Perjalanan --}}
    @if(count($data['roadmap_archives']))
    <div class="section">
        <div class="section-title">Arsip Perjalanan ({{ count($data['roadmap_archives']) }} pivot)</div>
        @foreach($data['roadmap_archives'] as $a)
        <div class="archive-card">
            <strong>{{ $a['career'] ?? 'Karir tidak diketahui' }}</strong>
            <div class="archive-meta">
                {{ $a['completed_skills'] }} dari {{ $a['total_skills'] }} skill selesai
                &nbsp;·&nbsp;
                {{ $a['archived_at'] ? \Carbon\Carbon::parse($a['archived_at'])->format('d M Y') : '—' }}
            </div>
            @if($a['reflection'])
            <div class="reflection">{{ $a['reflection'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Suluh &nbsp;·&nbsp; Diekspor untuk {{ $user->name }} &nbsp;·&nbsp; {{ now()->format('d M Y H:i') }}
        <br>Suluh tidak menyimpan atau memproses salinan dokumen ini setelah diunduh.
    </div>

</div>
</body>
</html>
