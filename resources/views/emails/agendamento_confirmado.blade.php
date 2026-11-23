<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Solicitação de visita recebida - CENPALEO</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #eef2f6; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #0f172a; font-size: 22px; margin: 0; }
        .header p { color: #059669; font-weight: bold; margin-top: 5px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .info-table td.label { font-weight: 600; color: #64748b; width: 40%; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Exposição Cenpaleo</h1>
            <p>Solicitação de Agendamento Recebida</p>
        </div>

        <p>Olá, <strong>{{ $dados['responsavel'] }}</strong>!</p>
        <p>Recebemos a sua solicitação de agendamento de visita para a Exposição Cenpaleo com sucesso.</p>

        <table class="info-table">
            <tr>
                <td class="label">Instituição:</td>
                <td>{{ $dados['instituicao'] }}</td>
            </tr>
            <tr>
                <td class="label">Data Solicitada:</td>
                <td>{{ \Carbon\Carbon::parse($dados['data'])->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">Turno:</td>
                <td>{{ $dados['turno'] === 'ambos' ? 'Manhã e Tarde' : ucfirst($dados['turno']) }}</td>
            </tr>
            <tr>
                <td class="label">Alunos/Visitantes:</td>
                <td>{{ $dados['num_alunos'] }}</td>
            </tr>
            <tr>
                <td class="label">Telefone de Contato:</td>
                <td>{{ $dados['telefone'] }}</td>
            </tr>
            <tr>
                <td class="label">Status Atual:</td>
                <td><span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Pendente de análise</span></td>
            </tr>
        </table>

        <p>Nossa equipe administrativa analisará a solicitação. Qualquer alteração de status será informada através deste e-mail.</p>

        <div class="footer">
            <p>Exposição Cenpaleo - Centro de Pesquisas Paleontológicas</p>
            <p>Conforme a LGPD, seus dados pessoais são mantidos em segurança exclusivamente para fins organizacionais da visita.</p>
        </div>
    </div>
</body>
</html>
