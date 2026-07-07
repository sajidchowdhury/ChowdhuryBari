<?php

namespace App\Http\Controllers;

use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Show the Get In Touch edit form (singleton).
     */
    public function edit()
    {
        $contact = ContactInfo::cached();
        return view('admin.contact.edit', compact('contact'));
    }

    /**
     * Update the Get In Touch content.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'address'        => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'email'          => ['nullable', 'email', 'max:255'],
            'whatsapp'       => ['nullable', 'string', 'max:50'],
            'office_hours'   => ['nullable', 'string', 'max:100'],
            'recipient_email' => ['nullable', 'email', 'max:255'],
            'form_active'    => ['nullable', 'boolean'],
        ]);

        $contact = ContactInfo::cached();
        $contact->address        = $validated['address'] ?? null;
        $contact->phone          = $validated['phone'] ?? null;
        $contact->email          = $validated['email'] ?? null;
        $contact->whatsapp       = $validated['whatsapp'] ?? null;
        $contact->office_hours   = $validated['office_hours'] ?? null;
        $contact->recipient_email = $validated['recipient_email'] ?? null;
        $contact->form_active    = $request->boolean('form_active', true);
        $contact->save();

        return redirect()->route('admin.contact.edit')
            ->with('status', 'Get In Touch content updated successfully.');
    }

    /**
     * Handle a public contact-form submission.
     * Sends an email to the admin-configured recipient address.
     */
    public function submit(Request $request)
    {
        $contact = ContactInfo::cached();

        // If the form is disabled, reject submissions.
        if (!$contact->form_active) {
            return back()->with('contact_error', 'বর্তমানে যোগাযোগ ফর্ম নিষ্ক্রিয় আছে।')->withInput();
        }

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $recipient = $contact->recipient;

        // Build a plain-text email body
        $body = "নতুন যোগাযোগ বার্তা — চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা\n\n"
              . "নাম: {$validated['name']}\n"
              . "মোবাইল: {$validated['phone']}\n"
              . "বিষয়: {$validated['subject']}\n\n"
              . "বার্তা:\n{$validated['message']}\n";

        try {
            Mail::raw($body, function ($m) use ($recipient, $validated) {
                $m->to($recipient)
                  ->subject('নতুন যোগাযোগ বার্তা: ' . $validated['subject'])
                  ->replyTo($validated['phone']); // reply-to phone as string fallback
            });
        } catch (\Throwable $e) {
            // Mail may fail if SMTP not configured; still acknowledge to user,
            // but log the issue for the admin.
            report($e);
        }

        return back()->with('contact_success', 'আপনার বার্তা পাঠানো হয়েছে। আমরা শীঘ্রই যোগাযোগ করব।');
    }
}
