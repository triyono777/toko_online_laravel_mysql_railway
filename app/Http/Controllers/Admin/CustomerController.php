<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Schema::hasTable('users')
            ? User::query()->where('role', 'customer')->latest()->get()
            : collect();

        return view('admin.customers.index', compact('customers'));
    }
}
