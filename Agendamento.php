<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floresta Muaythai - Agendamento de Aulas</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/imask"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        /* Tema Verde Floresta */
        :root {
            --color-floresta: #10b981;
            /* Verde Esmeralda Vibrante */
            --color-secondary: #064e3b;
            /* Verde Escuro */
            --color-dark-bg: #0c0f17;
            --color-card-bg: #161a22;
            --color-waitlist: #f6ad55;
            /* Laranja/Amarelo para Lista de Espera */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-dark-bg);
            color: #f0f8ff;
            /* Light text */
        }

        .class-card {
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: var(--color-card-bg);
            border-left: 5px solid var(--color-floresta);
            /* Verde Floresta Padrão */
            position: relative;
            /* Necessário para o ícone de agendado */
        }

        .class-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 20px -5px rgba(16, 185, 129, 0.2), 0 6px 10px -3px rgba(16, 185, 129, 0.1);
        }

        /* NOVO: Borda amarela para alunos na lista de espera */
        .card-waitlist-border {
            border-left-color: var(--color-waitlist);
        }

        .btn-schedule {
            background-color: var(--color-floresta);
            /* Verde para agendar */
        }

        .btn-schedule:hover {
            background-color: var(--color-secondary);
        }

        .btn-cancel {
            background-color: #e53e3e;
            /* Red for cancellation */
        }

        .btn-waitlist {
            background-color: var(--color-waitlist);
            /* Amarelo/Laranja para Lista de Espera */
            color: #1a202c;
            /* Texto escuro no botão amarelo */
        }

        .btn-view {
            background-color: #3182ce;
            /* Blue for viewing list */
        }

        .btn-disabled {
            background-color: #4a5568;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .modal-bg {
            background-color: rgba(0, 0, 0, 0.9);
        }

        .logo-text {
            color: var(--color-floresta);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .icon-booked {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--color-floresta);
        }

        /* Feature 2: Destaque visual para o dia selecionado */
        #date-selector option:checked {
            background-color: var(--color-floresta);
            color: #1a202c;
            font-weight: bold;
        }

        /* Sugestão 2: Estilo para dia passado */
        .card-past-day {
            opacity: 0.5;
            filter: grayscale(80%);
            transition: opacity 0.3s;
        }

        .z-80 { z-index: 80; }
        .z-90 { z-index: 90; }
    </style>
</head>

