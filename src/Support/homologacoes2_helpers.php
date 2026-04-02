<?php

if (!function_exists('h2_register_view_context')) {
    function h2_register_view_context(array $context): void
    {
        $GLOBALS['homologacoes2_view_context'] = $context;
    }
}

if (!function_exists('h2_context')) {
    function h2_context(): array
    {
        return $GLOBALS['homologacoes2_view_context'] ?? [];
    }
}

if (!function_exists('getUserById')) {
    function getUserById($id)
    {
        if (!$id) {
            return null;
        }

        $users = h2_context()['users'] ?? [];
        return $users[(int) $id] ?? ['id' => (int) $id, 'nome' => 'Usuário não identificado'];
    }
}

if (!function_exists('getHomologacaoById')) {
    function getHomologacaoById($id)
    {
        if (!$id) {
            return null;
        }

        $homologacoes = h2_context()['homologacoes'] ?? [];
        return $homologacoes[(int) $id] ?? null;
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status): string
    {
        $labels = [
            'aguardando_chegada' => 'Aguardando Chegada',
            'item_recebido' => 'Item Recebido - Aguardando Homologação',
            'em_homologacao' => 'Em Homologação',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
        ];

        return $labels[$status] ?? 'Desconhecido';
    }
}

if (!function_exists('getBadgeClass')) {
    function getBadgeClass($status): string
    {
        $classes = [
            'aguardando_chegada' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
            'item_recebido' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
            'em_homologacao' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'concluida' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
            'cancelada' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400',
        ];

        return $classes[$status] ?? 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
    }
}

if (!function_exists('getBorderClass')) {
    function getBorderClass($status): string
    {
        $classes = [
            'aguardando_chegada' => 'border-amber-500',
            'item_recebido' => 'border-cyan-500',
            'em_homologacao' => 'border-blue-500',
            'concluida' => 'border-emerald-500',
            'cancelada' => 'border-rose-500',
        ];

        return $classes[$status] ?? 'border-slate-500';
    }
}

if (!function_exists('calcularDiasRestantes')) {
    function calcularDiasRestantes($dataPrevista): ?int
    {
        if (!$dataPrevista) {
            return null;
        }

        $hoje = new DateTime(date('Y-m-d'));
        $prevista = new DateTime($dataPrevista);
        $intervalo = $hoje->diff($prevista);

        return (int) $intervalo->format('%R%a');
    }
}

if (!function_exists('getIconForTipo')) {
    function getIconForTipo($tipo): string
    {
        return match ($tipo) {
            'Impressora' => 'ph-printer',
            'Notebook' => 'ph-laptop',
            'Suprimento de Impressora' => 'ph-drop',
            'Peça de Impressora' => 'ph-gear',
            default => 'ph-box',
        };
    }
}

if (!function_exists('getVersaoHomologacao')) {
    function getVersaoHomologacao($id): ?int
    {
        $versions = h2_context()['versions'] ?? [];
        return $versions[(int) $id] ?? null;
    }
}

if (!function_exists('getRotuloVersao')) {
    function getRotuloVersao($versao): string
    {
        if (!$versao) {
            return '—';
        }

        $sufixos = [
            1 => '1ª Homologação',
            2 => '2ª Homologação (Rehom)',
            3 => '3ª Homologação (Rehom)',
            4 => '4ª Homologação (Rehom)',
            5 => '5ª Homologação (Rehom)',
        ];

        return $sufixos[$versao] ?? ($versao . 'ª Homologação');
    }
}
