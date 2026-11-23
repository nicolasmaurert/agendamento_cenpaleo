<?php

namespace Tests\Feature;

use App\Mail\AgendamentoConfirmadoMail;
use App\Mail\AgendamentoStatusMail;
use App\Models\Agendamento;
use App\Models\Bloqueio;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AgendamentoDisponibilidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_agendamento_deixa_o_turno_indisponivel_imediatamente(): void
    {
        Mail::fake();
        $data = $this->proximoDiaUtil();

        $response = $this->postJson('/api/agendar', $this->dadosAgendamento($data, 'manha'));

        $response->assertCreated()->assertJsonPath('success', true);

        Mail::assertQueued(AgendamentoConfirmadoMail::class, function (AgendamentoConfirmadoMail $mail): bool {
            return $mail->hasTo('maria@example.com')
                && $mail->dados['responsavel'] === 'Maria da Silva';
        });

        $disponibilidade = $this->getJson('/api/disponibilidade?mes='.$data->format('Y-m'));

        $disponibilidade
            ->assertOk()
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.manha.status", 'agendado')
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.tarde.status", 'livre')
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.tem_disponibilidade", true);

        $this->assertStringContainsString('no-store', (string) $disponibilidade->headers->get('Cache-Control'));
    }

    public function test_bloqueio_deixa_o_turno_indisponivel_na_consulta(): void
    {
        $data = $this->proximoDiaUtil();

        Bloqueio::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'tarde',
            'motivo' => 'Evento institucional',
        ]);

        $this->getJson('/api/disponibilidade?mes='.$data->format('Y-m'))
            ->assertOk()
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.manha.status", 'livre')
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.tarde.status", 'bloqueado');
    }

    public function test_cliente_pode_reservar_manha_e_tarde_no_mesmo_pedido(): void
    {
        Mail::fake();
        $data = $this->proximoDiaUtil();

        $this->postJson('/api/agendar', $this->dadosAgendamento($data, 'ambos'))
            ->assertCreated()
            ->assertJsonPath('agendamento.turno', 'ambos');

        $this->assertDatabaseHas('agendamentos', [
            'data' => $data->format('Y-m-d'),
            'turno' => 'ambos',
            'status' => 'pendente',
        ]);

        $this->getJson('/api/disponibilidade?mes='.$data->format('Y-m'))
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.manha.status", 'agendado')
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.turnos.tarde.status", 'agendado')
            ->assertJsonPath("dias.{$data->format('Y-m-d')}.tem_disponibilidade", false);
    }

    public function test_reserva_dos_dois_turnos_falha_quando_um_deles_esta_ocupado(): void
    {
        Mail::fake();
        $data = $this->proximoDiaUtil();

        Agendamento::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'manha',
            'responsavel' => 'Responsável existente',
            'instituicao' => 'Instituição existente',
            'num_alunos' => 20,
            'telefone' => '(47) 99999-9999',
            'email' => 'existente@example.com',
            'status' => 'confirmado',
        ]);

        $this->postJson('/api/agendar', $this->dadosAgendamento($data, 'ambos'))
            ->assertConflict()
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('agendamentos', 1);
    }

    public function test_administrador_nao_pode_bloquear_turno_com_agendamento_ativo(): void
    {
        $data = $this->proximoDiaUtil();

        Agendamento::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'manha',
            'responsavel' => 'Cliente confirmado',
            'instituicao' => 'Escola confirmada',
            'num_alunos' => 30,
            'telefone' => '(47) 99999-1111',
            'email' => 'cliente@example.com',
            'status' => 'confirmado',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.bloquear'), [
                'data' => $data->format('Y-m-d'),
                'turno' => 'ambos',
                'motivo' => 'Manutenção',
            ])
            ->assertSessionHasErrors([
                'bloqueio' => "Não é possível bloquear {$data->format('d/m/Y')} no turno da manhã: há uma visita de Cliente confirmado com status confirmado. Cancele primeiro o agendamento do cliente e tente novamente.",
            ]);

        $this->assertDatabaseMissing('bloqueios', [
            'data' => $data->format('Y-m-d'),
        ]);
    }

    public function test_administrador_pode_bloquear_turno_depois_de_cancelar_agendamento(): void
    {
        $data = $this->proximoDiaUtil();

        Agendamento::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'manha',
            'responsavel' => 'Cliente cancelado',
            'instituicao' => 'Escola cancelada',
            'num_alunos' => 30,
            'telefone' => '(47) 99999-2222',
            'email' => 'cancelado@example.com',
            'status' => 'cancelado',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->post(route('admin.bloquear'), [
                'data' => $data->format('Y-m-d'),
                'turno' => 'manha',
                'motivo' => 'Manutenção',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bloqueios', [
            'data' => $data->format('Y-m-d'),
            'turno' => 'manha',
        ]);
    }

    public function test_confirmacao_do_administrador_envia_email_ao_cliente(): void
    {
        Mail::fake();
        $agendamento = $this->criarAgendamento('pendente');

        $this->withSession(['admin_logged_in' => true])
            ->put(route('admin.agendamentos.update', $agendamento), [
                'status' => 'confirmado',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('agendamentos', [
            'id' => $agendamento->id,
            'status' => 'confirmado',
            'motivo_cancelamento' => null,
        ]);

        Mail::assertQueued(AgendamentoStatusMail::class, function (AgendamentoStatusMail $mail): bool {
            return $mail->hasTo('cliente@example.com')
                && $mail->tipo === 'confirmacao';
        });
    }

    public function test_negacao_exige_motivo_e_envia_o_motivo_ao_cliente(): void
    {
        Mail::fake();
        $agendamento = $this->criarAgendamento('pendente');

        $this->withSession(['admin_logged_in' => true])
            ->put(route('admin.agendamentos.update', $agendamento), [
                'status' => 'cancelado',
            ])
            ->assertSessionHasErrors('motivo_cancelamento');

        $this->assertDatabaseHas('agendamentos', [
            'id' => $agendamento->id,
            'status' => 'pendente',
        ]);
        Mail::assertNothingQueued();

        $motivo = 'A exposição estará fechada para manutenção preventiva.';

        $this->withSession(['admin_logged_in' => true])
            ->put(route('admin.agendamentos.update', $agendamento), [
                'status' => 'cancelado',
                'motivo_cancelamento' => $motivo,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('agendamentos', [
            'id' => $agendamento->id,
            'status' => 'cancelado',
            'motivo_cancelamento' => $motivo,
        ]);

        Mail::assertQueued(AgendamentoStatusMail::class, function (AgendamentoStatusMail $mail) use ($motivo): bool {
            return $mail->tipo === 'negacao'
                && $mail->dados['motivo_cancelamento'] === $motivo;
        });
    }

    public function test_cancelamento_de_visita_confirmada_envia_motivo_ao_cliente(): void
    {
        Mail::fake();
        $agendamento = $this->criarAgendamento('confirmado');
        $motivo = 'O museu estará reservado para uma atividade institucional.';

        $this->withSession(['admin_logged_in' => true])
            ->put(route('admin.agendamentos.update', $agendamento), [
                'status' => 'cancelado',
                'motivo_cancelamento' => $motivo,
            ])
            ->assertSessionHasNoErrors();

        Mail::assertQueued(AgendamentoStatusMail::class, function (AgendamentoStatusMail $mail) use ($motivo): bool {
            return $mail->hasTo('cliente@example.com')
                && $mail->tipo === 'cancelamento'
                && $mail->dados['motivo_cancelamento'] === $motivo;
        });
    }

    public function test_painel_administrativo_exibe_calendario_com_visitante_e_horario(): void
    {
        $data = $this->proximoDiaUtil();

        Agendamento::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'manha',
            'responsavel' => 'Professora Ana',
            'instituicao' => 'Escola do Contestado',
            'num_alunos' => 35,
            'telefone' => '(47) 98888-7777',
            'email' => 'ana@example.com',
            'status' => 'confirmado',
        ]);

        Bloqueio::create([
            'data' => $data->format('Y-m-d'),
            'turno' => 'tarde',
            'motivo' => 'Manutenção interna',
        ]);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.dashboard', ['mes' => $data->format('Y-m')]))
            ->assertOk()
            ->assertSee('Quem visitará o CENPALEO')
            ->assertSee('Professora Ana')
            ->assertSee('Escola do Contestado')
            ->assertSee('08:30–11:30')
            ->assertSee('id="confirmation-modal"', false)
            ->assertSee('id="confirmation-reason"', false)
            ->assertSee('data-confirm-title="Cancelar agendamento"', false)
            ->assertSee('data-requires-reason="true"', false)
            ->assertSee('data-confirm-title="Remover bloqueio"', false)
            ->assertSee('2xl:grid-cols-[minmax(0,1.8fr)_minmax(420px,0.8fr)]', false)
            ->assertDontSee('return confirm(', false);
    }

    public function test_pagina_publica_usa_novo_rotulo_e_nao_exibe_selo_lgpd_no_cabecalho(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Alunos / Visitantes')
            ->assertSee('Manhã e Tarde')
            ->assertDontSee('Em Conformidade com a LGPD');
    }

    private function proximoDiaUtil(): Carbon
    {
        $data = now()->addDay()->startOfDay();

        while ($data->isWeekend()) {
            $data->addDay();
        }

        return $data;
    }

    private function criarAgendamento(string $status): Agendamento
    {
        return Agendamento::create([
            'data' => $this->proximoDiaUtil()->format('Y-m-d'),
            'turno' => 'manha',
            'responsavel' => 'Cliente de teste',
            'instituicao' => 'Instituição de teste',
            'num_alunos' => 20,
            'telefone' => '(47) 99999-0000',
            'email' => 'cliente@example.com',
            'status' => $status,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dadosAgendamento(Carbon $data, string $turno): array
    {
        return [
            'responsavel' => 'Maria da Silva',
            'instituicao' => 'Universidade do Contestado',
            'data' => $data->format('Y-m-d'),
            'turno' => $turno,
            'num_alunos' => 25,
            'telefone' => '(47) 99999-0000',
            'email' => 'maria@example.com',
            'lgpd' => true,
        ];
    }
}
