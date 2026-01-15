<?php 
$title = 'e-Learning Atlas - SGQ OTI DJ';
$viewFile = __FILE__;
include __DIR__ . '/../layouts/main.php';
?>

<section class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 py-12">
  <div class="max-w-6xl mx-auto px-6">
    <!-- Header -->
    <div class="text-center mb-12">
      <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-3xl shadow-2xl mb-6 animate-bounce">
        <span class="text-5xl">🎓</span>
      </div>
      <h1 class="text-5xl font-bold text-gray-900 mb-4">
        e-Learning Atlas
      </h1>
      <div class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-lg font-bold rounded-full shadow-lg animate-pulse">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        EM BREVE
      </div>
      <p class="text-xl text-gray-600 mt-6 max-w-3xl mx-auto">
        Plataforma completa de aprendizagem e desenvolvimento para sua equipe, com vídeo-aulas, certificados e muito mais!
      </p>
    </div>

    <!-- Features Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
      <!-- Feature 1 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-purple-500">
        <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">📹</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Vídeo-aulas Profissionais</h3>
        <p class="text-gray-600">
          Gestores poderão criar e organizar cursos em vídeo com conteúdo de alta qualidade para capacitação da equipe.
        </p>
      </div>

      <!-- Feature 2 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-pink-500">
        <div class="w-16 h-16 bg-pink-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">📜</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Certificados Digitais</h3>
        <p class="text-gray-600">
          Emissão automática de certificados de aprendizagem e participação personalizados para cada colaborador.
        </p>
      </div>

      <!-- Feature 3 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-blue-500">
        <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">📊</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Acompanhamento e Relatórios</h3>
        <p class="text-gray-600">
          Dashboards completos com progresso individual, cursos concluídos, tempo de estudo e indicadores de performance.
        </p>
      </div>

      <!-- Feature 4 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-indigo-500">
        <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">🎯</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Trilhas de Aprendizagem</h3>
        <p class="text-gray-600">
          Crie jornadas de aprendizado com módulos sequenciais e pré-requisitos para desenvolvimento estruturado.
        </p>
      </div>

      <!-- Feature 5 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-green-500">
        <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">✅</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Avaliações e Quizzes</h3>
        <p class="text-gray-600">
          Sistema de avaliação integrado para validar conhecimento adquirido e garantir efetividade do treinamento.
        </p>
      </div>

      <!-- Feature 6 -->
      <div class="bg-white rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-t-4 border-yellow-500">
        <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
          <span class="text-3xl">🏆</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">Gamificação</h3>
        <p class="text-gray-600">
          Conquistas, badges, rankings e pontuações para engajar e motivar a equipe no processo de aprendizagem.
        </p>
      </div>
    </div>

    <!-- Additional Benefits -->
    <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-3xl p-8 md:p-12 text-white shadow-2xl mb-12">
      <h2 class="text-3xl font-bold mb-6 text-center">O que você poderá fazer?</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
            <span class="text-xl">✨</span>
          </div>
          <div>
            <h4 class="font-bold mb-2">Upload de Vídeos</h4>
            <p class="text-purple-100">Faça upload de vídeos em diversos formatos com suporte a legendas e transcrições.</p>
          </div>
        </div>
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
            <span class="text-xl">📚</span>
          </div>
          <div>
            <h4 class="font-bold mb-2">Biblioteca de Conteúdo</h4>
            <p class="text-purple-100">Organize materiais complementares, PDFs, apresentações e recursos adicionais.</p>
          </div>
        </div>
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
            <span class="text-xl">👥</span>
          </div>
          <div>
            <h4 class="font-bold mb-2">Gestão de Turmas</h4>
            <p class="text-purple-100">Crie turmas, atribua instrutores e acompanhe o progresso de cada grupo.</p>
          </div>
        </div>
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
            <span class="text-xl">🔔</span>
          </div>
          <div>
            <h4 class="font-bold mb-2">Notificações Inteligentes</h4>
            <p class="text-purple-100">Alertas automáticos sobre novos cursos, prazos e conquistas alcançadas.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl text-center">
      <h2 class="text-3xl font-bold text-gray-900 mb-4">Aguarde o Lançamento</h2>
      <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
        Estamos trabalhando para trazer a melhor experiência de aprendizagem para sua equipe. 
        Em breve você terá acesso a todo esse conjunto de ferramentas!
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <div class="flex items-center gap-2 text-gray-700">
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-medium">Interface Intuitiva</span>
        </div>
        <div class="flex items-center gap-2 text-gray-700">
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-medium">Totalmente Responsivo</span>
        </div>
        <div class="flex items-center gap-2 text-gray-700">
          <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-medium">Suporte Completo</span>
        </div>
      </div>
    </div>

    <!-- Voltar -->
    <div class="text-center mt-12">
      <a href="/inicio" class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-gray-700 to-gray-900 text-white font-bold rounded-xl hover:from-gray-800 hover:to-black transition-all shadow-lg hover:shadow-xl">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Voltar ao Início
      </a>
    </div>
  </div>
</section>

<style>
/* Animação adicional */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
</style>
