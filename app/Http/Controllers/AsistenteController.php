<?php

namespace App\Http\Controllers;

use App\Services\GeminiAssistantService;
use Illuminate\Http\Request;

class AsistenteController extends Controller
{
    public function chat(Request $request, GeminiAssistantService $asistente)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'array',
        ]);

        // Convertimos horas habladas (ej: "8 de la tarde") a formato 24h ("20:00")
        $mensajeProcesado = $this->convertirHorasColoquiales($validated['message']);

        $resultado = $asistente->chat($mensajeProcesado, $validated['history'] ?? []);

        return response()->json($resultado);
    }

    /**
     * Detecta expresiones como "8 de la tarde" o "6 de la noche" y las transforma a 24h.
     */
    private function convertirHorasColoquiales(string $texto): string
    {
        return preg_replace_callback('/(?:a las\s+)?(\d{1,2})(?::(\d{2}))?\s*(de la tarde|de la noche|pm)/i', function ($matches) {
            $hora = (int) $matches[1];
            $minutos = $matches[2] ?? '00';
            
            if ($hora < 12) {
                $hora += 12;
            }

            return sprintf('%02d:%s', $hora, $minutos);
        }, $texto);
    }
}