<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $successText = 'Thank you for reaching out, '.$message->name.'! Your message has been received. I will reply to '.$message->email.' promptly.';

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
