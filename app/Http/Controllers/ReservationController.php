<?php

namespace App\Http\Controllers;

use App\Models\Lane;
use App\Models\Reservation;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Muestra las reservas del usuario autenticado y los datos para el formulario.
     */
    public function index()
    {
        $lanes = Lane::where('is_active', true)->get();
        $schedules = Schedule::orderBy('start_time')->get();
        
        $myReservations = Reservation::with(['lane', 'schedule'])
            ->where('user_id', Auth::id())
            ->orderBy('reservation_date', 'desc')
            ->get();

        return view('reservations.index', compact('lanes', 'schedules', 'myReservations'));
    }

    /**
     * Procesa y valida la creación de una nueva reserva.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lane_id' => 'required|exists:lanes,id',
            'schedule_id' => 'required|exists:schedules,id',
            'reservation_date' => 'required|date|after_or_equal:today',
        ], [
            'reservation_date.after_or_equal' => 'La fecha de la reserva no puede ser en el pasado.',
        ]);

        $userId = Auth::id();
        $laneId = $request->lane_id;
        $scheduleId = $request->schedule_id;
        $date = $request->reservation_date;

        // 1. Validar si el usuario ya tiene reserva en ese mismo horario y fecha
        $userHasReservation = Reservation::where('user_id', $userId)
            ->where('schedule_id', $scheduleId)
            ->where('reservation_date', $date)
            ->where('status', 'confirmed')
            ->exists();

        if ($userHasReservation) {
            return back()->withErrors(['reservation_date' => 'Ya tienes una reserva activa para este bloque de horario en la fecha seleccionada.']);
        }

        // 2. Validar aforo máximo del carril en ese bloque horario
        $schedule = Schedule::findOrFail($scheduleId);
        $currentBookings = Reservation::where('lane_id', $laneId)
            ->where('schedule_id', $scheduleId)
            ->where('reservation_date', $date)
            ->where('status', 'confirmed')
            ->count();

        if ($currentBookings >= $schedule->max_capacity) {
            return back()->withErrors(['schedule_id' => 'El carril seleccionado ya alcanzó el cupo máximo para ese horario.']);
        }

        // 3. Crear la reserva
        Reservation::create([
            'user_id' => $userId,
            'lane_id' => $laneId,
            'schedule_id' => $scheduleId,
            'reservation_date' => $date,
            'status' => 'confirmed',
        ]);

        return redirect()->route('reservations.index')->with('success', '¡Reserva realizada con éxito!');
    }

    /**
     * Permite al usuario cancelar su reserva.
     */
    public function destroy(Reservation $reservation)
    {
        // Verificar que la reserva pertenezca al usuario autenticado
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')->with('success', 'Reserva cancelada correctamente.');
    }
}