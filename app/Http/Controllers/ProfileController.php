<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\WorkArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $authUser = $request->user()->fresh();

        return view('profile.edit', [
            'user' => $authUser,
            'workAreas' => $authUser->esAdmin()
                ? WorkArea::allOrderedForProfile()
                : collect(),
        ]);
    }

    /**
     * Estado para la tarjeta "Asistencia de contraseñas" (listado de ejecutivos u otras vistas admin).
     *
     * @return array{managedUser: ?User, userSearch: string, managedUsersSuggestions: \Illuminate\Support\Collection<int, User>}
     */
    public static function adminPasswordAssistanceState(Request $request): array
    {
        $authUser = $request->user();
        $managedUser = null;
        $managedUsersSuggestions = collect();
        $userSearch = trim((string) $request->query('user_search', ''));

        if ($authUser && $authUser->esAdmin()) {
            $managedUsersSuggestions = User::query()
                ->where('id', '!=', $authUser->id)
                ->orderBy('name')
                ->limit(200)
                ->get(['name', 'email']);

            if ($userSearch !== '') {
                $managedUser = User::query()
                    ->where('id', '!=', $authUser->id)
                    ->where(function ($q) use ($userSearch): void {
                        $q->where('name', 'like', '%'.$userSearch.'%')
                            ->orWhere('email', 'like', '%'.$userSearch.'%');
                    })
                    ->orderBy('name')
                    ->first();
            } elseif ($request->session()->has('managed_user_id')) {
                $managedUser = User::find($request->session()->get('managed_user_id'));
            }
        }

        return [
            'managedUser' => $managedUser,
            'userSearch' => $userSearch,
            'managedUsersSuggestions' => $managedUsersSuggestions,
        ];
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $path;
        }

        $user->save();
        $user->refresh();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Remove the user's profile photo from storage and clear the path.
     */
    public function destroyProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-photo-removed');
    }

    /**
     * Admin: reset password for a user and reveal temporary password once.
     */
    public function adminResetUserPassword(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->esAdmin(), 403);

        $validated = $request->validate([
            'managed_user_id' => ['required', 'integer', 'exists:users,id'],
            'new_password' => ['nullable', 'string', 'min:8', 'max:64'],
        ], [
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no puede superar 64 caracteres.',
        ]);

        $managedUser = User::findOrFail((int) $validated['managed_user_id']);

        abort_if($managedUser->id === $request->user()->id, 422, 'No puedes restablecer tu propia contraseña desde este panel.');

        $plainPassword = $validated['new_password'] ?: Str::password(12, true, true, false, false);

        $managedUser->forceFill([
            'password' => Hash::make($plainPassword),
        ])->save();

        return Redirect::route('executives.index')
            ->with('success', 'Contraseña restablecida correctamente.')
            ->with('managed_user_id', $managedUser->id)
            ->with('admin_generated_password', $plainPassword)
            ->with('admin_password_user_id', $managedUser->id);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
