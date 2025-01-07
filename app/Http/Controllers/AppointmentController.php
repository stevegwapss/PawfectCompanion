<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function schedule(Request $request, AdoptionApplication $application)
    {
        try {
            $this->validateScheduleRequest($request);
            $appointmentDate = $this->parseAndValidateDate($request->appointment_date);
            
            if ($this->isDateTaken($appointmentDate, $application->id)) {
                return $this->jsonError('This date is already taken', 400);
            }

            if ($request->is_rescheduling) {
                $appointment = $this->rescheduleAppointment($application, $appointmentDate);
            } else {
                $appointment = $this->createNewAppointment($application, $appointmentDate);
            }

            return response()->json([
                'success' => true,
                'appointment' => $appointment->load('adoptionApplication')
            ]);

        } catch (\Exception $e) {
            Log::error('Appointment scheduling error: ' . $e->getMessage());
            return $this->jsonError('Failed to schedule appointment: ' . $e->getMessage(), 500);
        }
    }

    public function cancel(Request $request, AdoptionApplication $application)
    {
        try {
            $appointment = $application->appointment;
            
            if (!$appointment) {
                return $this->jsonError('No appointment found', 404);
            }

            if ($appointment->appointment_status === 'approved') {
                return $this->jsonError('Cannot cancel an approved appointment', 400);
            }

            $appointment->update(['appointment_status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'appointment' => $appointment->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Appointment cancellation error: ' . $e->getMessage());
            return $this->jsonError('Failed to cancel appointment', 500);
        }
    }

    public function getAvailableSlots(Request $request)
    {
        try {
            $date = Carbon::parse($request->date)->setTimezone('Asia/Manila');
            
            if ($date->isWeekend()) {
                return $this->jsonError('No appointments available on weekends', 400);
            }

            $existingDates = $this->getExistingAppointmentDates();

            return response()->json([
                'success' => true,
                'existingDates' => $existingDates
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching available slots: ' . $e->getMessage());
            return $this->jsonError('Failed to fetch available dates', 500);
        }
    }

    private function validateScheduleRequest(Request $request)
    {
        return $request->validate([
            'appointment_date' => 'required|date',
            'is_rescheduling' => 'boolean'
        ]);
    }

    private function parseAndValidateDate($date)
    {
        $appointmentDate = Carbon::parse($date)->setTimezone('Asia/Manila');
        
        // Validate business hours (9 AM - 4 PM)
        $hour = $appointmentDate->hour;
        if ($hour < 9 || $hour >= 16) {
            throw new \Exception('Appointments must be scheduled between 9 AM and 4 PM');
        }

        // Validate weekends
        if ($appointmentDate->isWeekend()) {
            throw new \Exception('Appointments cannot be scheduled on weekends');
        }

        return $appointmentDate;
    }

    private function isDateTaken($date, $excludeApplicationId = null)
    {
        $query = Appointment::whereDate('appointment_date', $date->toDateString())
            ->where('appointment_status', '!=', 'disapproved')
            ->where('appointment_status', '!=', 'cancelled');

        if ($excludeApplicationId) {
            $query->whereHas('adoptionApplication', function ($q) use ($excludeApplicationId) {
                $q->where('id', '!=', $excludeApplicationId);
            });
        }

        return $query->exists();
    }

    private function rescheduleAppointment(AdoptionApplication $application, Carbon $appointmentDate)
    {
        $appointment = $application->appointment;
        
        if (!$appointment) {
            throw new \Exception('No existing appointment found for rescheduling');
        }

        $appointment->update([
            'appointment_date' => $appointmentDate,
            'appointment_status' => 'pending'
        ]);

        return $appointment;
    }

    private function createNewAppointment(AdoptionApplication $application, Carbon $appointmentDate)
    {
        if ($application->appointment()->exists()) {
            throw new \Exception('An appointment already exists for this application');
        }

        return $application->appointment()->create([
            'appointment_date' => $appointmentDate,
            'appointment_status' => 'pending'
        ]);
    }

    private function getExistingAppointmentDates()
    {
        return Appointment::where('appointment_status', 'pending')
            ->orWhere('appointment_status', 'approved')
            ->get()
            ->pluck('appointment_date')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values();
    }

    private function jsonError($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    public function adminCancel(Appointment $appointment)
{
    try {
        if ($appointment->appointment_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved appointments can be cancelled'
            ], 400);
        }

        $appointment->update(['appointment_status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'appointment' => $appointment->fresh()
        ]);

    } catch (\Exception $e) {
        Log::error('Admin appointment cancellation error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to cancel appointment'
        ], 500);
    }
}
}