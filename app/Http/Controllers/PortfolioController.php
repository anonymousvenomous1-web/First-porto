<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Display the portfolio home page.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $categories = [
            'all' => 'All Work ('.$projects->count().')',
            'full-stack' => 'Full-Stack ('.$projects->where('category', 'full-stack')->count().')',
            'backend-api' => 'Backend & API ('.$projects->where('category', 'backend-api')->count().')',
            'dev-tool' => 'Developer Tools ('.$projects->where('category', 'dev-tool')->count().')',
        ];

        return view('portfolio', [
            'projects' => $projects,
            'categories' => $categories,
        ]);
    }

    /**
     * Return JSON data for an individual project modal or inspection.
     */
    public function show(Project $project): JsonResponse
    {
        return response()->json([
            'project' => $project,
        ]);
    }

    /**
     * Store an incoming contact message.
     */
    public function contact(StoreContactMessageRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        unset($validated['company_hp']);

        $message = ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        try {
            $adminEmail = config('mail.admin_address', 'gamergoblin889@gmail.com');
            Mail::to($adminEmail)->send(new ContactMessageReceived($message));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch contact message email: '.$e->getMessage());
        }

        $successText = 'Terima kasih telah menghubungi, '.$message->name.'! Pesan Anda sudah saya terima dan akan saya balas ke '.$message->email.' secepatnya.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successText,
            ], 201);
        }

        return redirect()
            ->to(url()->previous().'#contact')
            ->with('success', $successText);
    }
}