<body class="min-h-screen">

    <!-- Tela de Carregamento -->
    <div id="loading" class="fixed inset-0 flex items-center justify-center modal-bg z-50">
        <div class="text-xl font-semibold flex flex-col items-center p-8 bg-gray-800 rounded-lg shadow-xl">
            <!-- Ícone de Carregamento SVG -->
            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Carregando sistema e perfil do aluno...
        </div>
    </div>

    <div class="container mx-auto p-4 md:p-8">
        <!-- Header -->
        <header class="text-center mb-8 border-b border-gray-700 pb-4">
            <!-- Logo Removido para Layout Limpo -->
            <h1 class="4xl md:text-5xl font-extrabold logo-text mt-2">FLORESTA MUAYTHAI</h1>
            <h2 class="text-2xl font-bold logo-text mt-1 mb-4">Agendamento de Aulas</h2>

            <p class="text-sm text-gray-500 mt-4">Olá, <span id="user-name" class="font-bold text-white">Aluno(a)</span>!</p>

            <!-- ALERTA DA PRÓXIMA AULA AGENDADA (Melhoria A) -->
            <div id="next-class-alert" class="hidden mt-4 p-3 bg-green-900/50 text-green-300 rounded-lg shadow-md border border-green-700">
                <!-- Conteúdo é inserido via JS -->
            </div>

            <!-- Seletor de Data e Display -->
            <div class="flex flex-col md:flex-row justify-center items-center mt-6 space-y-2 md:space-y-0 md:space-x-4">
                <label for="date-selector" class="text-gray-400 text-sm">Ver aulas para:</label>
                <select id="date-selector" class="bg-gray-700 text-white p-2 rounded-lg border border-gray-600 focus:ring-[--color-floresta] focus:border-[--color-floresta]" onchange="changeViewingDate(this.value)"></select>
                <p id="current-date-display" class="text-md font-semibold text-[--color-floresta]"></p>
            </div>

            <!-- FILTRO DE PROFESSOR -->
            <div class="flex justify-center mt-4">
                <select id="teacher-filter-select" class="bg-gray-700 text-white p-2 rounded-lg border border-gray-600 focus:ring-[--color-floresta] focus:border-[--color-floresta]" onchange="reRenderWithFilter()">
                    <option value="">Todos os Professores</option>
                    <!-- Opções preenchidas via JS -->
                </select>
            </div>
        </header>

        <!-- Botões de Gestão (Visível apenas para Admin/Professor) -->
        <div id="admin-controls" class="hidden flex flex-col md:flex-row justify-end gap-3 mb-6 p-4 bg-gray-700/50 rounded-lg border border-gray-600">
            <span class="text-sm text-yellow-300 self-center font-semibold">MODO ADMIN</span>
            <button id="add-template-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-150 ease-in-out text-sm">
                Gerenciar Grade Recorrente (Templates)
            </button>
            <button id="refresh-classes-btn" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-150 ease-in-out text-sm">
                Gerar Aulas do Dia (Manual)
            </button>
            <button id="cleanup-classes-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition duration-150 ease-in-out text-sm">
                Limpar Aulas Expiradas
            </button>
        </div>

        <!-- Botão de Acesso Admin -->
        <div class="flex justify-start mb-6">
            <button id="admin-access-btn" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-1 px-3 rounded-lg shadow-md transition duration-150 ease-in-out text-xs">
                Acesso Professor/Admin
            </button>
        </div>


        <!-- Lista de Aulas do Dia (Diárias) -->
        <main id="classes-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></main>

        <!-- Mensagem de Confirmação/Erro (Custom Modal) -->
        <!-- CORRIGIDO Z-index para z-80 -->
        <div id="message-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-80" onclick="this.classList.add('hidden')">
            <div class="bg-gray-800 p-6 rounded-xl shadow-2xl max-w-sm w-full mx-4" onclick="event.stopPropagation()">
                <h3 id="modal-title" class="text-xl font-bold mb-3"></h3>
                <p id="modal-content" class="text-gray-300"></p>
                <div class="mt-4 flex justify-end">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition" onclick="document.getElementById('message-modal').classList.add('hidden')">
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal 5: Confirmação Customizada (Substitui o confirm()) -->
        <!-- Z-index 90 para garantir que fique sempre no topo de todos os modais de fundo -->
        <div id="confirm-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-90">
            <div class="bg-gray-800 p-6 rounded-xl shadow-2xl max-w-sm w-full mx-4" onclick="event.stopPropagation()">
                <h3 class="text-xl font-bold mb-3 text-red-400">Confirmação Necessária</h3>
                <p id="confirm-modal-content" class="text-gray-300 mb-4">Tem certeza que deseja prosseguir com esta ação?</p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button id="confirm-cancel-btn" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition" onclick="document.getElementById('confirm-modal').classList.add('hidden')">
                        Cancelar
                    </button>
                    <button id="confirm-ok-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Cadastro/Atualização do Nome do Aluno -->
    <div id="profile-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
        <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-lg w-full mx-4" onclick="event.stopPropagation()">
            <h2 class="text-2xl font-bold mb-4 text-[--color-floresta]">Bem-vindo(a) à Floresta!</h2>
            <p class="text-gray-400 mb-6">Para agendar suas aulas, precisamos de algumas informações.</p>
            <form id="profile-form">
                <div class="mb-6">
                    <label for="student-name-input" class="block text-sm font-medium text-gray-400">Nome Completo</label>
                    <input
                        type="text"
                        id="student-name-input"
                        name="nome"
                        required
                        minlength="3"
                        maxlength="100"
                        pattern="[A-Za-zÀ-ÿ\s]+"
                        title="Digite apenas letras e espaços"
                        class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 
           focus:ring-[--color-floresta] focus:border-[--color-floresta]">
                </div>

                <div class="mb-6">
                    <label for="student-celular-input" class="block text-sm font-medium text-gray-400">Celular</label>
                    <input
                        type="tel"
                        id="student-celular-input"
                        name="celular"
                        required
                        minlength="15"
                        maxlength="15"
                        placeholder="(00) 00000-0000"
                        class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 
           focus:ring-[--color-floresta] focus:border-[--color-floresta]">
                </div>

                <div class="mb-6">
                    <label for="student-data-input" class="block text-sm font-medium text-gray-400">Data de Nascimento</label>
                    <input
                        type="date"
                        id="student-data-input"
                        name="data_nascimento"
                        required
                        max="2025-12-31"
                        class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 
           focus:ring-[--color-floresta] focus:border-[--color-floresta]">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-[--color-floresta] hover:bg-[--color-secondary] text-white font-bold py-2 px-4 rounded-lg transition">
                        Salvar e Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Adicionar Modelo de Aula (Template Recorrente) -->
    <div id="add-template-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
        <!-- CORREÇÃO: Adicionado max-h-[90vh] e overflow-y-auto para garantir scroll em telas pequenas -->
        <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <h2 id="template-modal-title" class="2xl font-bold mb-6 text-[--color-floresta]">Gerenciar Grade Recorrente</h2>
            <p class="text-gray-400 mb-4">Essas aulas serão usadas como modelo para a geração diária.</p>
            <form id="add-template-form">
                <!-- ID DE EDIÇÃO REMOVIDO: Apenas para adição de novos templates -->
                <div class="mb-4">
                    <label for="new-class-day" class="block text-sm font-medium text-gray-400">Dia da Semana</label>
                    <select id="new-class-day" required class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Segunda">Segunda-feira</option>
                        <option value="Terça">Terça-feira</option>
                        <option value="Quarta">Quarta-feira</option>
                        <option value="Quinta">Quinta-feira</option>
                        <option value="Sexta">Sexta-feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="new-class-time" class="block text-sm font-medium text-gray-400">Horário</label>
                        <input type="time" id="new-class-time" required class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="new-class-capacity" class="block text-sm font-medium text-gray-400">Capacidade Máxima</label>
                        <input type="number" id="new-class-capacity" required min="1" value="20" class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mb-6">
                    <label for="new-class-teacher" class="block text-sm font-medium text-gray-400">Professor (Kru)</label>
                    <input type="text" id="new-class-teacher" required class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Kru Fúria">
                </div>

                <h3 class="text-lg font-semibold text-gray-300 mb-2">Modelos Existentes:</h3>
                <ul id="templates-list" class="space-y-2 max-h-40 overflow-y-auto p-2 bg-gray-700 rounded-lg text-sm text-gray-300">
                    <!-- Templates serão carregados aqui -->
                    <li class="text-center text-gray-500 italic">Nenhum modelo carregado.</li>
                </ul>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition" onclick="closeTemplateModal()">
                        Fechar
                    </button>
                    <button type="submit" id="save-template-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        Salvar Modelo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Lista de Alunos Agendados -->
    <div id="students-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
        <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
            <h2 id="students-modal-title" class="2xl font-bold mb-4 text-[--color-floresta]">Alunos Agendados</h2>
            <p id="students-modal-details" class="text-gray-400 mb-4"></p>
            <ul id="scheduled-students-list" class="space-y-2 max-h-60 overflow-y-auto p-4 bg-gray-700 rounded-lg text-white">
                <!-- Lista de alunos será carregada aqui -->
            </ul>
            <!-- NOVO: Container para o botão de exportar (corrigindo a multiplicação) -->
            <div id="students-modal-controls" class="flex justify-between items-center mt-6">
                <!-- Botão de exportar será inserido aqui -->
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition" onclick="document.getElementById('students-modal').classList.add('hidden')">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 4: Login Admin/Professor (Chave Secreta) - Corrigido com botão Fechar -->
    <div id="admin-login-modal" class="hidden fixed inset-0 flex items-center justify-center modal-bg z-50">
        <div class="bg-gray-800 p-8 rounded-xl shadow-2xl max-w-sm w-full mx-4" onclick="event.stopPropagation()">
            <h2 class="text-2xl font-bold mb-4 text-yellow-400">Acesso Professor/Admin</h2>
            <p class="text-gray-400 mb-6">Digite a chave secreta para gerenciar a grade de aulas.</p>
            <form id="admin-login-form">
                <div class="mb-6">
                    <label for="admin-key-input" class="block text-sm font-medium text-gray-400">Chave Secreta</label>
                    <input type="password" id="admin-key-input" required class="mt-1 block w-full rounded-lg border-gray-600 bg-gray-700 text-white p-3 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Digite a chave">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition" onclick="document.getElementById('admin-login-modal').classList.add('hidden')">
                        Fechar
                    </button>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-gray-900 font-bold py-2 px-4 rounded-lg transition">
                        Acessar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function $(id) {
            return document.getElementById(id);
        }

        let viewingDateKey = null;
        let currentStudent = null;
        let isAdmin = false;

        const dayNames = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];

        const dateFormatter = new Intl.DateTimeFormat('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long'
        });

        function formatDateForDisplay(dateKey) {
            const parts = dateKey.split('-').map(Number);
            const d = new Date(parts[0], parts[1] - 1, parts[2], 12);
            const today = new Date();
            const todayMid = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12);
            const todayKey = todayMid.toISOString().slice(0, 10);
            const tomorrow = new Date(todayMid);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowKey = tomorrow.toISOString().slice(0, 10);
            if (dateKey === todayKey) return 'Hoje';
            if (dateKey === tomorrowKey) return 'Amanhã';
            return d.toLocaleDateString('pt-BR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }

        function initializeDateSelector() {
            const selector = $('date-selector');
            selector.innerHTML = '';
            const today = new Date();
            const todayMidday = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12);
            for (let i = 0; i < 7; i++) {
                const date = new Date(todayMidday);
                date.setDate(todayMidday.getDate() + i);
                const dateKey = date.toISOString().slice(0, 10);
                const option = document.createElement('option');
                option.value = dateKey;
                option.textContent = `${formatDateForDisplay(dateKey)} (${date.toLocaleDateString('pt-BR', { weekday: 'short' })})`;
                selector.appendChild(option);
            }
            viewingDateKey = selector.value;
            $('current-date-display').textContent = formatDateForDisplay(viewingDateKey);
            selector.onchange = function() {
                viewingDateKey = this.value;
                $('current-date-display').textContent = formatDateForDisplay(viewingDateKey);
                carregarAulas(viewingDateKey);
            };
        }

        async function carregarAulas(dateKey) {
            try {
                const res = await fetch('Aulas/get_aulas.php?data=' + dateKey);
                const aulas = await res.json();
                populateTeacherFilterFromAulas(aulas);
                await renderClasses(aulas);
            } catch (e) {
                console.error('Erro ao carregar aulas', e);
                showMessage('Erro', 'Falha ao carregar aulas.');
            }
        }

        function showMessage(title, content, type = 'info') {
            const modal = document.getElementById('message-modal');
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-title').className = `text-xl font-bold mb-3 ${type === 'error' ? 'text-red-400' : 'text-green-400'}`;
            document.getElementById('modal-content').textContent = content;
            modal.classList.remove('hidden');
        }

        function openProfileModal() {
            $('profile-modal').classList.remove('hidden');
        }

        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = $('student-name-input').value.trim();
            const celular = $('student-celular-input').value;
            const data = $('student-data-input').value;
            if (!name) return;
            if (!celular) return;
            if (!data) return;
            // create or get aluno
            const form = new FormData();
            form.append('nome', name);
            form.append('celular', celular);
            form.append('data', data);
            const res = await fetch('Alunos/save_or_get_aluno.php', {
                method: 'POST',
                body: form
            });
            const j = await res.json();
            if (j.success) {
                currentStudent = {
                    id: j.aluno_id,
                    name: j.nome
                };
                localStorage.setItem('floresta_student', JSON.stringify(currentStudent));
                $('user-name').textContent = j.nome;
                $('profile-modal').classList.add('hidden');
                carregarAulas(viewingDateKey);
                $('loading').classList.add('hidden');
            } else {
                showMessage('Erro', 'Não foi possível salvar o perfil.');
            }
        });

        function restoreStudentFromStorage() {
            const s = localStorage.getItem('floresta_student');
            if (s) {
                try {
                    currentStudent = JSON.parse(s);
                    $('user-name').textContent = currentStudent.name;
                } catch (e) {}
            }
        }

        // Render simplified classes using same card style as HTML expects.
        // Expects aulas array with fields: id, time, professor, capacidade, confirmed_count, wait_count
        async function renderClasses(classes) {
            const listContainer = document.getElementById('classes-list');
            listContainer.innerHTML = '';

            // CACHE DE DADOS: Salva a lista completa antes de filtrar
            lastReceivedClassesData = classes;

            const today = new Date();
            const todayMidday = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12);
            const todayKey = todayMidday.toISOString().slice(0, 10);

            const isViewingToday = viewingDateKey === todayKey;
            const isPastDayOverall = viewingDateKey < todayKey;

            // Filtrar classes baseadas na seleção do professor
            const selectedTeacher = document.getElementById('teacher-filter-select')?.value;
            const filteredClasses = selectedTeacher ?
                classes.filter(cls => cls.professor === selectedTeacher) :
                classes;


            if (filteredClasses.length === 0) {
                const message = isPastDayOverall ?
                    `Não há aulas registradas para **${formatDateForDisplay(viewingDateKey)}** (Dia Passado).` :
                    (viewingDateKey === todayKey ?
                        `Não há aulas agendadas para ${selectedTeacher ? selectedTeacher : 'HOJE'}. Aguarde o professor gerar a grade do dia.` :
                        `Não há aulas geradas para **${formatDateForDisplay(viewingDateKey)}** ${selectedTeacher ? `com ${selectedTeacher}` : ''}. O professor pode gerar manualmente.`);

                listContainer.innerHTML = `<p class="col-span-full text-center text-xl text-gray-500 mt-12 p-8 bg-gray-800 rounded-xl">${message}</p>`;
                return;
            }

            // Classifica as aulas por hora
            filteredClasses.sort((a, b) => a.hora.localeCompare(b.hora));

            let ids = filteredClasses.map(cls => cls.id);
            let form = new FormData();
            form.append('ids', JSON.stringify(ids));
            let res = await fetch('Agendamentos/get_agendamentos.php', {
                method: 'POST',
                body: form
            });
            let j = await res.json();

            for (const cls of filteredClasses) {
                let agendamentos = j.list[cls.id];

                const studentsCount = agendamentos.filter(x => x.status === 'confirmado').length;
                const waitlistCount = agendamentos.filter(x => x.status === 'espera').length;

                // NOVO CÁLCULO DE DIA/HORA PASSADO
                let buttonDisabled = false;
                let isCardInactive = isPastDayOverall; // Inativo se o dia for anterior a hoje

                if (isViewingToday) {
                    const now = new Date();
                    // Cria objeto de tempo para a aula de hoje
                    const [classHours, classMinutes] = cls.hora.split(':').map(Number);
                    const classTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), classHours, classMinutes);

                    // Se a aula de HOJE já passou (tempo atual > tempo da aula)
                    if (now > classTime) {
                        isCardInactive = true;
                    }
                }

                // Verifica status do usuário
                const isBooked = agendamentos.some(x => x.status === 'confirmado' && x.aluno_id === currentStudent.id);
                const isWaitlisted = agendamentos.some(x => x.status === 'espera' && x.aluno_id === currentStudent.id);
                const isFull = studentsCount >= cls.maxCapacity;

                let buttonText;
                let buttonClass;
                let action = '';

                if (isCardInactive) {
                    buttonText = 'Aula Encerrada';
                    buttonClass = 'btn-disabled';
                    buttonDisabled = true;
                } else if (isBooked) {
                    buttonText = 'Cancelar Agendamento';
                    buttonClass = 'btn-cancel hover:bg-red-700';
                    action = 'cancel';
                } else if (isWaitlisted) { // Usuário na Lista de Espera
                    buttonText = `Na Lista de Espera - (Cancelar)`;
                    buttonClass = 'btn-waitlist hover:bg-yellow-700';
                    action = 'cancel_waitlist';
                } else if (isFull) {
                    buttonText = `Entrar na Lista de Espera (${waitlistCount} na fila)`;
                    buttonClass = 'btn-waitlist hover:bg-yellow-700';
                    action = 'waitlist';
                } else {
                    buttonText = 'Agendar Minha Vaga';
                    buttonClass = 'btn-schedule hover:bg-[--color-secondary]';
                    action = 'book';
                }

                // Desativa agendamento se o aluno não tiver nome cadastrado
                const isProfileMissing = !currentStudent.name || currentStudent.name === 'Aluno(a)';
                if (isProfileMissing && !isCardInactive) {
                    buttonText = 'Complete seu Perfil';
                    buttonClass = 'btn-disabled';
                    buttonDisabled = true;
                }

                // MELHORIA B: Borda do Cartão e Ícone
                let cardClass = 'class-card rounded-xl p-6 shadow-xl flex flex-col justify-between';
                let iconColor = 'text-[--color-floresta]';

                if (isWaitlisted) {
                    cardClass += ' card-waitlist-border';
                    iconColor = 'text-[--color-waitlist]';
                }

                if (isCardInactive) { // Aplica estilo de inativo/passado
                    cardClass += ' card-past-day';
                }

                const bookedIcon = (isBooked || isWaitlisted) && !isCardInactive ?
                    `<svg class="icon-booked w-6 h-6 ${iconColor}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>` :
                    '';

                // Exibição da Lista de Espera
                const waitlistDisplay = waitlistCount > 0 ?
                    `<span class="text-xs text-yellow-400"> +${waitlistCount} na Espera</span>` :
                    '';

                // NOVO: Botão de remoção manual (apenas admin)
                const adminDeleteButton = isAdmin && !isPastDayOverall ? `
                    <button 
                        data-class-id="${cls.id}"
                        class="w-full bg-red-800 hover:bg-red-700 text-white font-bold py-1 px-4 rounded-lg shadow-md transition duration-150 ease-in-out text-xs mt-2"
                        onclick="deleteDailyClassInstance('${cls.id}')">
                        Remover Aula (Admin)
                    </button>
                ` : '';


                const cardHtml = `
                    <div class="${cardClass}">
                        <div>
                            <div class="text-2xl font-extrabold mb-1 text-[--color-floresta]">${cls.hora}</div>
                            <p class="text-gray-300 mb-4 font-light">${cls.data}: <span class="font-semibold">${cls.professor}</span></p>

                            <div class="mb-4">
                                <span class="text-sm font-medium ${isFull ? 'text-red-400' : 'text-green-400'}">
                                    Vagas Ocupadas: ${studentsCount} / ${cls.capacidade}
                                </span>
                                ${waitlistDisplay}
                                <div class="w-full bg-gray-600 rounded-full h-2 mt-1">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: ${Math.min(100, (studentsCount / cls.capacidade) * 100)}%; background-color: ${isFull ? '#e53e3e' : '#38a169'};"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-2">
                            <button
                                data-class-id="${cls.id}"
                                data-action="${action}"
                                ${buttonDisabled ? 'disabled' : ''}
                                class="w-full text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out ${buttonClass}"
                                onclick="handleBooking(event)">
                                ${buttonText}
                            </button>
                            <button
                                data-class-id="${cls.id}"
                                data-class-time="${cls.hora}"
                                data-class-teacher="${cls.professor}"
                                class="w-full text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out btn-view hover:bg-blue-700 text-sm"
                                onclick="viewStudentsList(event)">
                                Ver Alunos Agendados (${studentsCount + waitlistCount})
                            </button>
                            ${adminDeleteButton}
                        </div>
                        ${bookedIcon}
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', cardHtml);
            }
        }

        // Booking handler
        async function handleBooking(event) {
            const btn = event.currentTarget;
            const classId = btn.getAttribute('data-class-id');
            const action = btn.getAttribute('data-action');
            if (!currentStudent) {
                openProfileModal();
                return;
            }
            btn.disabled = true;
            const form = new FormData();
            form.append('aula_id', classId);
            form.append('aluno_id', currentStudent.id);
            form.append('action', action);
            try {
                const res = await fetch('Agendamentos/agendar.php', {
                    method: 'POST',
                    body: form
                });
                const j = await res.json();
                if (j.success) {
                    showMessage('Sucesso', j.mensagem);
                    carregarAulas(viewingDateKey);
                } else {
                    showMessage('Erro', j.mensagem || 'Falha ao agendar');
                }
            } catch (e) {
                console.error(e);
                showMessage('Erro', 'Falha ao comunicar com o servidor.');
            } finally {
                btn.disabled = false;
            }
        }

        // View students modal
        async function viewStudentsList(event) {
            const classId = event.currentTarget.getAttribute('data-class-id');
            const data = event.currentTarget.getAttribute('data-class-time');
            const professor = event.currentTarget.getAttribute('data-class-teacher');

            const res = await fetch('Alunos/listar_alunos.php?aula_id=' + classId);
            const j = await res.json();
            if (!j.success) {
                showMessage('Erro', 'Falha ao obter lista.');
                return;
            }
            const students = j.confirmed || [];
            const waitlist = j.waitlist || [];
            $('students-modal-title').textContent = `${data} - ${formatDateForDisplay(viewingDateKey)}`;
            $('students-modal-details').textContent = `Professor: ${professor} | Confirmados: ${students.length} | Espera: ${waitlist.length}`;
            const ul = $('scheduled-students-list');
            ul.innerHTML = '';
            students.forEach(s => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center p-2 bg-gray-700 rounded';
                li.innerHTML = `<span>${s.nome}</span>`;
                ul.appendChild(li);
            });
            waitlist.forEach((s, idx) => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center p-2 bg-gray-700 rounded';
                li.innerHTML = `<span>${s.nome} (Espera ${idx+1}º)</span>`;
                ul.appendChild(li);
            });
            $('students-modal').classList.remove('hidden');
        }

        // Admin controls
        document.getElementById('admin-login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const key = $('admin-key-input').value.trim();
            const form = new FormData();
            form.append('key', key);
            const res = await fetch('Utils/login_admin.php', {
                method: 'POST',
                body: form
            });
            const j = await res.json();
            if (j.success) {
                isAdmin = true;
                updateAdminControlsVisibility();
                document.getElementById('admin-login-modal').classList.add('hidden');
                showMessage("Sucesso", "Acesso de Professor/Admin concedido. Executando limpeza de dados...", 'info');

                cleanupExpiredClasses();

                document.getElementById('admin-access-btn').textContent = "Sair do Acesso Admin";
                document.getElementById('admin-access-btn').onclick = logOutAdmin;
            } else {
                showMessage('Erro', 'Chave incorreta.');
            }
        });

        async function cleanupExpiredClasses() {
            const res = await fetch('Aulas/limpar_aulas.php');
            const j = await res.json();
            //showMessage(j.success ? 'Sucesso' : 'Erro', j.mensagem || '');
            if (j.success) {
                showMessage('Limpeza Concluída', `Aulas expiradas (dias anteriores ao dia atual) foram removidas do banco de dados.`, 'info');
            } else {
                showMessage('Erro', j.mensagem, 'error');
            }
        }

        async function logOutAdmin() {
            isAdmin = false;
            const form = new FormData();
            const res = await fetch('Utils/logout_admin.php', {
                method: 'POST',
                body: form
            });
            updateAdminControlsVisibility();
            document.getElementById('admin-access-btn').textContent = "Acesso Professor/Admin";
            document.getElementById('admin-access-btn').onclick = () => document.getElementById('admin-login-modal').classList.remove('hidden');
            showMessage("Sessão Finalizada", "Você saiu do modo de Professor/Admin.", 'info');
        }

        function updateAdminControlsVisibility() {
            const controls = document.getElementById('admin-controls');
            if (isAdmin) {
                controls.classList.remove('hidden');
            } else {
                controls.classList.add('hidden');
            }
        }

        document.getElementById('admin-access-btn').addEventListener('click', () => {
            document.getElementById('admin-login-modal').classList.remove('hidden');
        });

        document.getElementById('add-template-btn').addEventListener('click', () => {
            if (!isAdmin) {
                return showMessage("Acesso Negado", "Você precisa de acesso de Professor/Admin para gerenciar a grade.", 'error');
            }
            // Abre o modal no modo 'Criação' (limpa tudo primeiro)
            closeTemplateModal();
            renderTemplateListFromCache();
            document.getElementById('add-template-modal').classList.remove('hidden');
        });

        document.getElementById('refresh-classes-btn').addEventListener('click', async () => {
            if (!isAdmin) return showMessage('Erro', 'Acesso admin necessário.');
            generateClassesForDate(viewingDateKey, true, false);
        });

        window.closeTemplateModal = function() {
            document.getElementById('add-template-modal').classList.add('hidden');
            document.getElementById('add-template-form').reset();
        }

        document.getElementById('cleanup-classes-btn').addEventListener('click', async () => {
            if (!isAdmin) return showMessage('Erro', 'Acesso admin necessário.');
            cleanupExpiredClasses();
            carregarAulas(viewingDateKey);
        });

        async function checkDuplicateTemplate(day, time) {
            form = new FormData();
            form.append('dia_semana', day);
            form.append('hora', time);
            res = await fetch('Template/listar_templates.php', {
                method: 'POST',
                body: form
            });
            j = await res.json();
            let templates = j.list;
            return templates.length !== 0;
        }

        document.getElementById('add-template-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const day = document.getElementById('new-class-day').value;
            const time = document.getElementById('new-class-time').value;
            const teacher = document.getElementById('new-class-teacher').value.trim();
            const capacity = parseInt(document.getElementById('new-class-capacity').value, 10);
            const diaId = dayNames.indexOf(day);

            // Feature 3: Validação de Duplicatas (apenas para criação)
            if (await checkDuplicateTemplate(diaId, time)) {
                return showMessage("Erro de Duplicidade", `Já existe um modelo de aula agendado para ${day} às ${time}.`, 'error');
            }

            try {
                const form = new FormData();
                form.append('dia', diaId);
                form.append('hora', time);
                form.append('professor', teacher);
                form.append('capacidade', capacity);
                const res = await fetch('Template/salvar_template.php', {
                    method: 'POST',
                    body: form
                });
                const j = await res.json();

                showMessage("Sucesso", `O novo modelo de aula de ${day} às ${time} foi salvo!`);

                closeTemplateModal();

                // NOVO FLUXO: TENTA GERAR A AULA IMEDIATAMENTE APÓS O SALVAMENTO.
                const [year, month, dayOfMonth] = viewingDateKey.split('-').map(Number);
                const viewingDate = new Date(year, month - 1, dayOfMonth);
                const currentDayName = dayNames[viewingDate.getDay()];

                if (currentDayName === day) {
                    // Tenta adicionar a aula ao dia visível (se não houver outra aula de mesmo ID de template)
                    await generateClassesForDate(viewingDateKey, true, false);
                } else {
                    showMessage("Sucesso e Aviso", "Novo modelo salvo! Ele aparecerá automaticamente nos próximos dias.", 'info');
                }

            } catch (e) {
                console.error("Erro ao salvar modelo de aula: ", e);
                showMessage("Erro", "Falha ao salvar modelo. Tente novamente.", 'error');
            }
        });

        async function generateClassesForDate(dateKey, force = false, forceRegeneration = false) {
            const [year, month, day] = dateKey.split('-').map(Number);
            const date = new Date(year, month - 1, day);
            const dayName = dayNames[date.getDay()];

            // 1. Busca Aulas Existentes
            let form = new FormData();
            const todayKey = date.toISOString().slice(0, 10);
            form.append('data', todayKey);
            let res = await fetch('Aulas/get_aulas_date.php', {
                method: 'POST',
                body: form
            });
            let j = await res.json();
            let aulas = j.list;

            // 2. Verifica todos os Templates para o dia
            form = new FormData();
            form.append('dia_semana', date.getDay());
            res = await fetch('Template/listar_templates.php', {
                method: 'POST',
                body: form
            });
            j = await res.json();
            let templates = j.list;

            if (templates.length === 0) {
                if (force) {
                    showMessage("Aviso", `Nenhum modelo de aula encontrado para ${dayName}.`, 'info');
                }
                return;
            }

            // 3. Filtra apenas os templates que AINDA NÃO EXISTEM
            const templatesToCreate = templates.filter(template => !aulas.some(x => x.hora == template.hora));

            if (templatesToCreate.length === 0) {
                if (force) {
                    // Aviso não-destrutivo
                    showMessage("Aviso", "Todas as aulas desta grade já estão geradas para este dia.", 'info');
                }
                return;
            }

            // 4. Cria as Aulas Faltantes
            let createdCount = 0;
            for (const template of templatesToCreate) {
                form = new FormData();
                form.append('data', dateKey);
                form.append('hora', template.hora);
                form.append('professor', template.professor);
                form.append('capacidade', template.capacidade);
                form.append('template_id', template.id);
                res = await fetch('Aulas/gerar_aula.php', {
                    method: 'POST',
                    body: form
                });
                j = await res.json();
                createdCount++;
            }

            if (force) {
                showMessage("Aulas Atualizadas", `${createdCount} aulas novas foram adicionadas à grade de ${formatDateForDisplay(dateKey)}.`, 'info');
            }

            carregarAulas(viewingDateKey);
        }

        async function renderTemplateListFromCache() {
            const templatesList = document.getElementById('templates-list');
            templatesList.innerHTML = '';

            const form = new FormData();
            const res = await fetch('Template/listar_templates.php', {
                method: 'POST',
                body: form
            });
            const j = await res.json();
            const templatesData = j.list;

            if (templatesData.length === 0) {
                templatesList.innerHTML = '<li class="text-center text-gray-500 italic">Nenhum modelo recorrente definido.</li>';
                return;
            }

            // Ordenar templates
            templatesData.sort((a, b) => {
                const dayA = a.dia_semana;
                const dayB = b.dia_semana;
                if (dayA !== dayB) return dayA - dayB;
                return a.hora.localeCompare(b.hora);
            });

            let count = 0;
            templatesData.forEach(template => {
                const countDisplay = count > 0 ? `<span class="text-xs text-green-400"> (Gerado: ${count})</span>` : '';
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center bg-gray-600 p-2 rounded';
                li.innerHTML = `
                    <span>${dayNames[template.dia_semana]} ${template.hora} (${template.professor}, ${template.capacidade} vagas)${countDisplay}</span>
                    <div class="flex items-center space-x-2">
                        <!-- Botão Deletar -->
                        <button type="button" data-id="${template.id}" class="text-red-400 hover:text-red-300 font-bold" onclick="handleTemplateDeleteClick(event)">
                            X
                        </button>
                    </div>
                `;
                templatesList.appendChild(li);
            });
        }

        window.deleteDailyClassInstance = async function(classId) {
            if (!isAdmin) return showMessage("Acesso Negado", "Acesso Admin necessário.", 'error');

            showConfirm("Tem certeza que deseja remover ESTA INSTÂNCIA de aula? Isso não afeta o modelo recorrente, mas a remove imediatamente do calendário dos alunos.", async () => {
                try {
                    let form = new FormData();
                    form.append('id', classId);
                    res = await fetch('Aulas/excluir_aula.php', {
                        method: 'POST',
                        body: form
                    });
                    j = await res.json();

                    // Força a atualização imediata da grade
                    carregarAulas(viewingDateKey);

                    showMessage("Sucesso", "Instância de aula removida permanentemente do calendário.", 'info');
                } catch (e) {
                    showMessage("Erro", "Falha ao remover instância de aula.", 'error');
                    console.error("Erro ao deletar instância diária:", e);
                }
            });
        }

        /**
         * Função que chama o modal de confirmação para exclusão.
         */
        window.handleTemplateDeleteClick = (event) => {
            if (!isAdmin) return showMessage("Acesso Negado", "Acesso Admin necessário para exclusão.", 'error');
            document.getElementById('add-template-modal').classList.add('hidden');
            const templateId = event.currentTarget.getAttribute('data-id');

            // CORREÇÃO: Abre o modal de confirmação. A função showConfirm agora esconde o modal de templates.
            showConfirm("Tem certeza que deseja remover este modelo de aula? As aulas já criadas serão CANCELADAS a partir de hoje.", () => {
                deleteTemplate(templateId);
                return;
            });

            showMessage("Aviso", `Template não foi excluido!`);
        }

        async function deleteTemplate(templateId) {
            const form = new FormData();
            form.append('id', templateId);
            const res = await fetch('Template/excluir_template.php', {
                method: 'POST',
                body: form
            });
            const j = await res.json();

            if (j.success) {
                showMessage("Sucesso", `Template excluido com sucesso!`);
            } else {
                showMessage('Erro', 'Não foi possível excluir o template.');
            }
        }

        function showConfirm(message, onConfirm) {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-modal-content').textContent = message;

            const confirmBtn = document.getElementById('confirm-ok-btn');
            const cancelBtn = document.getElementById('confirm-cancel-btn');

            confirmBtn.onclick = () => {
                // Esconde o modal de templates antes de confirmar (para o caso de exclusão)
                document.getElementById('add-template-modal').classList.add('hidden');
                modal.classList.add('hidden');
                onConfirm();
            };
            cancelBtn.onclick = () => {
                modal.classList.add('hidden');
            };

            modal.classList.remove('hidden');
        }

        function populateTeacherFilterFromAulas(aulas) {
            const select = document.getElementById('teacher-filter-select');
            const currentSelection = select.value;
            select.innerHTML = '<option value="">Todos os Professores</option>';

            const teachers = new Set(aulas.map(t => t.professor).filter(name => name && name.trim() !== ''));

            Array.from(teachers).sort().forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher;
                option.textContent = teacher;
                if (teacher === currentSelection) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        window.reRenderWithFilter = function() {
            // Apenas re-renderiza o cache (lastReceivedClassesData) com o novo filtro aplicado.
            renderClasses(lastReceivedClassesData);
        }

        // Init
        initializeDateSelector();
        restoreStudentFromStorage();
        // If no student, show profile modal
        if (!currentStudent || currentStudent.id == "0") {
            $('profile-modal').classList.remove('hidden');
        } else {
            carregarAulas(viewingDateKey);
            $('loading').classList.add('hidden');
        }

        function aplicarMascaraCelular(selector) {
            const input = document.querySelector(selector);
            if (!input) return;

            IMask(input, {
                mask: '(00) 00000-0000'
            });
        }

        // Chama a função após o carregamento da página
        document.addEventListener('DOMContentLoaded', () => {
            aplicarMascaraCelular('#student-celular-input');
        });
    </script>

</body>

</html>