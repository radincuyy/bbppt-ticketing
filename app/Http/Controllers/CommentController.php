<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment on a ticket
     */
    public function store(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Validate request
        $validated = $request->validate([
            'content' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        // Only staff can create internal notes
        $isInternal = false;
        if ($request->has('is_internal') && $user->can('comments.create.internal')) {
            $isInternal = $request->boolean('is_internal');
        }

        // Create comment
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'is_internal' => $isInternal,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/comments', 'public');
                
                Attachment::create([
                    'attachable_type' => Comment::class,
                    'attachable_id' => $comment->id,
                    'uploaded_by' => $user->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }
}
