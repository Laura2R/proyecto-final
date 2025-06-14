<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;



class AdminController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('admin'),
        ];
    }

    /**
     * Dashboard principal de administración
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', true)->count();
        $totalCards = Card::count();
        $totalBalance = Card::sum('saldo') / 100; // En euros

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'totalCards', 'totalBalance'));
    }

    /**
     * Mostrar lista de usuarios
     */
    public function users()
    {
        $users = User::with('cards')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Guardar nuevo usuario
     */
    public function storeUser(CreateUserRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualizar usuario existente
     */
    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin'),
        ];

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroyUser(User $user)
    {
        // Prevenir que se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        // Eliminar todas las tarjetas asociadas al usuario
        $user->cards()->delete();

        // Eliminar transacciones asociadas
        $user->transacciones()->delete();

        // Eliminar favoritos
        $user->lineasFavoritas()->detach();
        $user->paradasFavoritas()->detach();

        // Finalmente eliminar el usuario
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Usuario y todos sus datos eliminados correctamente.');
    }

    /**
     * Mostrar tarjetas de un usuario específico
     */
    public function userCards(User $user)
    {
        $cards = $user->cards()->paginate(10);
        return view('admin.users.cards', compact('user', 'cards'));
    }

    /**
     * Mostrar estadísticas detalladas del sistema
     */
    public function statistics()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'admins' => User::where('is_admin', true)->count(),
                'regular' => User::where('is_admin', false)->count(),
                'recent' => User::where('created_at', '>=', now()->subDays(30))->count(),
            ],
            'cards' => [
                'total' => Card::count(),
                'with_balance' => Card::where('saldo', '>', 0)->count(),
                'empty' => Card::where('saldo', '=', 0)->count(),
                'total_balance' => Card::sum('saldo') / 100,
                'average_balance' => Card::avg('saldo') / 100,
            ],
            'transactions' => [
                'total' => \App\Models\Transaccion::count(),
                'this_month' => \App\Models\Transaccion::whereMonth('created_at', now()->month)->count(),
                'total_amount' => \App\Models\Transaccion::sum('monto') / 100,
            ]
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Buscar usuarios
     */
    public function searchUsers(\Illuminate\Http\Request $request)
    {
        $query = $request->get('search');

        $users = User::with('cards')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate(15);

        return view('admin.users.index', compact('users', 'query'));
    }

    /**
     * Cambiar estado de administrador de un usuario
     */
    public function toggleAdmin(User $user)
    {
        // Prevenir que se quite permisos a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'No puedes cambiar tus propios permisos de administrador.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        $status = $user->is_admin ? 'otorgados' : 'revocados';

        return redirect()->route('admin.users')
            ->with('success', "Permisos de administrador {$status} para {$user->name}.");
    }

    /**
     * Exportar lista de usuarios (opcional)
     */
    public function exportUsers()
    {
        $users = User::with('cards')->get();

        $csvData = "ID,Nombre,Email,Es Admin,Tarjetas,Saldo Total,Fecha Registro\n";

        foreach ($users as $user) {
            $totalBalance = $user->cards->sum('saldo') / 100;
            $csvData .= "{$user->id},{$user->name},{$user->email}," .
                ($user->is_admin ? 'Sí' : 'No') . "," .
                "{$user->cards->count()},€{$totalBalance}," .
                "{$user->created_at->format('d/m/Y')}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="usuarios_onubabus.csv"');
    }


    /**
     * Mostrar formulario para editar tarjeta
     */
    public function editUserCard(User $user, Card $card)
    {
        // Verificar que la tarjeta pertenece al usuario
        if ($card->user_id !== $user->id) {
            return redirect()->route('admin.users.cards', $user)
                ->with('error', 'Esta tarjeta no pertenece al usuario seleccionado.');
        }

        return view('admin.users.edit-card', compact('user', 'card'));
    }

    /**
     * Actualizar saldo de tarjeta
     */
    public function updateUserCard(Request $request, User $user, Card $card)
    {
        // Verificar que la tarjeta pertenece al usuario
        if ($card->user_id !== $user->id) {
            return redirect()->route('admin.users.cards', $user)
                ->with('error', 'Esta tarjeta no pertenece al usuario seleccionado.');
        }

        $request->validate([
            'saldo' => 'required|numeric|min:0|max:999.99',
            'motivo' => 'required|string|max:255'
        ]);

        $nuevoSaldo = $request->saldo * 100; // Convertir a centimos
        $saldoAnterior = $card->saldo;

        $card->update(['saldo' => $nuevoSaldo]);

        // Log de la acción
        Log::info('Admin cambió saldo de tarjeta', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'card_id' => $card->id,
            'saldo_anterior' => $saldoAnterior / 100,
            'saldo_nuevo' => $request->saldo,
            'motivo' => $request->motivo
        ]);

        return redirect()->route('admin.users.cards', $user)
            ->with('success', "Saldo actualizado correctamente. Nuevo saldo: €{$request->saldo}");
    }

    /**
     * Eliminar tarjeta específica de un usuario
     */
    public function destroyUserCard(User $user, Card $card)
    {
        // Verificar que la tarjeta pertenece al usuario
        if ($card->user_id !== $user->id) {
            return redirect()->route('admin.users.cards', $user)
                ->with('error', 'Esta tarjeta no pertenece al usuario seleccionado.');
        }

        $numeroTarjeta = $card->numero_tarjeta;
        $saldoEliminado = $card->saldo / 100;

        // Log de la eliminación
        Log::info('Admin eliminó tarjeta', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'card_id' => $card->id,
            'numero_tarjeta' => $numeroTarjeta,
            'saldo_perdido' => $saldoEliminado
        ]);

        $card->delete();

        return redirect()->route('admin.users.cards', $user)
            ->with('success', "Tarjeta #{$numeroTarjeta} eliminada correctamente.");
    }

}
