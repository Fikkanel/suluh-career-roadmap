<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProgressContextNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $crs;
    public $contextLevel;
    public $messageType;

    /**
     * Create a new notification instance.
     */
    public function __construct($crs, $contextLevel, $messageType)
    {
        $this->crs = $crs;
        $this->contextLevel = $contextLevel;
        $this->messageType = $messageType; // 'encouragement', 'milestone', 'stagnant'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Keeping it in-app for now per PRD
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messages = [
            'low' => [
                'encouragement' => "Langkah kecil tetaplah langkah maju. Kamu sudah mencapai CRS {$this->crs}%. Jangan ragu untuk eksplorasi!",
                'milestone' => "Wow! Kamu mencapai {$this->crs}% CRS. Ini bukti nyata usahamu. Terus semangat!",
                'stagnant' => "Bingung mau lanjut ke mana? Tidak masalah. Berhenti sejenak juga bagian dari perjalanan.",
            ],
            'medium' => [
                'encouragement' => "Progress kamu stabil di {$this->crs}%. Tetap fokus pada tujuanmu.",
                'milestone' => "Hebat! Kamu telah menyentuh angka {$this->crs}% kesiapan karir. Pertahankan momentum ini.",
                'stagnant' => "Sudah beberapa hari sejak kamu membuka roadmap-mu. Ayo cek lagi jika kamu punya waktu luang.",
            ],
            'high' => [
                'encouragement' => "Kamu hampir siap menembus industri. Teruskan progress {$this->crs}%-mu.",
                'milestone' => "Pencapaian luar biasa! CRS {$this->crs}% menunjukkan dedikasi profesionalmu yang tinggi.",
                'stagnant' => "Jangan biarkan momentummu hilang. Industri sedang menunggumu.",
            ]
        ];

        // Fallback safety
        $context = in_array($this->contextLevel, ['low', 'medium', 'high']) ? $this->contextLevel : 'medium';
        $type = in_array($this->messageType, ['encouragement', 'milestone', 'stagnant']) ? $this->messageType : 'encouragement';

        $text = $messages[$context][$type];

        return [
            'crs' => $this->crs,
            'context_level' => $this->contextLevel,
            'type' => $this->messageType,
            'message' => $text,
            'url' => route('dashboard'),
        ];
    }
}
