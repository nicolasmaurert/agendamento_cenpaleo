<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização do agendamento - CENPALEO</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 24px; color: #27324a;">
    @php
        $confirmado = $tipo === 'confirmacao';
        $titulo = match ($tipo) {
            'confirmacao' => 'Sua visita foi confirmada',
            'negacao' => 'Sua solicitação não foi aprovada',
            default => 'Sua visita foi cancelada',
        };
        $cor = $confirmado ? '#087f8c' : '#b42318';
        $fundoStatus = $confirmado ? '#e6f7f5' : '#fff0ee';
    @endphp

    <div style="max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 28px rgba(24, 35, 58, 0.08);">
        <div style="background: #18233a; padding: 28px 32px; text-align: center;">
            <p style="margin: 0; color: #68d3d8; font-size: 12px; font-weight: bold; letter-spacing: 1.6px; text-transform: uppercase;">CENPALEO</p>
            <h1 style="margin: 8px 0 0; color: #ffffff; font-size: 24px;">{{ $titulo }}</h1>
        </div>

        <div style="padding: 32px;">
            <p style="margin-top: 0;">Olá, <strong>{{ $dados['responsavel'] }}</strong>!</p>

            @if($confirmado)
                <p>Sua solicitação foi analisada e a visita ao CENPALEO está confirmada. Esperamos vocês na data e no turno abaixo.</p>
            @elseif($tipo === 'negacao')
                <p>Após a análise da equipe, não foi possível aprovar a visita solicitada. Você poderá consultar o calendário e solicitar outra data disponível.</p>
            @else
                <p>Informamos que a visita anteriormente confirmada precisou ser cancelada pela equipe do CENPALEO.</p>
            @endif

            <table style="width: 100%; border-collapse: collapse; margin: 24px 0; font-size: 14px;">
                <tr>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; color: #667085; width: 42%;">Instituição</td>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; font-weight: bold;">{{ $dados['instituicao'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; color: #667085;">Data</td>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; font-weight: bold;">{{ \Carbon\Carbon::parse($dados['data'])->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; color: #667085;">Turno</td>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; font-weight: bold;">{{ $dados['turno'] === 'ambos' ? 'Manhã e Tarde' : ucfirst($dados['turno']) }}</td>
                </tr>
                <tr>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; color: #667085;">Alunos / visitantes</td>
                    <td style="padding: 11px 12px; border-bottom: 1px solid #edf0f5; font-weight: bold;">{{ $dados['num_alunos'] }}</td>
                </tr>
                <tr>
                    <td style="padding: 11px 12px; color: #667085;">Situação</td>
                    <td style="padding: 11px 12px;"><span style="display: inline-block; padding: 5px 10px; border-radius: 999px; background: {{ $fundoStatus }}; color: {{ $cor }}; font-weight: bold;">{{ $confirmado ? 'Confirmada' : ($tipo === 'negacao' ? 'Não aprovada' : 'Cancelada') }}</span></td>
                </tr>
            </table>

            @if(! $confirmado)
                <div style="border-left: 4px solid {{ $cor }}; background: {{ $fundoStatus }}; padding: 16px 18px; border-radius: 8px;">
                    <p style="margin: 0 0 6px; color: {{ $cor }}; font-size: 12px; font-weight: bold; text-transform: uppercase;">Motivo informado pela equipe</p>
                    <p style="margin: 0; line-height: 1.6;">{{ $dados['motivo_cancelamento'] }}</p>
                </div>
            @endif

            <p style="margin: 28px 0 0; color: #667085; font-size: 13px; line-height: 1.6;">Em caso de dúvidas, entre em contato com a equipe do CENPALEO pelos canais oficiais da Universidade do Contestado.</p>
        </div>

        <div style="padding: 18px 32px; background: #f8fafc; color: #98a2b3; text-align: center; font-size: 12px;">
            Exposição CENPALEO — Centro de Pesquisas Paleontológicas
        </div>
    </div>
</body>
</html>
