<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * 認証後にリダイレクトする先のパスをここに指定します。
     *
     * @var string
     */
    protected $redirectTo = '/address/form';

    //
}