<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-calendar-week text-primary-600"></i>
                Gestionar Bloques de Tiempo
            </h1>
            <p class="text-gray-600 mt-2">
                <span class="font-semibold text-gray-900"><?= htmlspecialchars($shift->name) ?></span> - 
                Define horarios específicos por cada día de la semana
            </p>
        </div>
        <a href="<?= isset($baseUrl) ? $baseUrl : '' ?>/turnos" 
           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Volver
        </a>
    </div>
    
    <!-- Info sobre turnos nocturnos -->
    <div class="mt-4 p-4 bg-indigo-50 border-2 border-indigo-200 rounded-lg flex items-start gap-3">
        <i class="fas fa-moon text-indigo-600 text-2xl mt-1"></i>
        <div>
            <h3 class="font-bold text-indigo-900">Turnos Nocturnos Soportados</h3>
            <p class="text-sm text-indigo-700 mt-1">
                Puedes definir turnos que cruzan la medianoche marcando "🌙 Al día siguiente". 
                Ejemplo: Lunes 21:00 → Martes 06:00 (9 horas)
            </p>
        </div>
    </div>
</div>

<div x-data="shiftBlocksManager()">
    <form method="POST" action="<?= isset($baseUrl) ? $baseUrl : '' ?>/turnos/<?= $shift->id ?>/bloques" @submit.prevent="submitForm">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Plantillas Rápidas -->
        <div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl shadow-sm border-2 border-primary-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-magic text-primary-600"></i>
                        Plantillas Rápidas
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Aplica horarios predefinidos y personalízalos después</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <button type="button" 
                        @click="applyTemplate('workweek')" 
                        class="flex items-center gap-3 p-4 bg-white border-2 border-blue-300 rounded-lg hover:border-blue-500 hover:shadow-md transition-all group">
                    <div class="bg-blue-100 rounded-full p-3 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-briefcase text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <div class="font-bold text-gray-900">Lun-Vie (9-18h)</div>
                        <div class="text-xs text-gray-600">Horario de oficina estándar</div>
                    </div>
                </button>

                <button type="button" 
                        @click="applyTemplate('morning')" 
                        class="flex items-center gap-3 p-4 bg-white border-2 border-orange-300 rounded-lg hover:border-orange-500 hover:shadow-md transition-all group">
                    <div class="bg-orange-100 rounded-full p-3 group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-sun text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <div class="font-bold text-gray-900">Mañanas (7-15h)</div>
                        <div class="text-xs text-gray-600">Turno matutino</div>
                    </div>
                </button>

                <button type="button" 
                        @click="applyTemplate('night')" 
                        class="flex items-center gap-3 p-4 bg-white border-2 border-indigo-300 rounded-lg hover:border-indigo-500 hover:shadow-md transition-all group">
                    <div class="bg-indigo-100 rounded-full p-3 group-hover:bg-indigo-200 transition-colors">
                        <i class="fas fa-moon text-indigo-600 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <div class="font-bold text-gray-900">Nocturno (21-06h)</div>
                        <div class="text-xs text-gray-600">Cruza medianoche</div>
                    </div>
                </button>

                <button type="button" 
                        @click="applyTemplate('split')" 
                        class="flex items-center gap-3 p-4 bg-white border-2 border-purple-300 rounded-lg hover:border-purple-500 hover:shadow-md transition-all group">
                    <div class="bg-purple-100 rounded-full p-3 group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-left">
                        <div class="font-bold text-gray-900">Partido (9-14, 16-19)</div>
                        <div class="text-xs text-gray-600">Con descanso a mediodía</div>
                    </div>
                </button>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <button type="button" 
                        @click="copyToAll()" 
                        class="text-sm px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-2">
                    <i class="fas fa-copy"></i>
                    Copiar Lunes a Todos
                </button>
                <button type="button" 
                        @click="clearAll()" 
                        class="text-sm px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 flex items-center gap-2">
                    <i class="fas fa-trash-alt"></i>
                    Limpiar Todo
                </button>
            </div>
        </div>

        <!-- Bloques por día -->
        <div class="grid grid-cols-1 gap-6">
            <template x-for="day in days" :key="day.num">
                <div class="bg-white rounded-xl shadow-md border-2 transition-all"
                     :class="hasBlocks(day.num) ? 'border-primary-300' : 'border-gray-200'">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 rounded-t-xl border-b-2"
                         :class="hasBlocks(day.num) ? 'border-primary-200' : 'border-gray-200'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-white rounded-full p-3 shadow-sm">
                                    <i class="fas fa-calendar-day text-primary-600 text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900" x-text="day.name"></h3>
                                    <div x-show="hasBlocks(day.num)" class="text-sm text-gray-600 flex items-center gap-2">
                                        <i class="fas fa-clock text-primary-500"></i>
                                        <span x-text="dayTotal(day.num)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="addBlock(day.num)" 
                                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:from-green-600 hover:to-emerald-600 shadow-md hover:shadow-lg flex items-center gap-2 transform hover:scale-105 transition-all">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Agregar Bloque</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-3">
                            <template x-for="(block, idx) in blocks[day.num]" :key="idx">
                                <div class="flex items-center gap-3 p-4 rounded-lg border-2 transition-all"
                                     :class="block.type === 'work' ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200'">
                                    <div class="flex items-center gap-2">
                                        <div class="relative">
                                            <select x-model="block.type" 
                                                    class="appearance-none px-4 py-2 pr-10 rounded-lg border-2 font-semibold text-sm cursor-pointer transition-all"
                                                    :class="block.type === 'work' ? 'bg-blue-100 border-blue-300 text-blue-800' : 'bg-orange-100 border-orange-300 text-orange-800'">
                                                <option value="work">💼 Trabajo</option>
                                                <option value="break">☕ Descanso</option>
                                            </select>
                                            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-500 text-xs"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 flex-1">
                                        <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border-2 border-gray-300">
                                            <i class="fas fa-clock text-gray-400 text-sm"></i>
                                            <input type="time" 
                                                   x-model="block.start" 
                                                   @change="checkMidnightCross(block)"
                                                   class="border-0 p-0 focus:ring-0 w-24 font-mono font-semibold">
                                        </div>
                                        
                                        <i class="fas fa-arrow-right text-gray-400" :class="block.spansNext ? 'text-purple-600' : ''"></i>
                                        
                                        <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border-2 border-gray-300">
                                            <i class="fas fa-clock text-gray-400 text-sm"></i>
                                            <input type="time" 
                                                   x-model="block.end" 
                                                   @change="checkMidnightCross(block)"
                                                   class="border-0 p-0 focus:ring-0 w-24 font-mono font-semibold">
                                        </div>
                                        
                                        <!-- Checkbox para turno nocturno -->
                                        <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all"
                                               :class="block.spansNext ? 'bg-purple-100 border-2 border-purple-300' : 'bg-gray-50 border-2 border-gray-200'">
                                            <input type="checkbox" 
                                                   x-model="block.spansNext"
                                                   class="rounded text-purple-600 focus:ring-purple-500">
                                            <span class="text-xs font-semibold whitespace-nowrap"
                                                  :class="block.spansNext ? 'text-purple-800' : 'text-gray-600'">
                                                🌙 Al día siguiente
                                            </span>
                                        </label>
                                    </div>
                                    
                                    <div class="px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-bold text-gray-700 min-w-[110px] text-center"
                                         :class="block.spansNext ? 'border-purple-300 bg-purple-50 text-purple-700' : ''">
                                        <span x-text="duration(block)"></span>
                                        <span x-show="block.spansNext" class="block text-xs text-purple-600">+1 día</span>
                                    </div>
                                    
                                    <button type="button" 
                                            @click="removeBlock(day.num, idx)" 
                                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </template>

                            <div x-show="!blocks[day.num] || blocks[day.num].length === 0" 
                                 class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-calendar-times text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500 font-medium">Sin bloques definidos</p>
                                <p class="text-sm text-gray-400">Haz clic en "Agregar Bloque" para comenzar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Resumen y Botones -->
        <div class="mt-6 sticky bottom-4 bg-white rounded-xl shadow-xl border-2 border-primary-300 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600" x-text="weeklyTotal()"></div>
                        <div class="text-sm text-gray-600">Horas Semanales</div>
                    </div>
                    <div class="h-12 w-px bg-gray-300"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600" x-text="activeDaysCount()"></div>
