    /**
     * Get Não Conformidades dashboard data
     */
    public function getNaoConformidadesData()
    {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['user_role'] ?? 'user';
            
            // WHERE clause baseado nas permissões
            $where = [];
            $params = [];
            
            // CONTROLE DE VISUALIZAÇÃO: Usuários não-admin só veem NCs onde são responsáveis ou criadores
            if ($userRole !== 'admin' && $userRole !== 'super_admin') {
                $where[] = "(nc.usuario_responsavel_id = :user_id OR nc.usuario_criador_id = :user_id)";
                $params[':user_id'] = $userId;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // 1. Cards - Contar por status
            $stmtStatus = $this->db->prepare("
                SELECT 
                    status,
                    COUNT(*) as total
                FROM nao_conformidades nc
                $whereClause
                GROUP BY status
            ");
            $stmtStatus->execute($params);
            $statusData = $stmtStatus->fetchAll(\PDO::FETCH_ASSOC);
            
            $pendentes = 0;
            $emAndamento = 0;
            $solucionadas = 0;
            
            foreach ($statusData as $row) {
                if ($row['status'] === 'pendente') $pendentes = (int)$row['total'];
                if ($row['status'] === 'em_andamento') $emAndamento = (int)$row['total'];
                if ($row['status'] === 'solucionada') $solucionadas = (int)$row['total'];
            }
            
            // 2. Top 10 Departamentos com mais NCs
            $stmtDepartamentos = $this->db->prepare("
                SELECT 
                    d.nome as departamento,
                    COUNT(nc.id) as total_ncs,
                    SUM(CASE WHEN nc.status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                    SUM(CASE WHEN nc.status = 'em_andamento' THEN 1 ELSE 0 END) as em_andamento,
                    SUM(CASE WHEN nc.status = 'solucionada' THEN 1 ELSE 0 END) as solucionadas
                FROM nao_conformidades nc
                LEFT JOIN departamentos d ON nc.departamento_id = d.id
                $whereClause
                GROUP BY d.id, d.nome
                HAVING COUNT(nc.id) > 0
                ORDER BY total_ncs DESC
                LIMIT 10
            ");
            $stmtDepartamentos->execute($params);
            $departamentosData = $stmtDepartamentos->fetchAll(\PDO::FETCH_ASSOC);
            
            $departamentosLabels = [];
            $departamentosTotal = [];
            $departamentosPendentes = [];
            $departamentosEmAndamento = [];
            $departamentosSolucionadas = [];
            
            foreach ($departamentosData as $dept) {
                $departamentosLabels[] = $dept['departamento'] ?? 'Sem Departamento';
                $departamentosTotal[] = (int)$dept['total_ncs'];
                $departamentosPendentes[] = (int)$dept['pendentes'];
                $departamentosEmAndamento[] = (int)$dept['em_andamento'];
                $departamentosSolucionadas[] = (int)$dept['solucionadas'];
            }
            
            // Montar resposta
            $response = [
                'success' => true,
                'data' => [
                    'cards' => [
                        'pendentes' => $pendentes,
                        'em_andamento' => $emAndamento,
                        'solucionadas' => $solucionadas
                    ],
                    'departamentos' => [
                        'labels' => $departamentosLabels,
                        'total' => $departamentosTotal,
                        'pendentes' => $departamentosPendentes,
                        'em_andamento' => $departamentosEmAndamento,
                        'solucionadas' => $departamentosSolucionadas
                    ]
                ]
            ];
            
            echo json_encode($response);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar dados de não conformidades: ' . $e->getMessage()
            ]);
        }
    }
