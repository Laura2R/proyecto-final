<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Filtrar Paradas por Línea, Municipio y Núcleo') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('paradas.filtro-linea') }}" class="mb-8 flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="linea_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Línea</label>
                            <select id="linea_id" name="linea_id" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                <option value="">-- Selecciona línea --</option>
                                @foreach($lineas as $linea)
                                    <option value="{{ $linea->id_linea }}" {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                                        {{ $linea->codigo }} {{ $linea->nombre  ?? '-'}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="municipio_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Municipio</label>
                            <select id="municipio_id" name="municipio_id" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100"
                                    {{ count($municipios) ? '' : 'disabled' }} onchange="this.form.submit()">
                                <option value="">-- Todos --</option>
                                @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id_municipio }}" {{ request('municipio_id') == $municipio->id_municipio ? 'selected' : '' }}>
                                        {{ $municipio->nombre  ?? '-'}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nucleo_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Núcleo</label>
                            <select id="nucleo_id" name="nucleo_id" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100"
                                    {{ count($nucleos) ? '' : 'disabled' }} onchange="this.form.submit()">
                                <option value="">-- Todos --</option>
                                @foreach($nucleos as $nucleo)
                                    <option value="{{ $nucleo->id_nucleo }}" {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                        {{ $nucleo->nombre ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if(request('linea_id'))
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID Parada</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Municipio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Núcleo</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($paradas as $parada)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $parada->id_parada }}</td>
                                        <td class="px-6 py-4">{{ $parada->nombre  ?? '-'}}</td>
                                        <td class="px-6 py-4">{{ $parada->municipio->nombre ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $parada->nucleo->nombre ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">No hay paradas para estos filtros.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $paradas->links() }}
                        </div>
                    @else
                        <div class="text-gray-600 dark:text-gray-300 mt-8">Selecciona una línea para ver sus paradas.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
