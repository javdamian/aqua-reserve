<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reserva de Carriles en Piscina') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    <span class="font-medium">¡Éxito!</span> {{ session('success') }}
                </div>
            @endif

            {{-- Mensajes de error --}}
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Formulario para crear reserva --}}
                <div class="p-6 bg-white shadow sm:rounded-lg md:col-span-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nueva Reserva</h3>

                    <form action="{{ route('reservations.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="reservation_date" :value="__('Fecha')" />
                            <x-text-input id="reservation_date" name="reservation_date" type="date" class="mt-1 block w-full" min="{{ date('Y-m-d') }}" value="{{ old('reservation_date', date('Y-m-d')) }}" required />
                        </div>

                        <div>
                            <x-input-label for="lane_id" :value="__('Carril / Área')" />
                            <select id="lane_id" name="lane_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecciona un carril</option>
                                @foreach ($lanes as $lane)
                                    <option value="{{ $lane->id }}">{{ $lane->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="schedule_id" :value="__('Horario')" />
                            <select id="schedule_id" name="schedule_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Selecciona un horario</option>
                                @foreach ($schedules as $schedule)
                                    <option value="{{ $schedule->id }}">
                                        {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }} (Max: {{ $schedule->max_capacity }} pers.)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <x-primary-button class="w-full justify-center">
                            {{ __('Reservar Turno') }}
                        </x-primary-button>
                    </form>
                </div>

                {{-- Tabla de Mis Reservas --}}
                <div class="p-6 bg-white shadow sm:rounded-lg md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Reservas Guardadas</h3>

                    @if ($myReservations->isEmpty())
                        <p class="text-gray-500 text-sm">Aún no tienes reservas registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Carril</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($myReservations as $reservation)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">{{ $reservation->reservation_date }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $reservation->lane->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                {{ substr($reservation->schedule->start_time, 0, 5) }} - {{ substr($reservation->schedule->end_time, 0, 5) }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if ($reservation->status === 'confirmed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Confirmada
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Cancelada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if ($reservation->status === 'confirmed')
                                                    <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>