<script>
function shiftBlocksManager() {
    return {
        days: [
            {num: 1, name: 'Lunes'},
            {num: 2, name: 'Martes'},
            {num: 3, name: 'Miércoles'},
            {num: 4, name: 'Jueves'},
            {num: 5, name: 'Viernes'},
            {num: 6, name: 'Sábado'},
            {num: 7, name: 'Domingo'}
        ],
        blocks: <?= json_encode($blocksGrouped) ?>,

        addBlock(day) {
            if (!this.blocks[day]) this.blocks[day] = [];
            
            // Si hay bloques, usar el último horario como base
            let start = '09:00', end = '13:00', spansNext = false;
            if (this.blocks[day].length > 0) {
                const last = this.blocks[day][this.blocks[day].length - 1];
                start = last.end;
                const endMins = this.timeToMins(start) + 240; // +4 horas
                end = this.minsToTime(endMins);
            }
            
            this.blocks[day].push({
                type: 'work',
                start: start,
                end: end,
                spansNext: spansNext
            });
        },

        removeBlock(day, idx) {
            this.blocks[day].splice(idx, 1);
            if (this.blocks[day].length === 0) {
                delete this.blocks[day];
            }
        },

        checkMidnightCross(block) {
            // Auto-detectar si el horario cruza medianoche
            const start = this.timeToMins(block.start);
            const end = this.timeToMins(block.end);
            if (end < start && !block.spansNext) {
                block.spansNext = true;
            }
        },

        duration(block) {
            const start = this.timeToMins(block.start);
            let end = this.timeToMins(block.end);
            
            // Si cruza medianoche, agregar 24 horas
            if (block.spansNext || end < start) {
                end += 1440; // 24 * 60
            }
            
            const diff = end - start;
            if (diff < 0) return '0h';
            const h = Math.floor(diff / 60);
            const m = diff % 60;
            return m > 0 ? `${h}h ${m}m` : `${h}h`;
        },

        dayTotal(day) {
            if (!this.blocks[day]) return '0h';
            let total = 0;
            this.blocks[day].filter(b => b.type === 'work').forEach(b => {
                const start = this.timeToMins(b.start);
                let end = this.timeToMins(b.end);
                if (b.spansNext || end < start) {
                    end += 1440; // 24 * 60
                }
                total += Math.max(0, end - start);
            });
            const h = Math.floor(total / 60);
            const m = total % 60;
            return m > 0 ? `${h}h ${m}m` : `${h}h`;
        },

        weeklyTotal() {
            let total = 0;
            for (let day = 1; day <= 7; day++) {
                if (this.blocks[day]) {
                    this.blocks[day].filter(b => b.type === 'work').forEach(b => {
                        const start = this.timeToMins(b.start);
                        let end = this.timeToMins(b.end);
                        if (b.spansNext || end < start) {
                            end += 1440; // 24 * 60
                        }
                        total += Math.max(0, end - start);
                    });
                }
            }
            const h = Math.floor(total / 60);
            const m = total % 60;
            return m > 0 ? `${h}h ${m}m` : `${h}h`;
        },

        activeDaysCount() {
            return Object.keys(this.blocks).filter(day => this.blocks[day] && this.blocks[day].length > 0).length;
        },

        hasBlocks(day) {
            return this.blocks[day] && this.blocks[day].length > 0;
        },

        timeToMins(time) {
            if (!time) return 0;
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        },

        minsToTime(mins) {
            const h = Math.floor(mins / 60) % 24;
            const m = mins % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
        },

        applyTemplate(template) {
            this.clearAll();
            
            if (template === 'workweek') {
                // Lun-Vie 9-18h
                for (let i = 1; i <= 5; i++) {
                    this.blocks[i] = [{type: 'work', start: '09:00', end: '18:00', spansNext: false}];
                }
            } else if (template === 'morning') {
                // Lun-Vie 7-15h
                for (let i = 1; i <= 5; i++) {
                    this.blocks[i] = [{type: 'work', start: '07:00', end: '15:00', spansNext: false}];
                }
            } else if (template === 'night') {
                // Lun-Vie turno nocturno 21:00 - 06:00 (cruza medianoche)
                for (let i = 1; i <= 5; i++) {
                    this.blocks[i] = [{type: 'work', start: '21:00', end: '06:00', spansNext: true}];
                }
            } else if (template === 'split') {
                // Lun-Vie partido 9-14, 16-19
                for (let i = 1; i <= 5; i++) {
                    this.blocks[i] = [
                        {type: 'work', start: '09:00', end: '14:00', spansNext: false},
                        {type: 'break', start: '14:00', end: '16:00', spansNext: false},
                        {type: 'work', start: '16:00', end: '19:00', spansNext: false}
                    ];
                }
            }
        },

        copyToAll() {
            if (!this.blocks[1] || this.blocks[1].length === 0) {
                alert('Primero define los bloques del lunes');
                return;
            }
            
            const monday = JSON.parse(JSON.stringify(this.blocks[1]));
            for (let i = 2; i <= 7; i++) {
                this.blocks[i] = JSON.parse(JSON.stringify(monday));
            }
        },

        clearAll() {
            if (Object.keys(this.blocks).length > 0) {
                if (!confirm('¿Eliminar todos los bloques de tiempo?')) return;
            }
            this.blocks = {};
        },

        submitForm() {
            const form = document.querySelector('form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'blocks_data';
            input.value = JSON.stringify(this.blocks);
            form.appendChild(input);
            form.submit();
        }
    }
}
</script                  this.blocks[i] = [{type: 'work', start: '09:00', end: '18:00'}];
                    }
                },

                clearAll() {
                    this.blocks = {};
                },

                submitForm() {
                    const form = document.querySelector('form');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'blocks_data';
                    input.value = JSON.stringify(this.blocks);
                    form.appendChild(input);
                    form.submit();
                }
            }
        }
    </script>
</body>
</html>
