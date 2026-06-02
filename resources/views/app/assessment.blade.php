<x-layouts.app title="Asesmen">
    <x-slot:heading>Asesmen Karir</x-slot:heading>

    <div class="max-w-3xl mx-auto">
        {{-- Flash message for draft save --}}
        @if(session('success'))
            <div class="alert alert-neutral mb-6" id="flash-message">
                <div class="flex gap-2.5 items-center">
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="mb-6" id="main-progress">
            <p class="text-sm mb-2" style="color:var(--muted);">
                Pertanyaan berbasis skenario sehari-hari. Tidak ada jawaban benar atau salah.
                Kamu bisa menjeda kapan saja dan melanjutkan nanti.
            </p>
            <x-progress-bar :value="0" :max="$total" label="Progres Asesmen" />
        </div>

        <form method="POST" action="{{ route('assessment.submit') }}" id="assessment-form" class="flex flex-col gap-6">
            @csrf
            @foreach($questions as $q)
                <x-assessment-question
                    :prompt="$q['prompt']"
                    :context="$q['context']"
                    :options="$q['options']"
                    :selectedOption="$savedAnswers[$q['id']] ?? null"
                    :step="$q['id']"
                    :total="$total"
                    :questionId="$q['id']"
                    :type="$q['type']" />
            @endforeach

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary" id="btn-submit">Lanjut →</button>
                <button type="button" class="btn btn-ghost" id="btn-save-draft">Jeda & Simpan</button>
                <button type="button" class="btn btn-ghost" id="btn-skip-all" style="margin-left:auto;color:var(--muted);">Lewati Semua →</button>
            </div>
        </form>

        {{-- Hidden form for saving draft --}}
        <form method="POST" action="{{ route('assessment.saveDraft') }}" id="draft-form" style="display:none;">
            @csrf
        </form>

        <x-privacy-notice
            title="Tentang data asesmen"
            body="Jawaban asesmen digunakan hanya untuk menghitung kecocokan karir. Jawaban individual tidak ditampilkan di mana pun, termasuk admin."
            variant="compact"
            class="mt-6" />
    </div>

    {{-- Script Real-time Progress Bar + Button Handlers --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('assessment-form');
        if (!form) return;

        const questions = form.querySelectorAll('.card');
        const totalQuestions = questions.length;
        
        const mainProgressContainer = document.getElementById('main-progress');
        const mainProgressText = mainProgressContainer ? mainProgressContainer.querySelector('.flex.justify-between span:last-child') : null;
        const mainProgressFill = mainProgressContainer ? mainProgressContainer.querySelector('.progress-fill') : null;

        function updateProgress() {
            let answered = 0;
            
            questions.forEach((card) => {
                const checkedRadio = card.querySelector('input[type="radio"]:checked');
                const textarea = card.querySelector('textarea');
                
                let isAnswered = false;
                if (checkedRadio) isAnswered = true;
                if (textarea && textarea.value.trim() !== '') isAnswered = true;
                
                if (isAnswered) answered++;
                
                const smallBarFill = card.querySelector('.progress-fill');
                if (smallBarFill) {
                    smallBarFill.style.width = isAnswered ? '100%' : '0%';
                    if (isAnswered) {
                        smallBarFill.classList.add('progress-fill-success');
                    } else {
                        smallBarFill.classList.remove('progress-fill-success');
                    }
                }
            });
            
            if (mainProgressFill && mainProgressText) {
                const pct = totalQuestions > 0 ? Math.round((answered / totalQuestions) * 100) : 0;
                mainProgressFill.style.width = pct + '%';
                mainProgressText.textContent = pct + '%';
                
                if (pct === 100) {
                    mainProgressFill.classList.add('progress-fill-success');
                } else {
                    mainProgressFill.classList.remove('progress-fill-success');
                }
            }
        }

        form.addEventListener('change', updateProgress);
        form.addEventListener('input', updateProgress);
        
        // Initial call
        updateProgress();

        /* ── Jeda & Simpan: copy answers to draft form and submit ── */
        const btnSaveDraft = document.getElementById('btn-save-draft');
        const draftForm = document.getElementById('draft-form');

        if (btnSaveDraft && draftForm) {
            btnSaveDraft.addEventListener('click', function() {
                // Remove old answer inputs from draft form
                draftForm.querySelectorAll('input[name^="answers"]').forEach(el => el.remove());

                // Copy all answered values into the draft form
                const allInputs = form.querySelectorAll('input[type="radio"]:checked, textarea');
                allInputs.forEach(function(input) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = input.name;
                    hidden.value = input.value;
                    draftForm.appendChild(hidden);
                });

                draftForm.submit();
            });
        }

        /* ── Lewati Semua: submit form as-is (unanswered = nullable) ── */
        const btnSkipAll = document.getElementById('btn-skip-all');

        if (btnSkipAll) {
            btnSkipAll.addEventListener('click', function() {
                window.showCustomConfirm(
                    'Lewati semua pertanyaan yang belum dijawab dan lihat hasil? Jawaban yang sudah diisi tetap dihitung.',
                    function() {
                        form.submit();
                    }
                );
            });
        }

        /* ── Auto-hide flash message ── */
        const flash = document.getElementById('flash-message');
        if (flash) {
            setTimeout(function() {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(function() { flash.remove(); }, 500);
            }, 4000);
        }
    });
    </script>
</x-layouts.app>

