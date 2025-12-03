<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'evolution_api_url' => Setting::get('evolution_api_url', ''),
            'evolution_api_token' => Setting::get('evolution_api_token', ''),
            'evolution_instance_name' => Setting::get('evolution_instance_name', ''),
            'n8n_webhook_url' => Setting::get('n8n_webhook_url', ''),
            'webhook_url' => url('/api/webhook/evolution'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'evolution_api_url' => 'required|url',
            'evolution_api_token' => 'required|string',
            'evolution_instance_name' => 'required|string',
            'n8n_webhook_url' => 'nullable|url',
        ]);

        Setting::set('evolution_api_url', $request->evolution_api_url);
        Setting::set('evolution_api_token', $request->evolution_api_token);
        Setting::set('evolution_instance_name', $request->evolution_instance_name);
        Setting::set('n8n_webhook_url', $request->n8n_webhook_url);

        return back()->with('success', 'Configurações salvas com sucesso!');
    }

    public function testConnection()
    {
        $url = Setting::get('evolution_api_url');
        $token = Setting::get('evolution_api_token');
        $instance = Setting::get('evolution_instance_name');

        if (!$url || !$token || !$instance) {
            return back()->with('error', 'Configure todas as credenciais primeiro.');
        }

        try {
            $response = \Http::withHeaders([
                'apikey' => $token,
            ])->get("{$url}/instance/connectionState/{$instance}");

            if ($response->successful()) {
                return back()->with('success', 'Conexão testada com sucesso! Status: ' . $response->json('state', 'connected'));
            } else {
                return back()->with('error', 'Erro ao conectar: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao testar conexão: ' . $e->getMessage());
        }
    }
}
