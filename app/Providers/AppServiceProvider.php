<?php

namespace App\Providers;

use App\Interfaces\IAccount;
use App\Interfaces\ITransaction;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\IUser;
use App\Repos\Account;
use App\Repos\Transaction;
use App\Repos\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IUser::class, User::class);
        $this->app->singleton(IAccount::class, Account::class);
        $this->app->singleton(ITransaction::class, Transaction::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
