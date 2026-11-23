<?php

namespace App\Mail;

use App\Models\Agendamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class AgendamentoStatusMail extends Mailable implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [60, 300];

    /**
     * @var array{
     *     responsavel: string,
     *     instituicao: string,
     *     data: string,
     *     turno: string,
     *     num_alunos: int,
     *     motivo_cancelamento: string|null
     * }
     */
    public array $dados;

    /**
     * Create a new message instance.
     */
    public function __construct(Agendamento $agendamento, public string $tipo)
    {
        if (! in_array($tipo, ['confirmacao', 'negacao', 'cancelamento'], true)) {
            throw new InvalidArgumentException('Tipo de e-mail de agendamento inválido.');
        }

        $this->dados = [
            'responsavel' => $agendamento->responsavel,
            'instituicao' => $agendamento->instituicao,
            'data' => $agendamento->data->format('Y-m-d'),
            'turno' => $agendamento->turno,
            'num_alunos' => $agendamento->num_alunos,
            'motivo_cancelamento' => $agendamento->motivo_cancelamento,
        ];

        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->tipo) {
            'confirmacao' => 'Visita confirmada - CENPALEO',
            'negacao' => 'Solicitação de visita não aprovada - CENPALEO',
            'cancelamento' => 'Visita cancelada - CENPALEO',
        };

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: 'emails.agendamento_status');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
