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

class AgendamentoConfirmadoMail extends Mailable implements ShouldBeEncrypted, ShouldQueue
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
     *     telefone: string
     * }
     */
    public array $dados;

    /**
     * Create a new message instance.
     */
    public function __construct(Agendamento $agendamento)
    {
        $this->dados = [
            'responsavel' => $agendamento->responsavel,
            'instituicao' => $agendamento->instituicao,
            'data' => $agendamento->data->format('Y-m-d'),
            'turno' => $agendamento->turno,
            'num_alunos' => $agendamento->num_alunos,
            'telefone' => $agendamento->telefone,
        ];

        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitação de visita recebida - CENPALEO',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.agendamento_confirmado',
        );
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
