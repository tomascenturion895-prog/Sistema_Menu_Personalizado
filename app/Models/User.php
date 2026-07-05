<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'apellido', 'email', 'telefono', 'fecha_nacimiento', 'terminos_aceptados_en', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fecha_nacimiento' => 'date',
            'terminos_aceptados_en' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * Nombre y apellido juntos, para mostrar en navbar/perfil sin concatenar
     * a mano en cada vista.
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->name} {$this->apellido}"));
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }
}
